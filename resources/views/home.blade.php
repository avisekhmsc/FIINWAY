@extends('layouts.app')

@section('title', 'FIINWAY - Online Shopping Site for Mobiles, Electronics, Furniture, Grocery, Lifestyle, Books & More. Best Offers!')

@section('content')
<div class="space-y-4 pt-2">
    <!-- Category Strip -->
    <div class="bg-white shadow-sm overflow-hidden mb-2">
        <div class="max-w-7xl mx-auto px-2 sm:px-4">
            <div class="flex items-center justify-between py-4 overflow-x-auto gap-4 scrollbar-hide">
                @foreach($categories->take(10) as $cat)
                    <a href="{{ route('products', ['category' => $cat->id]) }}" class="flex flex-col items-center gap-2 group min-w-[64px] shrink-0">
                        <div class="w-16 h-16 rounded bg-slate-50 flex items-center justify-center text-3xl group-hover:bg-green-50 transition-colors">
                            {{ $cat->icon ?? '🛒' }}
                        </div>
                        <span class="text-[13px] font-medium text-slate-800 text-center">{{ $cat->name }}</span>
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Hero Banner -->
    <div class="max-w-7xl mx-auto px-2 sm:px-4">
        <div class="rounded overflow-hidden flex flex-col md:flex-row items-center justify-between p-6 md:p-10 text-white relative" style="background: linear-gradient(135deg, #003d1f 0%, #006837 50%, #e94f1c 100%);">
            <div class="absolute inset-0 opacity-10" style="background-image: repeating-linear-gradient(45deg, #fff 0, #fff 1px, transparent 0, transparent 50%); background-size: 12px 12px;"></div>
            <div class="absolute top-0 right-0 w-64 h-full opacity-20" style="background: linear-gradient(135deg, transparent 0%, #e94f1c 100%);"></div>
            <div class="z-10 text-center md:text-left space-y-3">
                <h2 class="text-3xl md:text-5xl font-black italic tracking-tight" style="color: #ffd700;">Big Saving Days</h2>
                <p class="text-lg md:text-xl font-medium text-white/90">Sale is Live. Lowest Prices of the Year!</p>
                <div class="pt-2">
                    <a href="{{ route('products') }}" class="inline-block font-bold px-6 py-2 rounded text-sm transition-colors shadow" style="background: #ffd700; color: #003d1f;">
                        Shop Now
                    </a>
                </div>
            </div>
            <div class="z-10 mt-6 md:mt-0 flex gap-4">
                <i class="ri-smartphone-line text-6xl opacity-90"></i>
                <i class="ri-macbook-line text-6xl opacity-90 hidden sm:block"></i>
                <i class="ri-headphone-line text-6xl opacity-90"></i>
            </div>
        </div>
    </div>

    <!-- Deal of the Day (New Products) -->
    @if($newProducts->isNotEmpty())
    <div class="max-w-7xl mx-auto px-2 sm:px-4">
        <div class="bg-white rounded shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-medium text-slate-800">Best of Electronics</h2>
                    <p class="text-slate-400 text-sm mt-0.5">Top deals on brand new devices</p>
                </div>
                <a href="{{ route('products', ['condition' => 'new']) }}" class="w-8 h-8 rounded-full bg-green-700 text-white flex items-center justify-center hover:bg-green-800 transition-colors shadow-sm">
                    <i class="ri-arrow-right-s-line text-xl"></i>
                </a>
            </div>
            <div class="p-4 overflow-x-auto">
                <div class="flex gap-4 min-w-max pb-2">
                    @foreach($newProducts as $product)
                        <div class="w-48 shrink-0">
                            <x-product-card :product="$product" />
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Referral / Ad Banner -->
    <div class="max-w-7xl mx-auto px-2 sm:px-4">
        <div class="flex flex-col sm:flex-row gap-4">
            <div class="flex-1 rounded overflow-hidden flex items-center p-6 shadow-sm justify-between relative cursor-pointer"
                 style="background: linear-gradient(135deg, #f7a200 0%, #e94f1c 100%);"
                 onclick="window.location.href='{{ route('mobile') }}'">
                <div class="z-10">
                    <h3 class="font-bold text-xl text-white mb-1">Refer &amp; Earn ₹{{ \App\Models\AppSetting::get('referral_reward', 50) }}</h3>
                    <p class="text-white/80 text-sm font-medium">Invite friends to FIINWAY &amp; earn rewards</p>
                </div>
                <i class="ri-gift-2-line text-6xl text-white/30 z-10"></i>
            </div>
            <div class="flex-1 rounded overflow-hidden flex items-center p-6 shadow-sm justify-between relative cursor-pointer"
                 style="background: linear-gradient(135deg, #003d1f 0%, #006837 100%);"
                 onclick="window.location.href='{{ route('seller.products.create') }}'">
                <div class="z-10">
                    <h3 class="font-bold text-xl text-white mb-1">I want to sell</h3>
                    <p class="text-white/70 text-sm font-medium">Sell to crores of customers on FIINWAY</p>
                </div>
                <i class="ri-store-2-line text-6xl text-white/30 z-10"></i>
            </div>
        </div>
    </div>

    <!-- Pre-owned / Refurbished Section -->
    @if($oldProducts->isNotEmpty())
    <div class="max-w-7xl mx-auto px-2 sm:px-4 pb-8">
        <div class="bg-white rounded shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-medium text-slate-800">Top Pre-owned Deals</h2>
                    <p class="text-slate-400 text-sm mt-0.5">Certified refurbished at great prices</p>
                </div>
                <a href="{{ route('products', ['condition' => 'old']) }}" class="w-8 h-8 rounded-full bg-green-700 text-white flex items-center justify-center hover:bg-green-800 transition-colors shadow-sm">
                    <i class="ri-arrow-right-s-line text-xl"></i>
                </a>
            </div>
            <div class="p-4 grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-4">
                @foreach($oldProducts->take(5) as $product)
                    <x-product-card :product="$product" />
                @endforeach
            </div>
        </div>
    </div>
    @endif

</div>
@endsection
