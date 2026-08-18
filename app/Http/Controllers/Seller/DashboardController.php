<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\SellerEarning;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $seller = Auth::user();

        $totalProducts  = Product::where('user_id', $seller->id)->count();
        $activeProducts = Product::where('user_id', $seller->id)->where('status', 'active')->count();

        $newOrders = OrderItem::where('seller_id', $seller->id)
            ->where('status', 'confirmed')->count();

        $totalSales = SellerEarning::where('seller_id', $seller->id)
            ->whereIn('status', ['on_hold', 'released'])->sum('order_amount');

        $pendingEarnings = SellerEarning::where('seller_id', $seller->id)
            ->whereIn('status', ['pending', 'customer_ok', 'on_hold'])->sum('seller_amount');

        $releasedEarnings = SellerEarning::where('seller_id', $seller->id)
            ->where('status', 'released')->sum('seller_amount');

        $recentOrders = OrderItem::with(['order.buyer', 'product.images'])
            ->where('seller_id', $seller->id)
            ->latest()
            ->limit(5)
            ->get();

        $availableEarnings = $releasedEarnings; // alias for view

        return view('seller.dashboard', compact(
            'totalProducts', 'activeProducts', 'newOrders',
            'totalSales', 'pendingEarnings', 'releasedEarnings', 'availableEarnings', 'recentOrders'
        ));
    }
}
