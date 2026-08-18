<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - {{ \App\Models\AppSetting::get('app_name', 'FIINWAY') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <style>
        body { font-family: 'Roboto', sans-serif; background: #f1f3f6; }
        .fk-btn-primary { background: #e94f1c; color: #fff; font-weight: 700; padding: 0.7rem 2rem; border-radius: 2px; transition: background 0.15s; box-shadow: 0 1px 2px rgba(0,0,0,.2); letter-spacing: .04em; text-transform: uppercase; }
        .fk-btn-primary:hover { background: #cc4214; }
        .fk-btn-outline { background: #fff; color: #e94f1c; border: 1px solid #e94f1c; font-weight: 700; padding: 0.7rem 2rem; border-radius: 2px; letter-spacing: .04em; text-transform: uppercase; }
        .fk-card { background: #fff; border-radius: 2px; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.1); }
        .sidebar-link { display: flex; items-center: center; gap: 0.75rem; padding: 0.75rem 1.25rem; color: #fff; text-decoration: none; transition: all 0.2s; border-radius: 4px; margin-bottom: 0.25rem; font-size: 0.9rem; font-weight: 500; }
        .sidebar-link:hover { background: rgba(255,255,255,0.1); }
        .sidebar-link.active { background: #e94f1c; color: #fff; font-weight: 600; box-shadow: 0 2px 4px rgba(0,0,0,0.2); }
        
        .table-wrap { overflow-x: auto; background: #fff; border-radius: 2px; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.1); }
        table { width: 100%; border-collapse: collapse; }
        thead th { background: #f8fafc; padding: 0.875rem 1rem; font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; text-align: left; border-bottom: 1px solid #e2e8f0; }
        tbody td { padding: 0.875rem 1rem; border-bottom: 1px solid #f1f5f9; font-size: 0.9rem; vertical-align: middle; }
        tbody tr:hover { background: #f8fafc; }
        
        .stat-card { background: #fff; border-radius: 2px; padding: 1.5rem; box-shadow: 0 1px 2px rgba(0,0,0,0.1); display: flex; align-items: center; gap: 1rem; transition: all 0.3s; }
        .stat-icon { width: 3rem; height: 3rem; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; flex-shrink: 0; }
        .stat-value { font-size: 1.75rem; font-weight: 800; line-height: 1; color: #212121; }
        .stat-label { font-size: 0.85rem; color: #878787; font-weight: 500; margin-top: 0.25rem; }
    </style>
</head>
<body class="antialiased min-h-screen flex" style="background:#f1f3f6;">

    {{-- Sidebar --}}
    <aside class="w-[260px] flex-shrink-0 min-h-screen sticky top-0 flex flex-col shadow-lg z-50" style="background: #006837;">
        <div class="p-6 mb-2 border-b border-white/10 text-center">
            <img src="{{ asset('logo.png') }}" alt="FIINWAY Logo" class="h-10 object-contain mx-auto mb-3 brightness-200 contrast-200 drop-shadow-md">
            <p class="text-[10px] text-green-200 uppercase tracking-[0.2em] font-bold">Admin Portal</p>
        </div>

        <div class="px-4 py-4 flex-1 overflow-y-auto">
            <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="ri-dashboard-fill"></i> Dashboard
            </a>
            <a href="{{ route('admin.products') }}" class="sidebar-link {{ request()->routeIs('admin.products') ? 'active' : '' }}">
                <i class="ri-shopping-bag-3-fill"></i> Products
            </a>
            <a href="{{ route('admin.orders') }}" class="sidebar-link {{ request()->routeIs('admin.orders') ? 'active' : '' }}">
                <i class="ri-shopping-cart-fill"></i> Orders
            </a>
            <a href="{{ route('admin.users') }}" class="sidebar-link {{ request()->routeIs('admin.users') ? 'active' : '' }}">
                <i class="ri-group-fill"></i> Users
            </a>
            <a href="{{ route('admin.payouts') }}" class="sidebar-link {{ request()->routeIs('admin.payouts') ? 'active' : '' }}">
                <i class="ri-wallet-3-fill"></i> Payouts
            </a>
            <a href="{{ route('admin.returns') }}" class="sidebar-link {{ request()->routeIs('admin.returns') ? 'active' : '' }}">
                <i class="ri-arrow-go-back-fill"></i> Returns
            </a>
            <a href="{{ route('admin.refunds') }}" class="sidebar-link {{ request()->routeIs('admin.refunds') ? 'active' : '' }}">
                <i class="ri-refund-2-fill"></i> Refunds
            </a>
            <a href="{{ route('admin.categories') }}" class="sidebar-link {{ request()->routeIs('admin.categories') ? 'active' : '' }}">
                <i class="ri-list-check"></i> Categories
            </a>
            <a href="{{ route('admin.referrals') }}" class="sidebar-link {{ request()->routeIs('admin.referrals') ? 'active' : '' }}">
                <i class="ri-user-add-fill"></i> Referrals
            </a>
            <a href="{{ route('admin.settings') }}" class="sidebar-link {{ request()->routeIs('admin.settings') ? 'active' : '' }}">
                <i class="ri-settings-3-fill"></i> Settings
            </a>
        </div>

        <div class="p-4 border-t border-white/10 bg-black/10">
            <a href="{{ route('home') }}" class="flex items-center gap-2 px-3 py-2 text-green-200 hover:text-white text-sm font-medium transition-colors mb-2">
                <i class="ri-external-link-line"></i> Go to Website
            </a>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full flex items-center justify-center gap-2 px-4 py-2.5 rounded bg-white/10 hover:bg-[#e94f1c] text-white text-sm font-bold transition-colors">
                    <i class="ri-logout-box-r-line"></i> Logout
                </button>
            </form>
        </div>
    </aside>

    {{-- Main Content Area --}}
    <main class="flex-1 min-w-0 flex flex-col min-h-screen">
        
        {{-- Header Bar --}}
        <header class="bg-white shadow-sm h-16 flex items-center justify-between px-8 sticky top-0 z-40">
            <h2 class="text-xl font-bold text-[#212121]">@yield('header_title', 'Overview')</h2>
            <div class="flex items-center gap-4 text-sm font-medium text-[#878787]">
                <span><i class="ri-calendar-line"></i> {{ now()->format('d M Y') }}</span>
                <div class="w-8 h-8 rounded-full bg-green-100 text-[#006837] flex items-center justify-center font-bold border border-green-200">
                    A
                </div>
            </div>
        </header>

        {{-- Content Padding --}}
        <div class="p-8 flex-1 overflow-x-hidden">
            {{-- Flash Messages --}}
            @if(session('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" class="mb-6 flex items-center gap-3 p-4 rounded-sm bg-[#e8f5e9] border border-[#a5d6a7] text-[#2e7d32] shadow-sm">
                <i class="ri-checkbox-circle-fill text-xl"></i>
                <span class="flex-1 font-medium">{{ session('success') }}</span>
                <button @click="show = false"><i class="ri-close-line text-xl opacity-70 hover:opacity-100"></i></button>
            </div>
            @endif

            @if($errors->any())
            <div x-data="{ show: true }" x-show="show" class="mb-6 flex items-start gap-3 p-4 rounded-sm bg-[#ffebee] border border-[#ef9a9a] text-[#c62828] shadow-sm">
                <i class="ri-error-warning-fill text-xl mt-0.5"></i>
                <div class="flex-1 font-medium space-y-1">
                    @foreach($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
                <button @click="show = false"><i class="ri-close-line text-xl opacity-70 hover:opacity-100"></i></button>
            </div>
            @endif

            @yield('content')
        </div>
    </main>

</body>
</html>
