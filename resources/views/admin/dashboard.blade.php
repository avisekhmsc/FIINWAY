@extends('layouts.admin')

@section('content')
<div class="flex justify-between items-center mb-8">
    <h1 class="text-2xl font-bold text-slate-800">Admin Dashboard</h1>
    <div class="text-sm text-slate-500">{{ now()->format('l, d M Y') }}</div>
</div>

<!-- Stats Grid -->
<div class="grid grid-cols-4 gap-6 mb-8">
    <div class="stat-card">
        <div class="stat-icon bg-indigo-100 text-[#006837]"><i class="ri-group-fill"></i></div>
        <div>
            <div class="stat-value">{{ $stats['users'] }}</div>
            <div class="stat-label">Total Users ({{ $stats['sellers'] }} Sellers)</div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon bg-emerald-100 text-emerald-600"><i class="ri-shopping-bag-3-fill"></i></div>
        <div>
            <div class="stat-value">{{ $stats['products'] }}</div>
            <div class="stat-label">Products ({{ $stats['pending_products'] }} Pending)</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon bg-orange-100 text-orange-700"><i class="ri-shopping-cart-fill"></i></div>
        <div>
            <div class="stat-value">{{ $stats['orders'] }}</div>
            <div class="stat-label">Total Orders</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon bg-green-100 text-green-700"><i class="ri-wallet-3-fill"></i></div>
        <div>
            <div class="stat-value text-xl">₹{{ number_format($stats['commission']) }}</div>
            <div class="stat-label">Platform Commission</div>
        </div>
    </div>
</div>

<div class="grid grid-cols-2 gap-8">
    <!-- Pending Products -->
    <div class="fk-card p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-lg font-bold text-slate-800">Products Pending Approval</h2>
        <a href="{{ route('admin.products', ['status' => 'pending']) }}" class="text-sm font-semibold" style="color:#006837;">View All</a>
        </div>
        
        @if($pendingProducts->isEmpty())
            <p class="text-slate-500 text-sm text-center py-4">No products pending approval.</p>
        @else
            <div class="space-y-4">
                @foreach($pendingProducts as $product)
                <div class="flex items-center justify-between p-3 bg-slate-50 rounded-xl border border-slate-100">
                    <div class="flex items-center gap-3">
                        <img src="{{ $product->primary_image_url }}" class="w-12 h-12 rounded-lg object-cover">
                        <div>
                            <p class="text-sm font-semibold text-slate-800">{{ $product->name }}</p>
                            <p class="text-xs text-slate-500">By {{ $product->seller->name }} • ₹{{ number_format($product->selling_price) }}</p>
                        </div>
                    </div>
                    <form action="{{ route('admin.products.approve', $product->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="fk-btn-primary text-xs py-2 px-4 shadow-none"><i class="ri-check-line"></i> Approve</button>
                    </form>
                </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Recent Orders -->
    <div class="fk-card p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-lg font-bold text-slate-800">Recent Orders</h2>
            <a href="{{ route('admin.orders') }}" class="text-sm font-semibold" style="color:#006837;">View All</a>
        </div>
        
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Buyer</th>
                        <th>Amount</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentOrders as $order)
                    <tr>
                        <td class="font-semibold text-slate-700">{{ $order->order_number }}</td>
                        <td>{{ $order->buyer->name }}</td>
                        <td class="font-bold">₹{{ number_format($order->total) }}</td>
                        <td><span class="badge {{ $order->status === 'delivered' ? 'badge-success' : 'badge-warning' }}">{{ ucfirst($order->status) }}</span></td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="text-center py-4 text-slate-500">No recent orders.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
