@extends('layouts.app')

@section('title', 'My Orders — FIINWAY')

@section('content')
<div class="bg-[#f1f3f6] min-h-screen pb-16 md:pb-0">
    <div class="max-w-5xl mx-auto px-2 sm:px-4 py-4 sm:py-6 space-y-3">

        {{-- Header + Filter Tabs --}}
        <div class="bg-white rounded-sm shadow-sm p-4">
            <h1 class="text-xl font-medium text-[#212121] mb-3">My Orders</h1>
            <div class="flex items-center gap-2 overflow-x-auto pb-1 scrollbar-hide">
                @php $tabs = ['all'=>'All','pending'=>'Pending','shipped'=>'Shipped','delivered'=>'Delivered','cancelled'=>'Cancelled']; @endphp
                @foreach($tabs as $val => $label)
                    <a href="{{ route('orders', ['status'=>$val]) }}"
                       class="px-4 py-1.5 rounded text-sm font-medium border whitespace-nowrap transition-colors
                       {{ $status === $val ? 'border-[#006837] text-[#006837] bg-green-50' : 'border-slate-200 text-[#212121] hover:border-[#006837]' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>
        </div>

        {{-- Order Cards --}}
        @forelse($orders as $order)
            <div class="bg-white rounded-sm shadow-sm overflow-hidden">
                {{-- Order Header --}}
                <div class="px-4 py-3 bg-slate-50 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                    <div class="flex items-center gap-4 text-xs text-slate-500 font-medium flex-wrap">
                        <span>ORDER #{{ $order->order_number }}</span>
                        <span>{{ $order->created_at->format('d M Y, h:i A') }}</span>
                        @if($order->payment_status === 'paid')
                            <span class="text-[#388e3c] font-bold uppercase">● PAID</span>
                        @else
                            <span class="text-[#ff9f00] font-bold uppercase">● PAYMENT PENDING</span>
                        @endif
                    </div>
                    <span class="text-xs font-bold px-2 py-1 rounded uppercase
                        {{ $order->status === 'delivered' ? 'bg-[#388e3c] text-white' : ($order->status === 'cancelled' ? 'bg-red-600 text-white' : 'bg-[#006837] text-white') }}">
                        {{ strtoupper($order->status) }}
                    </span>
                </div>

                {{-- Items --}}
                @foreach($order->items as $item)
                    <div class="px-4 py-4 flex items-center gap-4 border-b border-slate-100 last:border-0">
                        <div class="w-16 h-16 shrink-0 border border-slate-100 bg-white flex items-center justify-center p-1">
                            <x-product-image :product="$item->product" aspect="square" class="w-full h-full object-contain" />
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="font-medium text-[#212121] text-sm truncate">{{ $item->product_name }}</h4>
                            <p class="text-xs text-slate-500 mt-0.5">Seller: {{ $item->seller->name ?? 'Verified Seller' }} • Qty: {{ $item->quantity }}</p>
                        </div>
                        <span class="font-bold text-[#212121] text-sm shrink-0">₹{{ number_format($item->subtotal) }}</span>
                    </div>
                @endforeach

                {{-- Footer --}}
                <div class="px-4 py-3 bg-white flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                    <div>
                        <span class="text-xs text-slate-500">Total Amount</span>
                        <p class="text-lg font-bold text-[#212121]">₹{{ number_format($order->total) }}</p>
                    </div>
                    <div class="flex items-center gap-2">
                        @if($order->payment_status !== 'paid')
                            <a href="{{ route('payment', $order->id) }}" class="px-6 py-2 bg-[#e94f1c] text-white font-bold text-sm rounded-sm hover:bg-[#cc4214] transition-colors flex items-center gap-2">
                                <i class="ri-bank-card-line"></i> Complete Payment
                            </a>
                        @else
                            <a href="{{ route('orders.track', $order->id) }}" class="px-5 py-2 border border-slate-300 text-[#212121] font-medium text-sm rounded-sm hover:bg-slate-50">
                                Track
                            </a>
                            <a href="{{ route('orders.show', $order->id) }}" class="px-5 py-2 border border-[#006837] text-[#006837] font-medium text-sm rounded-sm hover:bg-green-50">
                                View Details
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-sm shadow-sm p-16 text-center">
                <i class="ri-file-list-3-line text-5xl text-slate-200 block mb-4"></i>
                <h3 class="text-lg font-medium text-[#212121] mb-2">No orders found</h3>
                <p class="text-sm text-slate-500 mb-6">You haven't placed any orders yet.</p>
                <a href="{{ route('products') }}" class="px-8 py-3 bg-[#e94f1c] text-white font-bold text-sm rounded-sm hover:bg-[#cc4214]">Start Shopping</a>
            </div>
        @endforelse

        <div class="pb-4">{{ $orders->links('pagination::tailwind') }}</div>

    </div>
</div>
@endsection
