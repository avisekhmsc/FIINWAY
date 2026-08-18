<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use App\Models\Category;
use App\Models\Notification;
use App\Models\Order;
use App\Models\Payout;
use App\Models\Product;
use App\Models\Referral;
use App\Models\ReferralReward;
use App\Models\Refund;
use App\Models\ReturnRequest;
use App\Models\SellerEarning;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'users'            => User::count(),
            'sellers'          => User::where('is_seller', true)->count(),
            'products'         => Product::count(),
            'pending_products' => Product::where('status', 'pending')->count(),
            'orders'           => Order::count(),
            'total_sales'      => Order::where('payment_status', 'paid')->sum('total'),
            'commission'       => SellerEarning::sum('commission_amount'),
            'pending_payouts'  => SellerEarning::whereIn('status', ['on_hold', 'customer_ok'])->sum('seller_amount'),
            'returns'          => ReturnRequest::where('status', 'requested')->count(),
        ];

        $recentOrders    = Order::with(['buyer', 'items'])->latest()->limit(10)->get();
        $pendingProducts = Product::with(['seller', 'category'])->where('status', 'pending')->latest()->limit(5)->get();

        return view('admin.dashboard', compact('stats', 'recentOrders', 'pendingProducts'));
    }

    public function products(Request $request)
    {
        $status = $request->status ?? 'all';
        $query  = Product::with(['seller', 'category', 'images'])->latest();
        if ($status !== 'all') $query->where('status', $status);
        $products = $query->paginate(20);
        return view('admin.products', compact('products', 'status'));
    }

    public function approveProduct(Product $product)
    {
        $product->update(['status' => 'active']);
        Notification::create([
            'user_id' => $product->user_id,
            'title'   => 'Product Approved ✅',
            'body'    => "Your product \"{$product->name}\" has been approved and is now live.",
            'type'    => 'product',
        ]);
        return back()->with('success', 'Product approved!');
    }

    public function rejectProduct(Request $request, Product $product)
    {
        $product->update(['status' => 'rejected', 'reject_reason' => $request->reason]);
        Notification::create([
            'user_id' => $product->user_id,
            'title'   => 'Product Rejected ❌',
            'body'    => "Your product \"{$product->name}\" was rejected. Reason: {$request->reason}",
            'type'    => 'product',
        ]);
        return back()->with('success', 'Product rejected.');
    }

    public function toggleProduct(Product $product)
    {
        $newStatus = $product->status === 'active' ? 'inactive' : 'active';
        $product->update(['status' => $newStatus]);
        return back()->with('success', "Product status changed to $newStatus.");
    }

    public function editProduct(Product $product)
    {
        $categories = Category::where('is_active', true)->get();
        return view('admin.products-edit', compact('product', 'categories'));
    }

    public function updateProduct(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name'             => 'required|string|max:255',
            'category_id'      => 'required|exists:categories,id',
            'selling_price'    => 'required|numeric|min:0',
            'original_price'   => 'nullable|numeric|min:0',
            'stock'            => 'required|integer|min:0',
            'status'           => 'required|in:active,pending,rejected,inactive,sold',
        ]);

        $product->update($validated);
        return redirect()->route('admin.products')->with('success', 'Product updated successfully.');
    }

    public function users(Request $request)
    {
        $filter = $request->filter ?? 'all';
        $query  = User::latest();
        if ($filter === 'sellers') $query->where('is_seller', true);
        if ($filter === 'buyers')  $query->where('is_seller', false);
        if ($filter === 'blocked') $query->where('is_blocked', true);
        $users = $query->paginate(20);
        return view('admin.users', compact('users', 'filter'));
    }

    public function toggleBlock(User $user)
    {
        $user->update(['is_blocked' => !$user->is_blocked]);
        return back()->with('success', $user->is_blocked ? 'User blocked.' : 'User unblocked.');
    }

    public function orders(Request $request)
    {
        $status = $request->status ?? 'all';
        $query  = Order::with(['buyer', 'items'])->latest();
        if ($status !== 'all') $query->where('status', $status);
        $orders = $query->paginate(20);
        return view('admin.orders', compact('orders', 'status'));
    }

    public function payouts(Request $request)
    {
        // Auto-release any eligible hold earnings (idempotent)
        SellerEarning::where('status', 'on_hold')
            ->where('hold_until', '<=', now())
            ->each(function ($earning) {
                $earning->update(['status' => 'released', 'released_at' => now()]);
                $earning->seller->increment('wallet_balance', $earning->seller_amount);
                Payout::create([
                    'seller_id'       => $earning->seller_id,
                    'amount'          => $earning->seller_amount,
                    'status'          => 'done',
                    'processed_at'    => now(),
                    'transaction_ref' => 'AUTO-' . strtoupper(substr(md5($earning->id), 0, 10)),
                ]);
                Notification::create([
                    'user_id' => $earning->seller_id,
                    'title'   => 'Payout Released! 🎉',
                    'body'    => '₹' . number_format($earning->seller_amount, 2) . ' released to your wallet from order #' . $earning->order->order_number,
                    'type'    => 'payout',
                ]);
            });

        $earnings = SellerEarning::with(['seller', 'order'])
            ->whereIn('status', ['on_hold', 'customer_ok', 'pending'])
            ->latest()
            ->paginate(20);

        return view('admin.payouts', compact('earnings'));
    }

    public function releasePayout(SellerEarning $earning)
    {
        if ($earning->status === 'released') {
            return back()->with('error', 'Already released.');
        }
        $earning->update(['status' => 'released', 'released_at' => now()]);
        $earning->seller->increment('wallet_balance', $earning->seller_amount);

        Payout::create([
            'seller_id'       => $earning->seller_id,
            'amount'          => $earning->seller_amount,
            'status'          => 'done',
            'processed_at'    => now(),
            'transaction_ref' => 'ADMIN-' . strtoupper(substr(md5($earning->id . now()), 0, 10)),
        ]);

        Notification::create([
            'user_id' => $earning->seller_id,
            'title'   => 'Payout Released! 🎉',
            'body'    => '₹' . number_format($earning->seller_amount, 2) . ' has been released to your wallet.',
            'type'    => 'payout',
        ]);

        return back()->with('success', 'Payout released to seller wallet!');
    }

    public function categories()
    {
        $categories = Category::withCount('products')->orderBy('sort_order')->get();
        return view('admin.categories', compact('categories'));
    }

    public function storeCategory(Request $request)
    {
        $request->validate(['name' => 'required|string|max:100', 'icon' => 'nullable|string|max:10']);
        Category::create([
            'name'       => $request->name,
            'slug'       => \Illuminate\Support\Str::slug($request->name) . '-' . \Illuminate\Support\Str::random(4),
            'icon'       => $request->icon,
            'parent_id'  => $request->parent_id,
            'sort_order' => $request->sort_order ?? 0,
            'is_active'  => true,
        ]);
        return back()->with('success', 'Category created!');
    }

    public function settings()
    {
        return view('admin.settings');
    }

    public function updateSettings(Request $request)
    {
        foreach ($request->settings ?? [] as $key => $value) {
            AppSetting::set($key, $value);
        }
        Cache::flush();
        return back()->with('success', 'Settings updated!');
    }

    public function referrals()
    {
        $referrals = Referral::with(['referrer', 'referred'])->latest()->paginate(20);
        return view('admin.referrals', compact('referrals'));
    }

    // Returns management
    public function returns(Request $request)
    {
        $status  = $request->status ?? 'all';
        $query   = ReturnRequest::with(['order.buyer', 'order.items.product'])->latest();
        if ($status !== 'all') $query->where('status', $status);
        $returns = $query->paginate(20);
        return view('admin.returns', compact('returns', 'status'));
    }

    public function processReturn(Request $request, ReturnRequest $return)
    {
        $request->validate([
            'action'     => 'required|in:approve,reject',
            'admin_note' => 'nullable|string|max:500',
        ]);

        $newStatus = $request->action === 'approve' ? 'approved' : 'rejected';
        $return->update(['status' => $newStatus, 'admin_note' => $request->admin_note]);

        // If approved, create refund record, restore stock, and cancel seller earnings
        if ($newStatus === 'approved') {
            foreach ($return->order->items as $item) {
                if ($item->product) {
                    $item->product->increment('stock', $item->quantity);
                    if ($item->product->status === 'sold' && $item->product->stock > 0) {
                        $item->product->update(['status' => 'active']);
                    }
                }
            }

            // Cancel seller earnings to prevent payout after return/refund
            SellerEarning::where('order_id', $return->order_id)->update(['status' => 'failed']);

            $payment = $return->order->payment;
            if ($payment) {
                Refund::firstOrCreate(
                    ['order_id' => $return->order_id],
                    [
                        'payment_id'      => $payment->id,
                        'amount'          => $return->order->total,
                        'reason'          => $return->reason,
                        'status'          => 'pending',
                        'transaction_ref' => null,
                    ]
                );
            }
        }

        Notification::create([
            'user_id' => $return->user_id,
            'title'   => $newStatus === 'approved' ? 'Return Approved ✅' : 'Return Rejected ❌',
            'body'    => $newStatus === 'approved'
                ? "Your return for order #{$return->order->order_number} has been approved. Refund will be processed soon."
                : "Your return for order #{$return->order->order_number} was rejected." . ($request->admin_note ? " Note: {$request->admin_note}" : ''),
            'type'    => 'return',
        ]);

        return back()->with('success', "Return {$newStatus}.");
    }

    // Refunds management
    public function refunds(Request $request)
    {
        $status  = $request->status ?? 'all';
        $query   = Refund::with(['order.buyer', 'payment'])->latest();
        if ($status !== 'all') $query->where('status', $status);
        $refunds = $query->paginate(20);
        return view('admin.refunds', compact('refunds', 'status'));
    }

    public function processRefund(Refund $refund, \App\Services\RazorpayService $razorpayService)
    {
        if ($refund->status === 'processed') {
            return back()->with('error', 'Already processed.');
        }

        try {
            $payment = $refund->payment;
            $gatewayRefund = null;

            if ($payment) {
                // Idempotency: if gateway_refund_id already exists from webhook or previous call, reuse it without calling gateway again
                if ($refund->gateway_refund_id) {
                    $gatewayRefund = ['id' => $refund->gateway_refund_id, 'status' => 'processed'];
                } else {
                    $gatewayRefund = $razorpayService->createRefund($payment, $refund->amount, $refund->reason);
                }
            }

            $refund->update([
                'status'            => 'processed',
                'gateway_refund_id' => $gatewayRefund['id'] ?? null,
                'transaction_ref'   => $gatewayRefund['id'] ?? ('REFUND-' . strtoupper(\Illuminate\Support\Str::random(8))),
                'processed_at'      => now(),
            ]);

            // Update order payment status
            $refund->order->update(['payment_status' => 'refunded']);

            // Cancel seller earnings so seller is not paid for refunded order
            SellerEarning::where('order_id', $refund->order_id)->update(['status' => 'failed']);

            if ($payment) {
                $payment->update(['refunded_at' => now()]);
            }

            Notification::create([
                'user_id' => $refund->order->user_id,
                'title'   => 'Refund Processed 💸',
                'body'    => "₹" . number_format($refund->amount, 2) . " refund for order #{$refund->order->order_number} has been processed. Ref: " . ($gatewayRefund['id'] ?? $refund->transaction_ref),
                'type'    => 'refund',
            ]);

            return back()->with('success', 'Refund processed successfully!');
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error("Process refund failed for refund #{$refund->id}: " . $e->getMessage());
            return back()->with('error', 'Gateway refund failed: ' . $e->getMessage());
        }
    }
}
