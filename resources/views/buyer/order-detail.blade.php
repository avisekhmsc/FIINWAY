@extends('layouts.app', ['hideNav' => true])

@section('content')
<div class="bg-slate-50 min-h-screen pb-32">
    <!-- Header -->
    <div class="sticky top-0 z-40 bg-white border-b border-slate-100 p-4 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('orders') }}" class="w-10 h-10 rounded-full bg-slate-50 flex items-center justify-center text-slate-700">
                <i class="ri-arrow-left-line text-lg"></i>
            </a>
            <h1 class="text-lg font-bold text-slate-900">Order Details</h1>
        </div>
        @if(!in_array($order->status, ['delivered', 'cancelled', 'returned']))
            <a href="{{ route('orders.track', $order->id) }}" class="btn btn-outline btn-sm !rounded-full text-xs py-1.5 px-3"><i class="ri-map-pin-time-line"></i> Track</a>
        @endif
    </div>

    <div class="p-4 space-y-4">
        <!-- Status Card -->
        <div class="bg-white rounded-2xl p-4 shadow-sm text-center">
            <h2 class="text-xl font-black text-slate-900 mb-1">{{ $order->statusLabel() }}</h2>
            <p class="text-xs text-slate-500 mb-4">Order ID: {{ $order->order_number }} • {{ $order->created_at->format('d M, Y') }}</p>
            
            @if($order->status === 'delivered' && !$order->customer_confirmed)
                <div class="bg-green-50 rounded-xl p-4 border border-green-200 mt-2 text-left">
                    <h3 class="text-sm font-bold text-green-800 mb-1">Did you receive the product?</h3>
                    <p class="text-xs text-green-700 mb-3">Please confirm if you have received the items in good condition. This helps release payment to the seller.</p>
                    <form action="{{ route('orders.confirm-receipt', $order->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-success btn-sm btn-block !rounded-lg"><i class="ri-check-double-line"></i> Yes, Product Received & OK</button>
                    </form>
                </div>
            @endif
        </div>

        <!-- Items -->
        <div class="bg-white rounded-2xl p-4 shadow-sm">
            <h3 class="text-sm font-bold text-slate-800 mb-4">Items ({{ $order->items->count() }})</h3>
            <div class="space-y-4">
                @foreach($order->items as $item)
                <div class="flex items-start gap-3 pb-4 border-b border-slate-50 last:border-0 last:pb-0 flex-col sm:flex-row">
                    <div class="flex items-start gap-3 w-full">
                        <a href="{{ route('products.show', $item->product->slug) }}" class="w-16 h-16 rounded-xl bg-slate-50 shrink-0 border border-slate-100">
                            <img src="{{ $item->product->primary_image_url }}" alt="{{ $item->product->name }}" class="w-full h-full object-cover rounded-xl mix-blend-multiply">
                        </a>
                        <div class="flex-1">
                            <a href="{{ route('products.show', $item->product->slug) }}" class="text-sm font-semibold text-slate-800 line-clamp-2 leading-tight hover:text-indigo-600">{{ $item->product->name }}</a>
                            <p class="text-xs text-slate-500 mt-1">Sold by: <span class="font-semibold">{{ $item->seller->name }}</span></p>
                            
                            <div class="flex items-center justify-between mt-2">
                                <p class="text-sm font-bold text-slate-900">₹{{ number_format($item->price) }} <span class="text-xs text-slate-500 font-normal">x {{ $item->quantity }}</span></p>
                                <span class="badge {{ $item->status === 'delivered' ? 'badge-success' : 'badge-primary' }} !text-[0.6rem]">{{ ucfirst(str_replace('_', ' ', $item->status)) }}</span>
                            </div>
                        </div>
                    </div>
                    
                    @if($order->status === 'delivered')
                    <div class="w-full flex items-center justify-end gap-2 mt-2 sm:mt-0 pt-2 sm:pt-0 sm:border-0 border-t border-slate-50">
                        <a href="{{ route('returns.create', ['order_id' => $order->id]) }}" class="btn btn-outline !border-slate-200 !text-slate-600 btn-sm text-xs py-1.5 px-3">Return</a>
                        <a href="{{ route('reviews.create', ['order_id' => $order->id, 'product_id' => $item->product_id]) }}" class="btn btn-outline btn-sm text-xs py-1.5 px-3">Write Review</a>
                    </div>
                    @endif
                </div>
                @endforeach
            </div>
        </div>

        <!-- Address -->
        <div class="bg-white rounded-2xl p-4 shadow-sm">
            <h3 class="text-sm font-bold text-slate-800 mb-3">Delivery Address</h3>
            <p class="font-bold text-slate-900 text-sm mb-1">{{ $order->address->full_name }} <span class="badge badge-primary !text-[0.6rem] ml-2">{{ $order->address->label }}</span></p>
            <p class="text-xs text-slate-600 leading-relaxed mb-1">{{ $order->address->fullText() }}</p>
            <p class="text-xs font-semibold text-slate-800"><i class="ri-phone-line"></i> +91 {{ $order->address->phone }}</p>
        </div>

        <!-- Payment Details -->
        <div class="bg-white rounded-2xl p-4 shadow-sm">
            <h3 class="text-sm font-bold text-slate-800 mb-4">Payment Summary</h3>
            
            <div class="space-y-3 text-sm">
                <div class="flex justify-between text-slate-600">
                    <span>Items Total</span>
                    <span class="font-medium text-slate-800">₹{{ number_format($order->subtotal) }}</span>
                </div>
                <div class="flex justify-between text-slate-600">
                    <span>Delivery Charge</span>
                    <span class="font-medium text-slate-800">₹{{ number_format($order->delivery_charge) }}</span>
                </div>
                @if($order->discount > 0)
                <div class="flex justify-between text-green-600 font-medium">
                    <span>Discount</span>
                    <span>-₹{{ number_format($order->discount) }}</span>
                </div>
                @endif
                
                <div class="divider border-dashed my-3"></div>
                
                <div class="flex justify-between items-center mb-2">
                    <span class="font-bold text-slate-900 text-base">Total Amount</span>
                    <span class="font-black text-indigo-600 text-lg">₹{{ number_format($order->total) }}</span>
                </div>
                
                <div class="bg-slate-50 rounded-lg p-3 flex items-center justify-between">
                    <span class="text-xs text-slate-500 font-medium">Payment Mode</span>
                    <span class="text-xs font-bold text-slate-800 uppercase flex items-center gap-1">
                        @if($order->payment_status === 'paid')
                            <i class="ri-checkbox-circle-fill text-green-500"></i> {{ $order->payment_method }}
                        @else
                            <i class="ri-time-fill text-orange-700"></i> Pending
                        @endif
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
