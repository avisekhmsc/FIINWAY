<header class="sticky top-0 z-50" style="background:#006837;">
    {{-- Top Bar: Logo + Search + Actions --}}
    <div class="max-w-7xl mx-auto px-2 sm:px-4">
        <div class="flex items-center h-14 gap-2 sm:gap-4">

            {{-- Logo --}}
            <a href="{{ route('home') }}" class="flex flex-col items-start shrink-0">
                <img src="{{ asset('logo.png') }}" alt="FIINWAY Logo" class="h-8 sm:h-10 object-contain">
            </a>

            {{-- Search Bar --}}
            <form action="{{ route('products') }}" method="GET" class="hidden md:block flex-1 max-w-2xl">
                <div class="flex items-center bg-white rounded-sm overflow-hidden shadow-sm">
                    <input
                        type="text"
                        name="q"
                        value="{{ request('q') }}"
                        placeholder="Search for products, brands and more"
                        class="flex-1 px-3 py-2.5 text-sm text-slate-800 placeholder-slate-400 outline-none bg-white"
                    />
                    <button type="submit" class="px-4 py-2.5 flex items-center justify-center" style="background:#ff6161;">
                        <i class="ri-search-line text-white text-lg"></i>
                    </button>
                </div>
            </form>

            {{-- Desktop Nav Actions --}}
            <div class="hidden md:flex items-center gap-1">

                {{-- Login / Account --}}
                @auth
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center gap-2 px-3 py-1.5 rounded hover:bg-green-800 transition-colors">
                            <i class="ri-user-line text-white text-base"></i>
                            <div class="text-left">
                                <span class="text-white font-bold text-xs block leading-tight">{{ Str::limit(Auth::user()->name, 10) }}</span>
                                <span class="text-green-200 text-[10px]">Account</span>
                            </div>
                            <i class="ri-arrow-down-s-line text-white text-sm"></i>
                        </button>
                        <div x-show="open" @click.away="open = false" x-cloak
                             class="absolute right-0 mt-1 w-52 bg-white shadow-xl rounded border border-slate-100 z-50 py-1 text-sm text-slate-700">
                            <div class="px-4 py-2 border-b border-slate-100">
                                <p class="font-bold text-slate-900 text-xs">{{ Auth::user()->name }}</p>
                                <p class="text-[10px] text-slate-400">{{ Auth::user()->phone }}</p>
                            </div>
                            @if(Auth::user()->role === 'admin')
                                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 px-4 py-2 hover:bg-slate-50 font-bold text-green-700 text-xs">
                                    <i class="ri-shield-star-line"></i> Admin Panel
                                </a>
                            @endif
                            <a href="{{ route('profile') }}" class="flex items-center gap-2 px-4 py-2 hover:bg-slate-50 text-xs">
                                <i class="ri-user-line text-slate-400"></i> My Profile
                            </a>
                            <a href="{{ route('seller.dashboard') }}" class="flex items-center gap-2 px-4 py-2 hover:bg-slate-50 text-xs">
                                <i class="ri-store-2-line text-slate-400"></i> Seller Dashboard
                            </a>
                            <a href="{{ route('orders') }}" class="flex items-center gap-2 px-4 py-2 hover:bg-slate-50 text-xs">
                                <i class="ri-file-list-3-line text-slate-400"></i> My Orders
                            </a>
                            <a href="{{ route('wishlist') }}" class="flex items-center gap-2 px-4 py-2 hover:bg-slate-50 text-xs">
                                <i class="ri-heart-3-line text-slate-400"></i> Wishlist
                            </a>
                            <a href="{{ route('notifications') }}" class="flex items-center gap-2 px-4 py-2 hover:bg-slate-50 text-xs">
                                <i class="ri-notification-3-line text-slate-400"></i> Notifications
                            </a>
                            <div class="border-t border-slate-100 my-1"></div>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full text-left flex items-center gap-2 px-4 py-2 text-rose-600 font-bold hover:bg-rose-50 text-xs">
                                    <i class="ri-logout-box-r-line"></i> Logout
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('mobile') }}" class="flex items-center gap-1.5 px-4 py-1.5 rounded hover:bg-green-800 transition-colors border border-white/30">
                        <i class="ri-user-line text-white text-base"></i>
                        <div class="text-left">
                            <span class="text-white font-bold text-xs block leading-tight">Login</span>
                            <span class="text-green-200 text-[10px]">or Register</span>
                        </div>
                    </a>
                @endauth

                {{-- Become a Seller --}}
                <a href="{{ route('seller.products.create') }}"
                   class="hidden lg:flex items-center gap-1.5 px-3 py-1.5 rounded hover:bg-green-800 transition-colors text-white font-bold text-xs">
                    <i class="ri-store-2-line text-base"></i>
                    Become a Seller
                </a>

                {{-- Cart --}}
                @php
                    $cartCount = 0;
                    if (Auth::check()) {
                        $cart = Auth::user()->cart;
                        $cartCount = $cart ? $cart->items()->count() : 0;
                    }
                @endphp
                <a href="{{ route('cart') }}"
                   class="flex items-center gap-1.5 px-3 py-1.5 rounded hover:bg-green-800 transition-colors relative">
                    <i class="ri-shopping-cart-2-line text-white text-xl"></i>
                    <span class="text-white font-bold text-xs hidden lg:inline">Cart</span>
                    @if($cartCount > 0)
                        <span class="absolute -top-0.5 -right-0.5 w-5 h-5 rounded-full text-[10px] font-black flex items-center justify-center border-2 border-green-700" style="background:#ff6161; color:white;">
                            {{ $cartCount }}
                        </span>
                    @endif
                </a>

            </div>

            {{-- Mobile: Cart Icon --}}
            <div class="flex md:hidden items-center gap-2 ml-auto">
                <a href="{{ route('cart') }}" class="relative p-1.5">
                    <i class="ri-shopping-cart-2-line text-white text-xl"></i>
                    @if($cartCount > 0)
                        <span class="absolute top-0 right-0 w-4 h-4 rounded-full text-[9px] font-black flex items-center justify-center" style="background:#ff6161; color:white;">{{ $cartCount }}</span>
                    @endif
                </a>
            </div>
        </div>
    </div>

    {{-- Mobile Search Bar --}}
    <div class="md:hidden px-2 pb-2">
        <form action="{{ route('products') }}" method="GET">
            <div class="flex items-center bg-white rounded-sm overflow-hidden">
                <i class="ri-search-line text-slate-400 ml-3 text-sm"></i>
                <input type="text" name="q" value="{{ request('q') }}"
                       placeholder="Search for products, brands and more"
                       class="flex-1 px-2 py-2 text-xs text-slate-800 outline-none bg-white placeholder-slate-400"/>
                <button type="submit" class="px-3 py-2" style="background:#ff6161;">
                    <i class="ri-search-line text-white text-sm"></i>
                </button>
            </div>
        </form>
    </div>
</header>
