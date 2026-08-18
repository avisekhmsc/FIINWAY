@extends('layouts.app', ['hideNav' => true])

@section('content')
<div class="bg-slate-50 min-h-screen pb-32">
    <!-- Header -->
    <div class="sticky top-0 z-40 bg-white border-b border-slate-100 p-4 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('seller.orders') }}" class="w-10 h-10 rounded-full bg-slate-50 flex items-center justify-center text-slate-700">
                <i class="ri-arrow-left-line text-lg"></i>
            </a>
            <h1 class="text-lg font-bold text-slate-900">Manage Order</h1>
        </div>
    </div>

    <div class="p-4 space-y-4">
        <!-- Action Card -->
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100 text-center">
            <div class="inline-flex items-center justify-center px-3 py-1 bg-slate-100 rounded-full text-xs font-bold text-slate-600 mb-3 uppercase tracking-wider">
                Current Status
            </div>
            <h2 class="text-2xl font-black text-slate-900 mb-2">{{ ucfirst(str_replace('_', ' ', $item->status)) }}</h2>
            
            <div class="mt-6 pt-6 border-t border-slate-100">
                @if($item->status === 'confirmed')
                    <form action="{{ route('seller.orders.pack', $item->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-primary btn-block btn-lg"><i class="ri-box-3-line mr-1"></i> Mark as Packed</button>
                    </form>
                @elseif($item->status === 'packed')
                    <div x-data="{ open: false }">
                        <button @click="open = true" class="btn btn-primary btn-block btn-lg"><i class="ri-truck-line mr-1"></i> Ship Order</button>
                        
                        <!-- Ship Modal -->
                        <div x-show="open" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm" x-cloak>
                            <div class="bg-white rounded-3xl w-full max-w-sm p-6 shadow-2xl relative text-left" @click.away="open = false">
                                <h3 class="text-lg font-bold text-slate-900 mb-4">Shipping Details</h3>
                                <form action="{{ route('seller.orders.ship', $item->id) }}" method="POST" class="space-y-4">
                                    @csrf
                                    <div>
                                        <label class="block text-sm font-semibold text-slate-700 mb-1">Courier Partner (Optional)</label>
                                        <input type="text" name="courier_name" class="input" placeholder="e.g. Delhivery, DTDC">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-slate-700 mb-1">Tracking ID (Optional)</label>
                                        <input type="text" name="tracking_id" class="input" placeholder="e.g. AWB123456789">
                                    </div>
                                    <div class="pt-4 flex gap-3">
                                        <button type="button" @click="open = false" class="btn btn-outline flex-1">Cancel</button>
                                        <button type="submit" class="btn btn-primary flex-1">Confirm Ship</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @elseif($item->status === 'shipped')
                    <form action="{{ route('seller.orders.out-for-delivery', $item->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-primary btn-block btn-lg"><i class="ri-ebike-2-line mr-1"></i> Out for Delivery</button>
                    </form>
                @elseif($item->status === 'out_for_delivery')
                    <form action="{{ route('seller.orders.deliver', $item->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-success btn-block btn-lg shadow-lg shadow-green-200"><i class="ri-checkbox-circle-fill mr-1"></i> Mark Delivered</button>
                    </form>
                @endif
            </div>
        </div>

        <!-- Product Summary -->
        <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-100">
            <h3 class="text-sm font-bold text-slate-800 mb-4">Product Details</h3>
            <div class="flex items-start gap-3">
                <img src="{{ $item->product->primary_image_url }}" class="w-16 h-16 rounded-xl object-cover shrink-0 border border-slate-100">
                <div>
                    <h4 class="font-semibold text-slate-900 text-sm mb-1">{{ $item->product_name }}</h4>
                    <p class="text-xs text-slate-500 mb-2">Price: ₹{{ number_format($item->price) }} | Qty: {{ $item->quantity }}</p>
                    <p class="font-bold text-indigo-600">Total: ₹{{ number_format($item->subtotal) }}</p>
                </div>
            </div>
        </div>

        <!-- Earning Summary -->
        <div class="bg-emerald-50 rounded-2xl p-4 border border-emerald-100">
            <h3 class="text-sm font-bold text-emerald-900 mb-3 flex items-center gap-2"><i class="ri-wallet-3-line"></i> Earning Breakdown</h3>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between text-emerald-700">
                    <span>Order Amount</span>
                    <span class="font-medium">₹{{ number_format($item->subtotal) }}</span>
                </div>
                <div class="flex justify-between text-emerald-700">
                    <span>Commission ({{ \App\Models\AppSetting::get('commission_percent', 10) }}%)</span>
                    <span class="font-medium text-red-500">-₹{{ number_format($item->subtotal * (\App\Models\AppSetting::get('commission_percent', 10)/100)) }}</span>
                </div>
                <div class="divider border-emerald-200 border-dashed my-2"></div>
                <div class="flex justify-between items-center">
                    <span class="font-bold text-emerald-900">You Earnings</span>
                    <span class="font-black text-emerald-600 text-lg">₹{{ number_format($item->subtotal - ($item->subtotal * (\App\Models\AppSetting::get('commission_percent', 10)/100))) }}</span>
                </div>
            </div>
        </div>

        <!-- Buyer & Shipping Info -->
        <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-100">
            <h3 class="text-sm font-bold text-slate-800 mb-4">Buyer Details & Address</h3>
            
            <div class="mb-4">
                <p class="text-sm font-bold text-slate-900">{{ $item->order->buyer->name }}</p>
                <p class="text-xs text-slate-500"><i class="ri-phone-line"></i> +91 {{ $item->order->buyer->phone }}</p>
            </div>

            <div class="bg-slate-50 rounded-xl p-3 border border-slate-100">
                <p class="font-bold text-slate-800 text-sm mb-1">{{ $item->order->address->full_name }}</p>
                <p class="text-xs text-slate-600 leading-relaxed">{{ $item->order->address->fullText() }}</p>
            </div>
        </div>

    </div>
</div>
@endsection
