@extends('layouts.app', ['hideNav' => true])

@section('title', 'FIINWAY — Login')

@section('content')
<div class="min-h-[80vh] flex items-center justify-center bg-[#f1f3f6] p-4 sm:p-0">
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

        <!-- Right Side: Login Form -->
        <div class="w-full md:w-[60%] p-8 sm:p-12 relative flex flex-col">
            <a href="{{ route('home') }}" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600">
                <i class="ri-close-line text-2xl"></i>
            </a>

            <!-- Mobile Only Header -->
            <div class="md:hidden mb-6 pb-4 border-b border-slate-100">
                <h1 class="text-xl font-bold text-[#212121]">Log in for best experience</h1>
                <p class="text-sm text-slate-500 mt-1">Enter your phone number to continue</p>
            </div>
            
            <div class="flex-1 mt-4">
                <form action="{{ route('otp.send') }}" method="POST" class="space-y-8">
                    @csrf
                    <div class="relative border-b border-slate-300 focus-within:border-[#006837]">
                        <input type="tel" name="phone" required pattern="[0-9]{10}" maxlength="10" 
                               class="w-full pt-4 pb-2 text-[15px] outline-none peer bg-transparent placeholder-transparent text-[#212121]" 
                               placeholder="Enter Mobile number" id="phone_input" autofocus>
                        <label for="phone_input" 
                               class="absolute left-0 top-1 text-slate-400 text-sm transition-all peer-placeholder-shown:text-[15px] peer-placeholder-shown:top-4 peer-focus:top-1 peer-focus:text-xs peer-focus:text-[#006837]">
                            Enter Mobile number
                        </label>
                    </div>

                    <div class="text-[12px] text-slate-500 pt-2">
                        By continuing, you agree to FIINWAY's <a href="#" class="text-[#006837]">Terms of Use</a> and <a href="#" class="text-[#006837]">Privacy Policy</a>.
                    </div>

                    <button type="submit" class="w-full py-3.5 bg-[#e94f1c] text-white font-medium text-[15px] shadow-sm rounded-sm hover:bg-[#cc4214] transition-colors mt-4">
                        Request OTP
                    </button>
                </form>

                <div class="mt-8 pt-6 text-center text-[13px] text-slate-500 border-t border-slate-100 hidden md:block">
                    New to FIINWAY? <a href="#" class="text-[#006837] font-medium">Create an account</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
