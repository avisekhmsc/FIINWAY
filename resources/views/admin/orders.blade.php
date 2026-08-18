@extends('layouts.admin')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-slate-800">All Orders</h1>
    
    <div class="flex gap-2">
        @foreach(['all' => 'All', 'pending' => 'Pending', 'confirmed' => 'Processing', 'shipped' => 'Shipped', 'delivered' => 'Delivered'] as $val => $label)
            <a href="{{ route('admin.orders', ['status' => $val]) }}" class="btn btn-sm {{ $status === $val ? 'btn-primary' : 'btn-outline bg-white border-slate-200 text-slate-600' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>
</div>

<div class="fk-card p-0">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Date</th>
                    <th>Buyer</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Payment</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                <tr>
                    <td class="font-bold text-slate-700">{{ $order->order_number }}</td>
                    <td class="text-sm">{{ $order->created_at->format('d M, Y') }}</td>
                    <td>
                        <p class="font-semibold">{{ $order->buyer->name }}</p>
                        <p class="text-xs text-slate-500">+91 {{ $order->buyer->phone }}</p>
                    </td>
                    <td class="font-black text-[#006837]">₹{{ number_format($order->total) }}</td>
                    <td>
                        <span class="badge {{ $order->status === 'delivered' ? 'badge-success' : 'badge-primary' }}">
                            {{ ucfirst($order->status) }}
                        </span>
                    </td>
                    <td>
                        @if($order->payment_status === 'paid')
                            <div class="flex items-center gap-1 text-green-600 text-sm font-bold">
                                <i class="ri-check-double-line"></i> {{ strtoupper($order->payment_method) }}
                            </div>
                        @else
                            <span class="badge badge-warning">Pending</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center py-8 text-slate-500">No orders found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($orders->hasPages())
    <div class="p-4 border-t border-slate-100">
        {{ $orders->links('pagination::tailwind') }}
    </div>
    @endif
</div>
@endsection
