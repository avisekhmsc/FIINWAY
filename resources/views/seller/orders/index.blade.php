@extends('layouts.app')

@section('content')
<div class="bg-slate-50 min-h-screen pb-24">
    <!-- Header -->
    <div class="sticky top-0 z-40 bg-white border-b border-slate-100 p-4">
        <h1 class="text-xl font-bold text-slate-900">Manage Orders</h1>
        
        <!-- Tabs -->
        <div class="flex overflow-x-auto gap-4 mt-4 hide-scrollbar">
            @php $tabs = ['all' => 'All', 'confirmed' => 'New/Pending', 'packed' => 'Packed', 'shipped' => 'Shipped', 'out_for_delivery' => 'Out for Del', 'delivered' => 'Delivered']; @endphp
            @foreach($tabs as $val => $label)
                <a href="{{ route('seller.orders', ['status' => $val]) }}" class="whitespace-nowrap pb-2 text-sm font-semibold transition-colors relative {{ $status === $val ? 'text-indigo-600 border-b-2 border-indigo-600' : 'text-slate-500' }}">
                    {{ $label }}
                    @if($val === 'confirmed' && $newCount > 0)
                        <span class="absolute -top-1 -right-3 w-4 h-4 bg-red-500 text-white rounded-full text-[0.6rem] flex items-center justify-center font-bold">{{ $newCount }}</span>
                    @endif
                </a>
            @endforeach
        </div>
    </div>

    <div class="p-4 space-y-4">
        @forelse($orderItems as $item)
        <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-100">
            <div class="flex items-center justify-between mb-3 pb-3 border-b border-slate-50">
                <div>
                    <span class="text-xs font-bold text-slate-500">Order ID: {{ $item->order->order_number }}</span>
                    <p class="text-[0.65rem] text-slate-400 mt-0.5">{{ $item->created_at->format('d M Y, h:i A') }}</p>
                </div>
                <span class="badge {{ in_array($item->status, ['delivered']) ? 'badge-success' : 'badge-primary' }} text-[0.65rem]">
                    {{ ucfirst(str_replace('_', ' ', $item->status)) }}
                </span>
            </div>

            <div class="flex items-center gap-3 mb-4">
                <div class="w-16 h-16 rounded-lg bg-slate-50 shrink-0">
                    <img src="{{ $item->product->primary_image_url }}" alt="{{ $item->product_name }}" class="w-full h-full object-cover rounded-lg mix-blend-multiply">
                </div>
                <div class="flex-1">
                    <p class="text-sm font-semibold text-slate-800 line-clamp-1 mb-1">{{ $item->product_name }}</p>
                    <div class="flex justify-between items-center">
                        <p class="text-sm font-bold text-slate-900">₹{{ number_format($item->price) }} <span class="text-xs text-slate-500 font-normal">x {{ $item->quantity }}</span></p>
                        <p class="text-xs font-semibold text-indigo-600">Total: ₹{{ number_format($item->subtotal) }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-slate-50 rounded-xl p-3 mb-4 text-xs text-slate-600">
                <p><span class="font-semibold text-slate-800">Buyer:</span> {{ $item->order->buyer->name }} ({{ $item->order->address->city }})</p>
            </div>

            <div class="flex gap-2">
                <a href="{{ route('seller.orders.show', $item->id) }}" class="btn btn-outline btn-sm flex-1">View Details</a>
                
                @if($item->status === 'confirmed')
                    <form action="{{ route('seller.orders.pack', $item->id) }}" method="POST" class="flex-1">
                        @csrf
                        <button type="submit" class="btn btn-primary btn-sm btn-block">Mark Packed</button>
                    </form>
                @elseif($item->status === 'packed')
                    <a href="{{ route('seller.orders.show', $item->id) }}" class="btn btn-primary btn-sm flex-1">Ship Order</a>
                @endif
            </div>
        </div>
        @empty
        <div class="flex flex-col items-center justify-center py-16 text-center">
            <div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center text-slate-300 mb-4">
                <i class="ri-box-3-line text-4xl"></i>
            </div>
            <h3 class="text-lg font-bold text-slate-800 mb-1">No orders found</h3>
            <p class="text-sm text-slate-500">You don't have any {{ $status !== 'all' ? $status : '' }} orders yet.</p>
        </div>
        @endforelse

        <div class="mt-4">
            {{ $orderItems->links('pagination::tailwind') }}
        </div>
    </div>
</div>
@endsection
