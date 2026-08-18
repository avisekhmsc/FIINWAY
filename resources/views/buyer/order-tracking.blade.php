@extends('layouts.app')

@section('title', 'Track Order #' . $order->order_number . ' — FIINWAY')

@section('content')
<div class="bg-[#f1f3f6] min-h-screen pb-16">
    <div class="max-w-4xl mx-auto px-2 sm:px-4 py-4 sm:py-6 space-y-4">

        {{-- Header --}}
        <div class="bg-white p-4 sm:p-6 rounded-sm shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-lg font-medium text-[#212121]">Order Tracking</h1>
                <p class="text-xs text-[#878787] mt-1">Order #{{ $order->order_number }} • {{ $order->created_at->format('d M Y') }}</p>
            </div>
            <a href="{{ route('orders.show', $order->id) }}" class="px-5 py-2 rounded-sm border border-slate-300 hover:bg-slate-50 text-[#212121] text-sm font-medium transition-colors text-center">
                View Details
            </a>
        </div>

        @php
            // Resolve which items and status to display
            // Use shipments if they exist, otherwise synthesize one block from the order itself
            $trackingBlocks = $order->shipments->isNotEmpty()
                ? $order->shipments->map(fn($s) => [
                    'shipment'   => $s,
                    'status'     => $s->status,
                    'items'      => ($s->items && $s->items->isNotEmpty()) ? $s->items : $order->items,
                    'courier'    => $s->courier_name,
                    'trackingId' => $s->tracking_id,
                    'seller'     => $s->seller->name ?? 'Verified Seller',
                  ])
                : collect([[
                    'shipment'   => null,
                    'status'     => $order->status,
                    'items'      => $order->items,
                    'courier'    => null,
                    'trackingId' => null,
                    'seller'     => null,
                  ]]);

            $statusOrder = ['confirmed' => 1, 'packed' => 2, 'shipped' => 3, 'out_for_delivery' => 4, 'delivered' => 5];
        @endphp

        @foreach($trackingBlocks as $block)
        @php
            $status      = $block['status'];
            $items       = $block['items'];
            $currentStep = $statusOrder[$status] ?? 1;
        @endphp

        <div class="bg-white rounded-sm shadow-sm overflow-hidden">

            {{-- Shipment header --}}
            <div class="p-4 sm:p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-slate-50">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-green-50 text-[#006837] flex items-center justify-center">
                        <i class="ri-truck-line text-lg"></i>
                    </div>
                    <div>
                        @if($block['shipment'])
                            <h3 class="font-medium text-[#212121] text-sm">Shipment #{{ $block['shipment']->id }}</h3>
                            <p class="text-xs text-[#878787]">Seller: {{ $block['seller'] }}</p>
                        @else
                            <h3 class="font-medium text-[#212121] text-sm">Order #{{ $order->order_number }}</h3>
                            <p class="text-xs text-[#878787]">All items</p>
                        @endif
                    </div>
                </div>

                @if($block['trackingId'])
                <div class="flex flex-col sm:flex-row gap-3 sm:items-center">
                    @if($block['seller_id'] ?? false)
                        @php $sellerModel = \App\Models\User::find($block['seller_id']); @endphp
                        @if($sellerModel)
                            <div class="flex gap-2">
                                @if($sellerModel->phone)
                                    <a href="tel:{{ $sellerModel->phone }}" class="px-3 py-1.5 bg-green-50 text-green-700 border border-green-200 rounded-sm text-xs font-bold hover:bg-green-100 flex items-center gap-1">
                                        <i class="ri-phone-line"></i> Call Seller
                                    </a>
                                @endif
                                <a href="{{ route('chat.show', ['order' => $order->id, 'seller' => $sellerModel->id]) }}" class="px-3 py-1.5 bg-green-50 text-green-800 border border-green-200 rounded-sm text-xs font-bold hover:bg-green-100 flex items-center gap-1">
                                    <i class="ri-chat-3-line"></i> Chat with Seller
                                </a>
                            </div>
                        @endif
                    @endif
                    <div class="px-4 py-2 bg-white border border-slate-200 rounded-sm text-right sm:text-left">
                        <span class="text-[10px] font-bold text-[#878787] uppercase tracking-wider block">{{ $block['courier'] ?? 'Courier Partner' }}</span>
                        <span class="text-sm font-bold text-[#212121]">{{ $block['trackingId'] }}</span>
                    </div>
                </div>
                @else
                    @if($block['seller_id'] ?? false)
                        @php $sellerModel = \App\Models\User::find($block['seller_id']); @endphp
                        @if($sellerModel)
                            <div class="flex gap-2">
                                @if($sellerModel->phone)
                                    <a href="tel:{{ $sellerModel->phone }}" class="px-3 py-1.5 bg-green-50 text-green-700 border border-green-200 rounded-sm text-xs font-bold hover:bg-green-100 flex items-center gap-1">
                                        <i class="ri-phone-line"></i> Call Seller
                                    </a>
                                @endif
                                <a href="{{ route('chat.show', ['order' => $order->id, 'seller' => $sellerModel->id]) }}" class="px-3 py-1.5 bg-green-50 text-green-800 border border-green-200 rounded-sm text-xs font-bold hover:bg-green-100 flex items-center gap-1">
                                    <i class="ri-chat-3-line"></i> Chat with Seller
                                </a>
                            </div>
                        @endif
                    @endif
                @endif
            </div>

            <div class="p-4 sm:p-6 md:p-8">

                {{-- Items --}}
                <div class="mb-8 space-y-3">
                    <h4 class="text-sm font-medium text-[#212121] mb-3">Items in this shipment</h4>
                    @forelse($items ?? [] as $item)
                        <div class="flex items-center gap-4 py-2 border-b border-slate-50 last:border-0">
                            <div class="w-14 h-14 shrink-0 border border-slate-100 flex items-center justify-center p-1 rounded-sm">
                                <x-product-image :product="$item->product" aspect="square" class="w-full h-full object-contain" />
                            </div>
                            <div class="flex-1 min-w-0">
                                <a href="{{ route('products.show', $item->product->slug) }}" class="font-medium text-[#212121] text-sm hover:text-[#006837] line-clamp-1">
                                    {{ $item->product_name ?? $item->product->name }}
                                </a>
                                <p class="text-xs text-[#878787] mt-0.5">Qty: {{ $item->quantity }} • ₹{{ number_format($item->price) }}</p>
                            </div>
                            @if(in_array($status, ['delivered']) && $order->status === 'delivered')
                                <a href="{{ route('reviews.create') }}?product_id={{ $item->product_id }}&order_id={{ $order->id }}"
                                   class="shrink-0 px-3 py-1.5 text-[#006837] text-xs font-medium hover:bg-green-50 transition-colors border border-green-100 rounded-sm flex items-center gap-1">
                                    <i class="ri-star-line"></i> Rate &amp; Review
                                </a>
                            @endif
                        </div>
                    @empty
                        <p class="text-sm text-[#878787]">No item details available.</p>
                    @endforelse
                </div>

                {{-- Vertical Timeline --}}
                <div class="relative pl-8 space-y-8 before:absolute before:left-3.5 before:top-3 before:bottom-3 before:w-0.5 before:bg-slate-200">

                    @foreach([
                        [1, 'Order Confirmed',   'ri-checkbox-circle-line', 'Payment verified and order submitted to seller.'],
                        [2, 'Packed & Handled',  'ri-box-3-line',           'Seller has packed your item securely.'],
                        [3, 'Shipped',           'ri-truck-line',           'In transit with courier partner.'],
                        [4, 'Out for Delivery',  'ri-map-pin-time-line',    'Delivery agent is on the way.'],
                        [5, 'Delivered',         'ri-home-smile-2-line',    'Package delivered to your address.'],
                    ] as [$step, $label, $icon, $desc])
                    @php
                        $isLast    = $step === 5;
                        $done      = $currentStep >= $step;
                        $active    = $currentStep === $step;
                        $dotColor  = $done ? ($isLast ? 'bg-[#388e3c] border-[#388e3c]' : 'bg-[#006837] border-[#006837]') : 'bg-white border-slate-200';
                        $textColor = $done ? ($isLast ? 'text-[#388e3c]' : 'text-[#212121]') : 'text-[#878787]';
                    @endphp
                    <div class="relative flex items-start gap-4">
                        <div class="absolute -left-8 top-0 w-7 h-7 rounded-full flex items-center justify-center text-xs border-2 transition-all {{ $dotColor }}">
                            @if($done)
                                <i class="ri-check-line text-white text-xs font-bold"></i>
                            @endif
                        </div>
                        <div class="pt-0.5 flex-1">
                            <h4 class="font-medium text-sm flex items-center gap-2 {{ $textColor }}">
                                <i class="{{ $icon }}"></i> {{ $label }}
                                @if($active)
                                    <span class="text-[10px] font-bold uppercase tracking-wider px-1.5 py-0.5 rounded-sm"
                                          style="background:#fff3cd;color:#856404;">Current</span>
                                @endif
                            </h4>
                            <p class="text-xs text-[#878787] mt-0.5">{{ $desc }}</p>
                        </div>
                    </div>
                    @endforeach

                </div>

            </div>
        </div>
        @endforeach

    </div>
</div>
@endsection
