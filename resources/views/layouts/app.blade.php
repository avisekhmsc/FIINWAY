<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'FIINWAY — India ka Bazaar')</title>
    <meta name="description" content="@yield('meta_description', 'FIINWAY: India\'s leading marketplace for new and pre-owned electronics, mobiles, laptops and gadgets with secure payments.')">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <style>
        body { font-family: 'Roboto', sans-serif; background: #f1f3f6; }
        .fk-btn-primary { background: #e94f1c; color: #fff; font-weight: 700; padding: 0.7rem 2rem; border-radius: 2px; transition: background 0.15s; box-shadow: 0 1px 2px rgba(0,0,0,.2); letter-spacing: .04em; text-transform: uppercase; }
        .fk-btn-primary:hover { background: #cc4214; }
        .fk-btn-outline { background: #fff; color: #e94f1c; border: 1px solid #e94f1c; font-weight: 700; padding: 0.7rem 2rem; border-radius: 2px; letter-spacing: .04em; text-transform: uppercase; }
        .fk-card { background: #fff; border-radius: 2px; }
        .fk-price { color: #212121; font-weight: 700; font-size: 1.1rem; }
        .fk-mrp { color: #878787; text-decoration: line-through; font-size: .85rem; }
        .fk-discount { color: #388e3c; font-weight: 600; font-size: .85rem; }
        .fk-tag-new { background: #ff6161; color: #fff; font-size: 10px; padding: 2px 6px; font-weight: 700; border-radius: 2px; }
        .fk-tag-used { background: #ff9f00; color: #fff; font-size: 10px; padding: 2px 6px; font-weight: 700; border-radius: 2px; }
        .fk-star { background: #388e3c; color: #fff; font-size: 11px; padding: 2px 6px; border-radius: 2px; font-weight: 700; }
        [x-cloak] { display: none !important; }
    </style>
    @stack('styles')
</head>
<body class="antialiased min-h-screen flex flex-col" style="background:#f1f3f6;">
    @php
        $cartCount = 0;
        if (Auth::check()) {
            $cart = Auth::user()->cart;
            $cartCount = $cart ? $cart->items()->count() : 0;
        }
    @endphp

    {{-- Top Header --}}
    @if(!isset($hideHeader))
        <x-header />
    @endif

    {{-- Flash: Success --}}
    @if(session('success'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
         class="fixed top-16 left-4 right-4 z-[100] max-w-sm mx-auto p-4 rounded bg-green-700 text-white shadow-xl flex items-center gap-3">
        <i class="ri-checkbox-circle-fill text-xl shrink-0"></i>
        <span class="flex-1 text-sm font-semibold">{{ session('success') }}</span>
        <button @click="show = false" class="text-white/80 hover:text-white"><i class="ri-close-line text-lg"></i></button>
    </div>
    @endif

    @if(session('error'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
         class="fixed top-16 left-4 right-4 z-[100] max-w-sm mx-auto p-4 rounded bg-red-600 text-white shadow-xl flex items-center gap-3">
        <i class="ri-error-warning-fill text-xl shrink-0"></i>
        <span class="flex-1 text-sm font-semibold">{{ session('error') }}</span>
        <button @click="show = false" class="text-white/80 hover:text-white"><i class="ri-close-line text-lg"></i></button>
    </div>
    @endif

    <main class="flex-1 pb-16 md:pb-0">
        @yield('content')
    </main>

    {{-- Footer --}}
    @if(!isset($hideFooter))
    <footer style="background:#172337;" class="text-slate-300 hidden md:block mt-4">
        {{-- Top Links --}}
        <div class="max-w-7xl mx-auto px-4 py-10 grid grid-cols-2 md:grid-cols-5 gap-8 text-xs border-b border-slate-700">
            <div>
                <h4 class="text-white font-bold uppercase tracking-wider mb-3 text-[11px]">About</h4>
                <ul class="space-y-2 text-slate-400">
                    <li><a href="{{ route('page.contact') }}" class="hover:text-white">Contact Us</a></li>
                    <li><a href="{{ route('page.about') }}" class="hover:text-white">About FIINWAY</a></li>
                    <li><a href="{{ route('page.careers') }}" class="hover:text-white">Careers</a></li>
                    <li><a href="{{ route('page.press') }}" class="hover:text-white">Press</a></li>
                </ul>
            </div>
            <div>
                <h4 class="text-white font-bold uppercase tracking-wider mb-3 text-[11px]">Help</h4>
                <ul class="space-y-2 text-slate-400">
                    <li><a href="{{ route('page.payments') }}" class="hover:text-white">Payments</a></li>
                    <li><a href="{{ route('page.shipping') }}" class="hover:text-white">Shipping</a></li>
                    <li><a href="{{ route('returns.index') }}" class="hover:text-white">Returns</a></li>
                    <li><a href="{{ route('orders') }}" class="hover:text-white">Track Order</a></li>
                </ul>
            </div>
            <div>
                <h4 class="text-white font-bold uppercase tracking-wider mb-3 text-[11px]">Policy</h4>
                <ul class="space-y-2 text-slate-400">
                    <li><a href="{{ route('page.return-policy') }}" class="hover:text-white">Return Policy</a></li>
                    <li><a href="{{ route('page.terms') }}" class="hover:text-white">Terms of Use</a></li>
                    <li><a href="{{ route('page.security') }}" class="hover:text-white">Security</a></li>
                    <li><a href="{{ route('page.privacy') }}" class="hover:text-white">Privacy</a></li>
                </ul>
            </div>
            <div>
                <h4 class="text-white font-bold uppercase tracking-wider mb-3 text-[11px]">Social</h4>
                <ul class="space-y-2 text-slate-400">
                    <li><a href="https://facebook.com" target="_blank" rel="noopener" class="hover:text-white flex items-center gap-1.5"><i class="ri-facebook-fill text-[#1877f2]"></i> Facebook</a></li>
                    <li><a href="https://twitter.com" target="_blank" rel="noopener" class="hover:text-white flex items-center gap-1.5"><i class="ri-twitter-x-fill"></i> Twitter</a></li>
                    <li><a href="https://instagram.com" target="_blank" rel="noopener" class="hover:text-white flex items-center gap-1.5"><i class="ri-instagram-fill text-pink-400"></i> Instagram</a></li>
                    <li><a href="https://youtube.com" target="_blank" rel="noopener" class="hover:text-white flex items-center gap-1.5"><i class="ri-youtube-fill text-red-500"></i> YouTube</a></li>
                </ul>
            </div>
            <div>
                <h4 class="text-white font-bold uppercase tracking-wider mb-3 text-[11px]">Sell on FIINWAY</h4>
                <ul class="space-y-2 text-slate-400">
                    <li><a href="{{ route('page.sell-online') }}" class="hover:text-white">Sell Products Online</a></li>
                    <li><a href="{{ route('seller.dashboard') }}" class="hover:text-white">Seller Dashboard</a></li>
                    <li><a href="{{ route('seller.earnings') }}" class="hover:text-white">View Earnings</a></li>
                </ul>
            </div>
        </div>
        {{-- Bottom --}}
        <div class="max-w-7xl mx-auto px-4 py-4 flex flex-col md:flex-row items-center justify-between gap-4 text-[11px] text-slate-500">
            <div class="flex items-center gap-2">
                <img src="{{ asset('logo.png') }}" alt="FIINWAY Logo" class="h-6 object-contain grayscale brightness-200">
                <span>© {{ date('Y') }} FIINWAY Marketplace Pvt. Ltd.</span>
            </div>
            <div class="flex items-center gap-4">
                <span class="flex items-center gap-1 text-slate-400"><i class="ri-shield-check-fill text-green-400 text-sm"></i> Verified & Secure Payments</span>
                <span class="flex items-center gap-1 text-slate-400"><i class="ri-truck-line text-green-500 text-sm"></i> Pan India Delivery</span>
            </div>
        </div>
    </footer>
    @endif

    {{-- Mobile Bottom Nav (Flipkart-style) --}}
    @if(!isset($hideNav))
    @php $notifCount = Auth::check() ? Auth::user()->notifications()->where('is_read', false)->count() : 0; @endphp
    <nav class="md:hidden fixed bottom-0 left-0 right-0 z-50 flex justify-around items-center py-1.5 border-t border-slate-200" style="background:#fff;">
        <a href="{{ route('home') }}" class="flex flex-col items-center gap-0.5 text-[10px] font-bold px-3 {{ request()->routeIs('home') ? 'text-green-700' : 'text-slate-500' }}">
            <i class="ri-home-5-{{ request()->routeIs('home') ? 'fill' : 'line' }} text-2xl"></i>
            Home
        </a>
        <a href="{{ route('products') }}" class="flex flex-col items-center gap-0.5 text-[10px] font-bold px-3 {{ request()->routeIs('products*') ? 'text-green-700' : 'text-slate-500' }}">
            <i class="ri-search-{{ request()->routeIs('products*') ? 'fill' : 'line' }} text-2xl"></i>
            Search
        </a>
        <a href="{{ Auth::check() ? route('cart') : route('mobile') }}" class="flex flex-col items-center gap-0.5 text-[10px] font-bold px-3 relative {{ request()->routeIs('cart') ? 'text-green-700' : 'text-slate-500' }}">
            <i class="ri-shopping-cart-2-{{ request()->routeIs('cart') ? 'fill' : 'line' }} text-2xl"></i>
            @if($cartCount > 0)
                <span class="absolute top-0 right-1 text-[8px] font-black text-white rounded-full w-4 h-4 flex items-center justify-center" style="background:#ff6161;">{{ $cartCount }}</span>
            @endif
            Cart
        </a>
        <a href="{{ Auth::check() ? route('wishlist') : route('mobile') }}" class="flex flex-col items-center gap-0.5 text-[10px] font-bold px-3 {{ request()->routeIs('wishlist') ? 'text-green-700' : 'text-slate-500' }}">
            <i class="ri-heart-3-{{ request()->routeIs('wishlist') ? 'fill' : 'line' }} text-2xl"></i>
            Wishlist
        </a>
        <a href="{{ Auth::check() ? route('orders') : route('mobile') }}" class="flex flex-col items-center gap-0.5 text-[10px] font-bold px-3 {{ request()->routeIs('orders*') ? 'text-green-700' : 'text-slate-500' }}">
            <i class="ri-file-list-3-{{ request()->routeIs('orders*') ? 'fill' : 'line' }} text-2xl"></i>
            Orders
        </a>
    </nav>
    @endif

    @stack('scripts')
</body>
</html>
