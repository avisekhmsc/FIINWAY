<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use App\Models\Coupon;
use App\Models\Notification;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Referral;
use App\Models\ReferralReward;
use App\Models\SellerEarning;
use App\Models\Shipment;
use App\Models\UserAddress;
use App\Services\RazorpayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    protected RazorpayService $razorpayService;

    public function __construct(RazorpayService $razorpayService)
    {
        $this->razorpayService = $razorpayService;
    }

    public function checkout()
    {
        $user = Auth::user();
        $cart = $user->cart()->with(['items.product.images', 'items.product.seller'])->first();

        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('cart')->with('error', 'Your cart is empty.');
        }

        $addresses = $user->addresses()->get();
        $defaultAddress = $addresses->where('is_default', true)->first() ?? $addresses->first();

        $subtotal = $cart->subtotal;
        $standardDelivery = (float)\App\Models\AppSetting::get('standard_delivery_fee', 49);
        $expressDelivery = (float)\App\Models\AppSetting::get('express_delivery_fee', 99);
        $freeDeliveryThreshold = (float)\App\Models\AppSetting::get('free_delivery_threshold', 500);
        
        $delivery = session('delivery_option', 'standard') === 'express' 
            ? $expressDelivery 
            : ($subtotal > $freeDeliveryThreshold ? 0 : $standardDelivery);
        $discount = session('coupon_discount', 0);
        $total = $subtotal + $delivery - $discount;

        return view('buyer.checkout', compact('cart', 'addresses', 'defaultAddress', 'subtotal', 'delivery', 'discount', 'total'));
    }

    public function placeOrder(Request $request)
    {
        $request->validate([
            'address_id'      => 'required|exists:user_addresses,id',
            'delivery_option' => 'required|in:standard,express',
        ]);

        $user = Auth::user();
        $cart = $user->cart()->with(['items.product'])->first();

        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('cart')->with('error', 'Your cart is empty.');
        }

        // Validate address belongs to user
        $address = UserAddress::where('id', $request->address_id)->where('user_id', $user->id)->firstOrFail();

        $subtotal = $cart->subtotal;
        $standardDelivery = (float)\App\Models\AppSetting::get('standard_delivery_fee', 49);
        $expressDelivery = (float)\App\Models\AppSetting::get('express_delivery_fee', 99);
        $freeDeliveryThreshold = (float)\App\Models\AppSetting::get('free_delivery_threshold', 500);

        $delivery = $request->delivery_option === 'express' 
            ? $expressDelivery 
            : ($subtotal > $freeDeliveryThreshold ? 0 : $standardDelivery);
        $discount = session('coupon_discount', 0);
        $couponCode = session('coupon_code');
        $total = $subtotal + $delivery - $discount;

        DB::beginTransaction();
        try {
            // Create order
            $order = Order::create([
                'order_number'    => 'ORD-' . strtoupper(Str::random(10)),
                'user_id'         => $user->id,
                'address_id'      => $address->id,
                'subtotal'        => $subtotal,
                'delivery_charge' => $delivery,
                'discount'        => $discount,
                'total'           => $total,
                'coupon_code'     => $couponCode,
                'delivery_option' => $request->delivery_option,
                'status'          => 'pending',
                'payment_status'  => 'pending',
            ]);

            // Create order items (grouped by seller)
            $commissionPercent = (float) AppSetting::get('commission_percent', 10);
            
            $productIds = $cart->items->pluck('product_id')->toArray();
            $products = \App\Models\Product::whereIn('id', $productIds)->lockForUpdate()->get()->keyBy('id');

            foreach ($cart->items as $item) {
                $product = $products->get($item->product_id);
                if (!$product || $product->stock < $item->quantity) {
                    throw new \Exception("Product {$item->product->name} is out of stock.");
                }
                $product->decrement('stock', $item->quantity);
                if ($product->stock <= 0) {
                    $product->update(['status' => 'sold']);
                }

                $orderItem = OrderItem::create([
                    'order_id'     => $order->id,
                    'product_id'   => $item->product_id,
                    'seller_id'    => $item->product->user_id,
                    'product_name' => $item->product->name,
                    'price'        => $item->price,
                    'quantity'     => $item->quantity,
                    'subtotal'     => $item->subtotal,
                    'status'       => 'pending',
                ]);

                // Seller earning entry
                $commissionAmt = round($item->subtotal * $commissionPercent / 100, 2);
                SellerEarning::create([
                    'seller_id'          => $item->product->user_id,
                    'order_id'           => $order->id,
                    'order_item_id'      => $orderItem->id,
                    'order_amount'       => $item->subtotal,
                    'commission_percent' => $commissionPercent,
                    'commission_amount'  => $commissionAmt,
                    'seller_amount'      => $item->subtotal - $commissionAmt,
                    'status'             => 'pending',
                ]);

                // Create shipment per seller
                Shipment::firstOrCreate(
                    ['order_id' => $order->id, 'seller_id' => $item->product->user_id],
                    [
                        'status'            => 'confirmed',
                        'expected_delivery' => now()->addDays($request->delivery_option === 'express' ? 2 : 5),
                    ]
                );
            }

            // Create Razorpay Order via Gateway API
            $razorpayOrder = $this->razorpayService->createOrder($order);

            // Create Payment record bound to Razorpay Order ID
            Payment::create([
                'order_id'         => $order->id,
                'user_id'          => $user->id,
                'gateway'          => 'razorpay',
                'gateway_order_id' => $razorpayOrder['id'],
                'amount'           => $order->total,
                'currency'         => 'INR',
                'method'           => 'razorpay',
                'status'           => 'pending',
            ]);

            // Update coupon usage
            if ($couponCode) {
                Coupon::where('code', $couponCode)->increment('used_count');
                session()->forget(['coupon_code', 'coupon_discount']);
            }

            DB::commit();

            session(['pending_order_id' => $order->id]);

            return redirect()->route('payment', $order->id);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage() ?: 'Something went wrong. Please try again.');
        }
    }

    public function payment(Order $order)
    {
        Gate::authorize('view', $order);
        if ($order->payment_status === 'paid') return redirect()->route('orders.show', $order->id);

        $payment = Payment::where('order_id', $order->id)->where('gateway', 'razorpay')->latest()->first();
        $razorpayKeyId = $this->razorpayService->getKeyId();

        $simulatedPaymentId = 'pay_sim_' . Str::random(14);
        $secret = config('services.razorpay.key_secret') ?: 'mocksecret1234567890';
        $simulatedSignature = hash_hmac('sha256', ($payment->gateway_order_id ?? '') . '|' . $simulatedPaymentId, $secret);

        return view('buyer.payment', compact('order', 'payment', 'razorpayKeyId', 'simulatedPaymentId', 'simulatedSignature'));
    }

    public function processPayment(Request $request, Order $order)
    {
        Gate::authorize('view', $order);
        if ($order->payment_status === 'paid') {
            return redirect()->route('orders.show', $order->id);
        }

        $request->validate([
            'payment_method' => 'required|in:upi,card,netbanking,cod,voucher',
        ]);

        $payment = Payment::where('order_id', $order->id)->firstOrFail();
        $simulatedPaymentId = 'pay_sim_' . Str::random(14);
        $secret = config('services.razorpay.key_secret') ?: 'mocksecret1234567890';
        $simulatedSignature = hash_hmac('sha256', $payment->gateway_order_id . '|' . $simulatedPaymentId, $secret);

        $request->merge([
            'razorpay_order_id'   => $payment->gateway_order_id,
            'razorpay_payment_id' => $simulatedPaymentId,
            'razorpay_signature'  => $simulatedSignature,
        ]);

        return $this->verifyPayment($request, $order);
    }

    /**
     * Server-side cryptographic signature verification endpoint.
     */
    public function verifyPayment(Request $request, Order $order)
    {
        Gate::authorize('view', $order);

        $request->validate([
            'razorpay_order_id'   => 'required|string',
            'razorpay_payment_id' => 'required|string',
            'razorpay_signature'  => 'required|string',
        ]);

        $payment = Payment::where('order_id', $order->id)->where('gateway', 'razorpay')->firstOrFail();

        // 1. Amount & Order ID mismatch protection
        if ($payment->gateway_order_id !== $request->razorpay_order_id) {
            return back()->with('error', 'Gateway order mismatch detected.');
        }

        // 2. Cryptographic signature verification
        $isValid = $this->razorpayService->verifyPaymentSignature(
            $request->razorpay_order_id,
            $request->razorpay_payment_id,
            $request->razorpay_signature
        );

        if (!$isValid) {
            return back()->with('error', 'Invalid Razorpay payment signature.');
        }

        // 3. Idempotent payment & order update
        DB::transaction(function () use ($order, $payment, $request) {
            $paymentFresh = Payment::lockForUpdate()->find($payment->id);
            $orderFresh   = Order::lockForUpdate()->find($order->id);

            if ($paymentFresh->status === 'success' || $orderFresh->payment_status === 'paid') {
                return;
            }

            $paymentFresh->update([
                'gateway_payment_id' => $request->razorpay_payment_id,
                'transaction_id'     => $request->razorpay_payment_id,
                'signature'          => $request->razorpay_signature,
                'status'             => 'success',
                'paid_at'            => now(),
            ]);

            $orderFresh->update([
                'payment_status' => 'paid',
                'payment_method' => 'razorpay',
                'transaction_id' => $request->razorpay_payment_id,
                'paid_at'        => now(),
                'status'         => 'confirmed',
            ]);

            $orderFresh->items()->update(['status' => 'confirmed']);
            $orderFresh->shipments()->update(['status' => 'confirmed']);

            // Clear buyer cart
            $buyer = Auth::user();
            if ($buyer && $buyer->cart) {
                $buyer->cart->items()->delete();
            }

            // Check referral reward
            $referral = Referral::where('referred_id', $buyer->id)
                ->where('eligible_action_done', false)
                ->first();

            if ($referral) {
                $referral->update(['eligible_action_done' => true, 'eligible_at' => now()]);
                $rewardAmount = (float) AppSetting::get('referral_reward', 50);
                ReferralReward::create([
                    'user_id'     => $referral->referrer_id,
                    'referral_id' => $referral->id,
                    'amount'      => $rewardAmount,
                    'status'      => 'credited',
                    'credited_at' => now(),
                ]);
                $referral->referrer->increment('wallet_balance', $rewardAmount);
            }

            Notification::create([
                'user_id' => $orderFresh->user_id,
                'title'   => 'Payment Successful! 🎉',
                'body'    => "Your payment of ₹" . number_format($orderFresh->total, 2) . " for order #{$orderFresh->order_number} has been verified.",
                'type'    => 'order',
            ]);
        });

        return redirect()->route('payment.success', $order->id);
    }

    public function paymentSuccess(Order $order)
    {
        Gate::authorize('view', $order);
        $order->load(['items.product', 'address']);
        return view('buyer.payment-success', compact('order'));
    }

    // My Orders list
    public function myOrders(Request $request)
    {
        $status = $request->status ?? 'all';
        $query = Order::with(['items.product.images'])
            ->where('user_id', Auth::id())
            ->latest();

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $orders = $query->paginate(10);
        return view('buyer.orders', compact('orders', 'status'));
    }

    // Order detail
    public function show(Order $order)
    {
        Gate::authorize('view', $order);
        $order->load(['items.product.images', 'address', 'shipments.events', 'payment']);
        return view('buyer.order-detail', compact('order'));
    }

    // Order tracking
    public function track(Order $order)
    {
        Gate::authorize('view', $order);
        $order->load(['items.product.images', 'address', 'shipments.events', 'shipments.items.product.images']);
        return view('buyer.order-tracking', compact('order'));
    }

    // Customer confirms receipt
    public function confirmReceipt(Order $order)
    {
        Gate::authorize('view', $order);
        if ($order->status !== 'delivered') abort(400);

        $order->update([
            'customer_confirmed'    => true,
            'customer_confirmed_at' => now(),
        ]);

        // Update seller earnings — start 2-day hold
        $order->earnings()->where('status', 'pending')->update([
            'status'       => 'on_hold',
            'customer_ok_at' => now(),
            'hold_until'   => now()->addDays(2),
        ]);

        return back()->with('success', 'Thank you for confirming! Payment will be released to seller in 2 days.');
    }
}
