@extends('layouts.app')

@section('title', 'Seller Dashboard — FIINWAY')

@section('content')
<div class="bg-[#f1f3f6] min-h-screen pb-10">
    <!-- Blue Banner Header (Flipkart Seller Hub style) -->
    <div class="bg-[#172337] text-white py-6 px-4 sm:px-8 shadow-md">
        <div class="max-w-7xl mx-auto flex flex-col sm:flex-row justify-between items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-white">Welcome back, {{ Auth::user()->name }}</h1>
                <p class="text-slate-300 text-sm mt-1">Manage your inventory, fulfill orders and grow your business.</p>
            </div>
            <a href="{{ route('seller.products.create') }}" class="px-6 py-2.5 rounded-sm bg-[#ff9f00] text-white font-bold text-sm shadow hover:bg-[#f39800] transition-colors flex items-center gap-2 uppercase tracking-wide">
                <i class="ri-add-line text-lg"></i> Add New Product
            </a>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-8 mt-[-20px] relative z-10 space-y-6">
        
        <!-- Metrics Grid -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white p-5 rounded-sm shadow-sm border-t-2 border-[#006837]">
                <p class="text-[13px] text-slate-500 font-medium uppercase tracking-wide">Total Sales</p>
                <p class="text-2xl font-bold text-[#212121] mt-1">₹{{ number_format($totalSales) }}</p>
            </div>
            <div class="bg-white p-5 rounded-sm shadow-sm border-t-2 border-[#ff9f00]">
                <p class="text-[13px] text-slate-500 font-medium uppercase tracking-wide">New Orders</p>
                <p class="text-2xl font-bold text-[#212121] mt-1">{{ $newOrders }}</p>
            </div>
            <div class="bg-white p-5 rounded-sm shadow-sm border-t-2 border-[#e94f1c]">
                <p class="text-[13px] text-slate-500 font-medium uppercase tracking-wide">Pending Earnings</p>
                <p class="text-2xl font-bold text-[#212121] mt-1">₹{{ number_format($pendingEarnings) }}</p>
            </div>
            <div class="bg-white p-5 rounded-sm shadow-sm border-t-2 border-[#388e3c]">
                <p class="text-[13px] text-slate-500 font-medium uppercase tracking-wide">Available Payout</p>
                <p class="text-2xl font-bold text-[#212121] mt-1">₹{{ number_format($availableEarnings ?? 0) }}</p>
            </div>
        </div>

        <!-- Quick Links -->
        <div class="bg-white p-6 rounded-sm shadow-sm flex flex-col md:flex-row gap-6">
            <a href="{{ route('seller.products.create') }}" class="flex-1 border border-slate-200 rounded p-4 flex items-center gap-4 hover:border-[#006837] transition-colors group">
                <div class="w-12 h-12 bg-green-50 text-[#006837] rounded-full flex items-center justify-center text-xl group-hover:bg-[#006837] group-hover:text-white transition-colors">
                    <i class="ri-add-box-line"></i>
                </div>
                <div>
                    <h4 class="font-bold text-[#212121] text-sm">Add Product</h4>
                    <p class="text-xs text-slate-500">List new inventory</p>
                </div>
            </a>
            <a href="{{ route('seller.products') }}" class="flex-1 border border-slate-200 rounded p-4 flex items-center gap-4 hover:border-[#006837] transition-colors group">
                <div class="w-12 h-12 bg-green-50 text-[#006837] rounded-full flex items-center justify-center text-xl group-hover:bg-[#006837] group-hover:text-white transition-colors">
                    <i class="ri-stack-line"></i>
                </div>
                <div>
                    <h4 class="font-bold text-[#212121] text-sm">Manage Inventory</h4>
                    <p class="text-xs text-slate-500">Edit prices & stock</p>
                </div>
            </a>
            <a href="{{ route('seller.orders') }}" class="flex-1 border border-slate-200 rounded p-4 flex items-center gap-4 hover:border-[#006837] transition-colors group">
                <div class="w-12 h-12 bg-green-50 text-[#006837] rounded-full flex items-center justify-center text-xl group-hover:bg-[#006837] group-hover:text-white transition-colors">
                    <i class="ri-file-list-3-line"></i>
                </div>
                <div>
                    <h4 class="font-bold text-[#212121] text-sm">Seller Orders</h4>
                    <p class="text-xs text-slate-500">Fulfill buyer orders</p>
                </div>
            </a>
            <a href="{{ route('seller.earnings') }}" class="flex-1 border border-slate-200 rounded p-4 flex items-center gap-4 hover:border-[#006837] transition-colors group">
                <div class="w-12 h-12 bg-green-50 text-[#006837] rounded-full flex items-center justify-center text-xl group-hover:bg-[#006837] group-hover:text-white transition-colors">
                    <i class="ri-wallet-3-line"></i>
                </div>
                <div>
                    <h4 class="font-bold text-[#212121] text-sm">Payouts</h4>
                    <p class="text-xs text-slate-500">Earnings & transfers</p>
                </div>
            </a>
        </div>

        <!-- Recent Orders -->
        <div class="bg-white rounded-sm shadow-sm border border-slate-200">
            <div class="px-6 py-4 border-b border-slate-200 flex justify-between items-center">
                <h3 class="font-bold text-[#212121] text-lg">Recent Customer Orders</h3>
                <a href="{{ route('seller.orders') }}" class="text-sm font-medium text-[#006837] hover:underline">View All</a>
            </div>
            <div class="p-0">
                @forelse($recentOrders as $item)
                    <div class="flex items-center justify-between p-4 border-b border-slate-100 last:border-0 hover:bg-slate-50 transition-colors">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 border border-slate-200 bg-white p-1">
                                <x-product-image :product="$item->product" aspect="square" class="w-full h-full object-contain" />
                            </div>
                            <div>
                                <h4 class="font-bold text-[#212121] text-sm">{{ $item->product_name }}</h4>
                                <p class="text-[12px] text-slate-500 mt-0.5">Qty: {{ $item->quantity }} | Buyer: {{ $item->order->buyer->name ?? 'Buyer' }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="font-bold text-[#212121]">₹{{ number_format($item->subtotal) }}</p>
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded text-white mt-1 inline-block bg-[#388e3c] uppercase">
                                {{ $item->status }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-slate-500 text-sm">No recent orders received yet.</div>
                @endforelse
            </div>
        </div>

    </div>
</div>
@endsection
