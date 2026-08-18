<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Shipment;
use App\Models\TrackingEvent;
use App\Services\OrderStatusService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    protected OrderStatusService $statusService;

    public function __construct(OrderStatusService $statusService)
    {
        $this->statusService = $statusService;
    }
    public function index(Request $request)
    {
        $status   = $request->status ?? 'all';
        $sellerId = Auth::id();

        $orderItems = OrderItem::with(['order.buyer', 'order.address', 'product.images'])
            ->where('seller_id', $sellerId)
            ->when($status !== 'all', fn($q) => $q->where('status', $status))
            ->latest()
            ->paginate(10);

        $newCount = OrderItem::where('seller_id', $sellerId)->where('status', 'confirmed')->count();

        return view('seller.orders.index', compact('orderItems', 'status', 'newCount'));
    }

    public function show(OrderItem $item)
    {
        \Illuminate\Support\Facades\Gate::authorize('view', $item);
        $item->load(['order.buyer', 'order.address', 'product.images', 'order.payment']);
        $shipment = Shipment::where('order_id', $item->order_id)
            ->where('seller_id', Auth::id())
            ->with('events')
            ->first();
        return view('seller.orders.show', compact('item', 'shipment'));
    }

    public function confirm(OrderItem $item)
    {
        \Illuminate\Support\Facades\Gate::authorize('update', $item);

        try {
            $this->statusService->assertValidTransition($item, OrderStatusService::STATE_CONFIRMED);
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }

        $item->update(['status' => 'confirmed']);
        $this->addTrackingEvent($item, 'confirmed', 'Order confirmed by seller');
        $this->notifyBuyer($item, 'Order Confirmed ✅', "Seller has confirmed your order for {$item->product_name}.");

        return back()->with('success', 'Order confirmed!');
    }

    public function pack(OrderItem $item)
    {
        \Illuminate\Support\Facades\Gate::authorize('update', $item);

        try {
            $this->statusService->assertValidTransition($item, OrderStatusService::STATE_PACKED);
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }

        $item->update(['status' => 'packed']);
        $this->addTrackingEvent($item, 'packed', 'Order packed and ready to ship');
        $this->notifyBuyer($item, 'Order Packed 📦', "Your order for {$item->product_name} has been packed.");

        return back()->with('success', 'Order marked as packed!');
    }

    public function ship(Request $request, OrderItem $item)
    {
        \Illuminate\Support\Facades\Gate::authorize('update', $item);

        try {
            $this->statusService->assertValidTransition($item, OrderStatusService::STATE_SHIPPED);
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }

        $request->validate([
            'courier_name' => 'nullable|string|max:100',
            'tracking_id'  => 'nullable|string|max:100',
        ]);

        $item->update(['status' => 'shipped']);

        $shipment = Shipment::where('order_id', $item->order_id)->where('seller_id', Auth::id())->first();
        if ($shipment) {
            $shipment->update([
                'status'       => 'shipped',
                'courier_name' => $request->courier_name,
                'tracking_id'  => $request->tracking_id,
            ]);
            TrackingEvent::create([
                'shipment_id' => $shipment->id,
                'status'      => 'shipped',
                'description' => 'Order shipped via ' . ($request->courier_name ?? 'courier'),
                'event_at'    => now(),
            ]);
        }

        $this->updateOrderStatus($item->order_id);
        $courier = $request->courier_name ? " via {$request->courier_name}" : '';
        $this->notifyBuyer($item, 'Order Shipped 🚚', "Your order for {$item->product_name} has been shipped{$courier}." . ($request->tracking_id ? " Tracking: {$request->tracking_id}" : ''));

        return back()->with('success', 'Order marked as shipped!');
    }

    public function outForDelivery(OrderItem $item)
    {
        \Illuminate\Support\Facades\Gate::authorize('update', $item);

        try {
            $this->statusService->assertValidTransition($item, OrderStatusService::STATE_OUT_FOR_DELIVERY);
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }

        $item->update(['status' => 'out_for_delivery']);
        $this->addTrackingEvent($item, 'out_for_delivery', 'Order is out for delivery');
        $this->updateOrderStatus($item->order_id);
        $this->notifyBuyer($item, 'Out for Delivery 🛵', "Your order for {$item->product_name} is out for delivery!");

        return back()->with('success', 'Marked as out for delivery!');
    }

    public function deliver(OrderItem $item)
    {
        \Illuminate\Support\Facades\Gate::authorize('update', $item);

        try {
            $this->statusService->assertValidTransition($item, OrderStatusService::STATE_DELIVERED);
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }

        $item->update(['status' => 'delivered']);
        $this->addTrackingEvent($item, 'delivered', 'Order delivered successfully');
        $this->updateOrderStatus($item->order_id);
        $this->notifyBuyer($item, 'Order Delivered 🏠', "Your order for {$item->product_name} has been delivered. Please confirm receipt.");

        return back()->with('success', 'Order marked as delivered!');
    }

    private function addTrackingEvent(OrderItem $item, string $status, string $description): void
    {
        $shipment = Shipment::where('order_id', $item->order_id)->where('seller_id', $item->seller_id)->first();
        if ($shipment) {
            $shipment->update(['status' => $status]);
            TrackingEvent::create([
                'shipment_id' => $shipment->id,
                'status'      => $status,
                'description' => $description,
                'event_at'    => now(),
            ]);
        }
    }

    private function notifyBuyer(OrderItem $item, string $title, string $body): void
    {
        Notification::create([
            'user_id' => $item->order->user_id,
            'title'   => $title,
            'body'    => $body,
            'type'    => 'order',
            'data'    => json_encode(['order_id' => $item->order_id]),
        ]);
    }

    private function updateOrderStatus(int $orderId): void
    {
        $order    = Order::find($orderId);
        $statuses = $order->items->pluck('status')->unique()->values()->toArray();

        if (count($statuses) === 1) {
            $status = $statuses[0];
            $updates = ['status' => $status];
            if ($status === 'delivered') {
                $updates['delivered_at'] = now();
            }
            $order->update($updates);
        } elseif (in_array('shipped', $statuses) || in_array('out_for_delivery', $statuses)) {
            $order->update(['status' => 'shipped']);
        }
    }
}
