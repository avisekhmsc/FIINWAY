@extends('layouts.app')

@section('title', 'My Account — FIINWAY')

@section('content')
<style>
.profile-input {
    width: 100%;
    border: none;
    border-bottom: 1.5px solid #e0e0e0;
    padding: 8px 0 6px;
    font-size: 14px;
    color: #212121;
    background: transparent;
    outline: none;
    transition: border-color 0.2s;
}
.profile-input:focus { border-bottom-color: #006837; }
.profile-input:disabled { color: #878787; border-bottom-color: transparent; background: transparent; }
.faq-item { border-bottom: 1px solid #f0f0f0; padding-bottom: 16px; margin-bottom: 16px; }
.faq-item:last-child { border-bottom: none; margin-bottom: 0; }
</style>

<div style="background:#f1f3f6;" class="min-h-screen pb-8">
<div class="max-w-[1050px] mx-auto px-4 pt-6 pb-12">

    <div class="flex flex-col md:flex-row gap-4 items-start">

        {{-- ===== SIDEBAR ===== --}}
        <aside class="w-full md:w-[240px] shrink-0 space-y-4">

            {{-- User card --}}
            <div class="bg-white shadow-sm rounded-sm">
                <div class="p-4 border-b border-[#f0f0f0] flex items-center gap-3">
                    <div class="w-12 h-12 rounded-full overflow-hidden shrink-0 border-2 border-[#006837]/20">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=006837&color=fff&size=96" class="w-full h-full object-cover">
                    </div>
                    <div class="min-w-0">
                        <p class="text-[11px] text-[#878787] leading-tight">Hello,</p>
                        <p class="font-bold text-[#212121] text-[15px] truncate leading-tight">{{ $user->name }}</p>
                    </div>
                </div>

                {{-- Nav sections --}}
                @php
                $navSections = [
                    ['icon'=>'ri-folder-5-fill','label'=>'MY ORDERS','color'=>'#006837','links'=>[
                        ['href'=>route('orders'),'label'=>'My Orders'],
                    ]],
                    ['icon'=>'ri-user-fill','label'=>'ACCOUNT SETTINGS','color'=>'#e94f1c','links'=>[
                        ['href'=>route('profile'),'label'=>'Profile Information','active'=>true],
                        ['href'=>'#','label'=>'Manage Addresses'],
                    ]],
                    ['icon'=>'ri-wallet-3-fill','label'=>'PAYMENTS','color'=>'#f59e0b','links'=>[
                        ['href'=>'#','label'=>'Saved Cards & UPI'],
                    ]],
                    ['icon'=>'ri-heart-3-fill','label'=>'MY STUFF','color'=>'#ec4899','links'=>[
                        ['href'=>route('wishlist'),'label'=>'My Wishlist'],
                        ['href'=>route('returns.index'),'label'=>'My Returns'],
                    ]],
                ];
                @endphp

                @foreach($navSections as $section)
                    <div class="border-b border-[#f0f0f0]">
                        <div class="px-4 pt-4 pb-2 flex items-center gap-3">
                            <i class="{{ $section['icon'] }} text-base" style="color:{{ $section['color'] }}"></i>
                            <span class="text-[11px] font-bold text-[#878787] uppercase tracking-wider">{{ $section['label'] }}</span>
                        </div>
                        <div class="pb-2">
                            @foreach($section['links'] as $link)
                                <a href="{{ $link['href'] }}"
                                   class="block py-2 pl-10 pr-4 text-[13px] transition-colors {{ !empty($link['active']) ? 'text-[#006837] font-semibold bg-[#f0faf5]' : 'text-[#212121] hover:text-[#006837] hover:bg-[#f9f9f9]' }}">
                                    {{ $link['label'] }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endforeach

                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full px-4 py-3 flex items-center gap-3 text-[11px] font-bold text-[#878787] uppercase tracking-wider hover:bg-[#f9f9f9] transition-colors text-left">
                        <i class="ri-logout-box-r-fill text-base text-[#878787]"></i> LOGOUT
                    </button>
                </form>
            </div>
        </aside>

        {{-- ===== MAIN CONTENT ===== --}}
        <main class="flex-1 min-w-0 space-y-4">

            {{-- Profile Info Card --}}
            <div class="bg-white shadow-sm rounded-sm" x-data="{ editing: false }">
                <div class="px-6 pt-5 pb-4 border-b border-[#f0f0f0] flex items-center justify-between">
                    <h1 class="text-[16px] font-semibold text-[#212121]">Personal Information</h1>
                    <button type="button" @click="editing = !editing"
                        class="text-[13px] font-semibold transition-colors"
                        :class="editing ? 'text-red-500 hover:text-red-700' : 'text-[#006837] hover:text-green-800'"
                        x-text="editing ? 'Cancel' : 'Edit'">
                    </button>
                </div>

                <form action="{{ route('profile.update') }}" method="POST" class="px-6 py-6">
                    @csrf

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-12 gap-y-6 max-w-[600px]">
                        <div>
                            <label class="block text-[11px] text-[#878787] mb-1.5 uppercase tracking-wider font-semibold">Full Name</label>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}"
                                class="profile-input" :disabled="!editing" required>
                        </div>
                        <div>
                            <label class="block text-[11px] text-[#878787] mb-1.5 uppercase tracking-wider font-semibold">Email Address</label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}"
                                class="profile-input" :disabled="!editing" required>
                        </div>
                        <div>
                            <label class="block text-[11px] text-[#878787] mb-1.5 uppercase tracking-wider font-semibold">Mobile Number</label>
                            <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                                class="profile-input" :disabled="!editing" maxlength="10" required>
                        </div>
                        <div>
                            <label class="block text-[11px] text-[#878787] mb-1.5 uppercase tracking-wider font-semibold">Gender</label>
                            <div class="flex items-center gap-5 pt-2">
                                <label class="flex items-center gap-1.5 text-sm text-[#212121] cursor-pointer">
                                    <input type="radio" name="gender" value="male" class="accent-[#006837]" :disabled="!editing" checked> Male
                                </label>
                                <label class="flex items-center gap-1.5 text-sm text-[#212121] cursor-pointer">
                                    <input type="radio" name="gender" value="female" class="accent-[#006837]" :disabled="!editing"> Female
                                </label>
                            </div>
                        </div>
                        <div>
                            <label class="block text-[11px] text-[#878787] mb-1.5 uppercase tracking-wider font-semibold">City</label>
                            <input type="text" name="city" value="{{ old('city', $user->city) }}"
                                class="profile-input" :disabled="!editing">
                        </div>
                        <div>
                            <label class="block text-[11px] text-[#878787] mb-1.5 uppercase tracking-wider font-semibold">State</label>
                            <input type="text" name="state" value="{{ old('state', $user->state) }}"
                                class="profile-input" :disabled="!editing">
                        </div>
                        <div>
                            <label class="block text-[11px] text-[#878787] mb-1.5 uppercase tracking-wider font-semibold">Pincode</label>
                            <input type="text" name="pincode" value="{{ old('pincode', $user->pincode) }}"
                                class="profile-input" :disabled="!editing">
                        </div>
                    </div>

                    <div class="mt-8 flex items-center gap-4" x-show="editing" x-cloak>
                        <button type="submit"
                            style="background:#e94f1c;"
                            class="px-10 py-2.5 text-white font-bold text-sm tracking-wider uppercase hover:opacity-90 transition-opacity rounded-sm shadow-sm">
                            SAVE
                        </button>
                        <button type="button" @click="editing = false"
                            class="px-8 py-2.5 border border-slate-300 text-[#212121] font-bold text-sm uppercase rounded-sm hover:bg-slate-50">
                            CANCEL
                        </button>
                    </div>
                </form>
            </div>

            {{-- Change Password Card --}}
            <div class="bg-white shadow-sm rounded-sm" x-data="{ showPwd: false }">
                <div class="px-6 pt-5 pb-4 border-b border-[#f0f0f0] flex items-center justify-between">
                    <h2 class="text-[16px] font-semibold text-[#212121]">Change Password</h2>
                    <button type="button" @click="showPwd = !showPwd"
                        class="text-[13px] font-semibold text-[#006837] hover:text-green-800"
                        x-text="showPwd ? 'Cancel' : 'Update'">
                    </button>
                </div>
                <div x-show="showPwd" x-cloak class="px-6 py-6">
                    <form action="{{ route('profile.update') }}" method="POST" class="max-w-[480px] space-y-5">
                        @csrf
                        {{-- hidden fields to pass existing data through --}}
                        <input type="hidden" name="name" value="{{ $user->name }}">
                        <input type="hidden" name="email" value="{{ $user->email }}">
                        <input type="hidden" name="phone" value="{{ $user->phone }}">
                        <div>
                            <label class="block text-[11px] text-[#878787] mb-1.5 uppercase tracking-wider font-semibold">New Password</label>
                            <input type="password" name="password" class="profile-input" placeholder="Minimum 8 characters">
                        </div>
                        <div>
                            <label class="block text-[11px] text-[#878787] mb-1.5 uppercase tracking-wider font-semibold">Confirm New Password</label>
                            <input type="password" name="password_confirmation" class="profile-input" placeholder="Re-enter password">
                        </div>
                        <div class="flex gap-4 pt-2">
                            <button type="submit"
                                style="background:#e94f1c;"
                                class="px-10 py-2.5 text-white font-bold text-sm uppercase rounded-sm hover:opacity-90 shadow-sm">
                                UPDATE PASSWORD
                            </button>
                        </div>
                    </form>
                </div>
                <div x-show="!showPwd" class="px-6 py-4 text-[13px] text-[#878787]">
                    Click Update to change your account password.
                </div>
            </div>

            {{-- Refer & Earn Card --}}
            <div class="bg-white shadow-sm rounded-sm overflow-hidden">
                <div class="flex flex-col sm:flex-row items-stretch">
                    <div class="flex-1 px-6 py-5">
                        <div class="flex items-center gap-2 mb-1">
                            <i class="ri-gift-fill text-xl text-[#e94f1c]"></i>
                            <h3 class="text-[15px] font-bold text-[#212121]">Refer & Earn</h3>
                        </div>
                        <p class="text-[13px] text-[#878787] mb-4">Share your code with friends. When they place their first order, you both earn ₹50 reward credits.</p>
                        <div class="flex items-center gap-3">
                            <div class="bg-[#f0faf5] border-2 border-dashed border-[#006837] px-5 py-2 rounded-sm font-black text-xl text-[#212121] tracking-widest select-all" id="referral-code">
                                {{ $user->referral_code ?? 'N/A' }}
                            </div>
                            <button onclick="navigator.clipboard.writeText('{{ $user->referral_code }}').then(()=>{ this.innerText='Copied!'; setTimeout(()=>this.innerText='Copy',2000) })"
                                class="px-4 py-2 border border-[#006837] text-[#006837] text-sm font-bold rounded-sm hover:bg-[#f0faf5] transition-colors">
                                Copy
                            </button>
                        </div>
                    </div>
                    <div class="hidden sm:flex items-center justify-center w-[160px] shrink-0" style="background: linear-gradient(135deg, #006837, #00a859);">
                        <div class="text-center text-white p-4">
                            <i class="ri-user-add-fill text-4xl block mb-1 opacity-90"></i>
                            <p class="text-[11px] font-bold uppercase tracking-wider opacity-80">Earn ₹50</p>
                            <p class="text-[11px] opacity-60">per referral</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Account Stats Card --}}
            <div class="bg-white shadow-sm rounded-sm px-6 py-5">
                <h3 class="text-[15px] font-bold text-[#212121] mb-4">Account Summary</h3>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    @php
                        $orderCount   = $stats['orders'];
                        $wishlistCount = $stats['wishlist'];
                        $reviewCount  = $stats['reviews'];
                        $returnCount  = $stats['returns'];
                    @endphp
                    @foreach([
                        ['icon'=>'ri-shopping-bag-3-fill', 'val'=>$orderCount,   'label'=>'Orders',    'color'=>'#006837'],
                        ['icon'=>'ri-heart-3-fill',         'val'=>$wishlistCount,'label'=>'Wishlist',  'color'=>'#e94f1c'],
                        ['icon'=>'ri-star-fill',             'val'=>$reviewCount,  'label'=>'Reviews',   'color'=>'#f59e0b'],
                        ['icon'=>'ri-arrow-go-back-fill',   'val'=>$returnCount,  'label'=>'Returns',   'color'=>'#64748b'],
                    ] as $stat)
                    <div class="flex items-center gap-3 p-3 bg-[#f9f9f9] rounded-sm">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center shrink-0" style="background:{{ $stat['color'] }}18;">
                            <i class="{{ $stat['icon'] }} text-base" style="color:{{ $stat['color'] }}"></i>
                        </div>
                        <div>
                            <p class="text-xl font-black text-[#212121] leading-tight">{{ $stat['val'] }}</p>
                            <p class="text-[11px] text-[#878787]">{{ $stat['label'] }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- FAQs --}}
            <div class="bg-white shadow-sm rounded-sm px-6 py-5">
                <h3 class="text-[15px] font-bold text-[#212121] mb-5">Frequently Asked Questions</h3>
                <div class="space-y-0 text-[13px] text-[#878787] leading-relaxed">
                    <div class="faq-item">
                        <p class="font-semibold text-[#212121] mb-1">What happens when I update my email or mobile number?</p>
                        <p>Your login credentials update immediately. All account communication will go to the new email or number.</p>
                    </div>
                    <div class="faq-item">
                        <p class="font-semibold text-[#212121] mb-1">When will my FIINWAY account reflect the new email/mobile?</p>
                        <p>It happens as soon as you save the changes. Your order history and saved information remain untouched.</p>
                    </div>
                    <div class="faq-item">
                        <p class="font-semibold text-[#212121] mb-1">Can I have multiple FIINWAY accounts?</p>
                        <p>No. Each mobile number and email address can only be linked to one FIINWAY account.</p>
                    </div>
                </div>
            </div>

        </main>
    </div>
</div>
</div>
@endsection
