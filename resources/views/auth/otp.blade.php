@extends('layouts.app', ['hideNav' => true])

@section('title', 'FIINWAY — Verify OTP')

@section('content')
<div class="min-h-[80vh] flex items-center justify-center bg-[#f1f3f6] p-4 sm:p-0" x-data="{ otp: ['', '', '', '', '', ''] }">
    <div class="w-full max-w-[850px] bg-white rounded-sm shadow-md flex flex-col md:flex-row overflow-hidden min-h-[520px]">
        
        <!-- Left Side: Blue Banner (Hidden on Mobile) -->
        <div class="hidden md:flex w-[40%] bg-[#006837] p-10 flex-col justify-between">
            <div>
                <h1 class="text-[28px] font-medium text-white mb-4 leading-tight">Login</h1>
                <p class="text-[18px] text-white/90 leading-snug">Get access to your Orders, Wishlist and Recommendations</p>
            </div>
            <div class="flex justify-center pb-4">
                <img src="https://static-assets-web.flixcart.com/fk-p-linchpin-web/fk-cp-zion/img/login_img_c4a81e.png" alt="Login Banner" class="w-full max-w-[200px] object-contain">
            </div>
        </div>

        <!-- Right Side: OTP Form -->
        <div class="w-full md:w-[60%] p-8 sm:p-12 relative flex flex-col">
            <a href="{{ route('home') }}" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600">
                <i class="ri-close-line text-2xl"></i>
            </a>

            <!-- Mobile Only Header -->
            <div class="md:hidden mb-6 pb-4 border-b border-slate-100">
                <h1 class="text-xl font-bold text-[#212121]">Verify OTP</h1>
                <p class="text-sm text-slate-500 mt-1">Sent to {{ session('otp_phone') }}</p>
            </div>
            
            <div class="flex-1 mt-4">
                <div class="text-[14px] text-slate-600 mb-6 hidden md:block">
                    Please enter the OTP sent to <span class="font-bold text-[#212121]">{{ session('otp_phone') }}</span>. 
                    <a href="{{ route('mobile') }}" class="text-[#006837] font-medium ml-1">Change</a>
                </div>

                @if(session('demo_otp'))
                <div class="bg-green-50 border border-green-100 rounded p-3 mb-6 flex items-center gap-3">
                    <i class="ri-information-fill text-[#006837] text-xl"></i>
                    <div>
                        <p class="text-xs text-slate-500 font-medium">DEMO MODE - USE THIS OTP</p>
                        <p class="text-lg font-mono font-bold tracking-widest text-[#212121]">{{ session('demo_otp') }}</p>
                    </div>
                </div>
                @endif

                <form action="{{ route('otp.verify.post') }}" method="POST" class="space-y-8">
                    @csrf
                    
                    <div class="flex justify-between gap-2 max-w-[300px]">
                        <template x-for="(digit, index) in otp" :key="index">
                            <input type="text" 
                                maxlength="1" 
                                x-model="otp[index]"
                                @input="if($event.target.value) { $event.target.nextElementSibling?.focus() }"
                                @keydown.backspace="if(!$event.target.value) { $event.target.previousElementSibling?.focus() }"
                                class="w-10 h-10 border-b-2 border-slate-300 text-center text-xl font-medium outline-none focus:border-[#006837] text-[#212121] transition-colors bg-transparent">
                        </template>
                    </div>
                    
                    <input type="hidden" name="otp" :value="otp.join('')">

                    <button type="submit" class="w-full py-3.5 bg-[#e94f1c] text-white font-medium text-[15px] shadow-sm rounded-sm hover:bg-[#cc4214] transition-colors mt-4" :class="{ 'opacity-50 cursor-not-allowed': otp.join('').length !== 6 }">
                        Verify
                    </button>
                </form>

                <div class="mt-8 text-center text-[14px]">
                    <span class="text-slate-500">Not received your code?</span>
                    <form action="{{ route('otp.send') }}" method="POST" class="inline">
                        @csrf
                        <input type="hidden" name="phone" value="{{ session('otp_phone') }}">
                        <button type="submit" class="text-[#006837] font-medium ml-1 hover:underline">Resend OTP</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
