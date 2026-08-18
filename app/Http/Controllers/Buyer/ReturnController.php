<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReturnController extends Controller
{
    // List my returns
    public function index()
    {
        $returns = \App\Models\ReturnRequest::with(['order.items.product'])
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('buyer.returns.index', compact('returns'));
    }

    // Show return request form
    public function create(Request $request)
    {
        $orderId = $request->order_id;
        $order   = Order::where('id', $orderId)
            ->where('user_id', Auth::id())
            ->where('status', 'delivered')
            ->firstOrFail();

        $order->load('items.product');

        // Check if already returned
        $existing = \App\Models\ReturnRequest::where('order_id', $orderId)
            ->where('user_id', Auth::id())
            ->first();

        return view('buyer.returns.create', compact('order', 'existing'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'order_id'    => 'required|exists:orders,id',
            'reason'      => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        $user  = Auth::user();
        $order = Order::where('id', $request->order_id)
            ->where('user_id', $user->id)
            ->where('status', 'delivered')
            ->firstOrFail();

        // Only one return per order
        if (\App\Models\ReturnRequest::where('order_id', $order->id)->where('user_id', $user->id)->exists()) {
            return back()->withErrors(['error' => 'A return request already exists for this order.']);
        }

        \App\Models\ReturnRequest::create([
            'order_id'    => $order->id,
            'user_id'     => $user->id,
            'reason'      => $request->reason,
            'description' => $request->description,
            'status'      => 'requested',
        ]);

        // Notify seller(s)
        foreach ($order->items as $item) {
            Notification::create([
                'user_id' => $item->seller_id,
                'title'   => 'Return Requested',
                'body'    => "Buyer has requested a return for order #{$order->order_number}.",
                'type'    => 'return',
            ]);
        }

        return redirect()->route('returns.index')
            ->with('success', 'Return request submitted. We will update you soon.');
    }
}
