@extends('layouts.app', ['hideNav' => true])

@section('title', 'FIINWAY — Verify OTP')

@section('content')
<div class="min-h-[80vh] flex items-center justify-center bg-[#f1f3f6] p-4 sm:p-0"
     x-data="{
        digits: ['','','','','',''],
        error: '{{ $errors->first('otp') }}',
        loading: false,
        get fullOtp() { return this.digits.join('') },
        get isComplete() { return this.fullOtp.length === 6 },
        focusNext(index) {
            if (index < 5) {
                const inputs = this.$refs.otpBox.querySelectorAll('input[data-slot]');
                inputs[index + 1]?.focus();
            }
        },
        focusPrev(index) {
            if (index > 0) {
                const inputs = this.$refs.otpBox.querySelectorAll('input[data-slot]');
                inputs[index - 1]?.focus();
            }
        },
        handleInput(index, event) {
            const val = event.target.value.replace(/\D/g, '');
            // Handle paste of full OTP
            if (val.length > 1) {
                const parts = val.slice(0, 6).split('');
                parts.forEach((c, i) => { if (i < 6) this.digits[i] = c; });
                const inputs = this.$refs.otpBox.querySelectorAll('input[data-slot]');
                inputs[Math.min(parts.length - 1, 5)]?.focus();
                return;
            }
            this.digits[index] = val;
            if (val) this.focusNext(index);
        },
        handleBackspace(index, event) {
            if (!this.digits[index] && index > 0) {
                this.digits[index - 1] = '';
                this.focusPrev(index);
            }
        },
        submit() {
            if (!this.isComplete) return;
            this.loading = true;
            this.$refs.otpForm.submit();
        }
     }">

    <div class="w-full max-w-[850px] bg-white rounded-sm shadow-md flex flex-col md:flex-row overflow-hidden min-h-[520px]">
        
        {{-- Left banner --}}
        <div class="hidden md:flex w-[40%] bg-[#006837] p-10 flex-col justify-between">
            <div>
                <h1 class="text-[28px] font-medium text-white mb-4 leading-tight">Verify OTP</h1>
                <p class="text-[18px] text-white/90 leading-snug">Get access to your Orders, Wishlist and Recommendations</p>
            </div>
            <div class="flex justify-center pb-4">
                <img src="https://static-assets-web.flixcart.com/fk-p-linchpin-web/fk-cp-zion/img/login_img_c4a81e.png" 
                     alt="Login Banner" class="w-full max-w-[200px] object-contain">
            </div>
        </div>

        {{-- Right: OTP Form --}}
        <div class="w-full md:w-[60%] p-8 sm:p-12 relative flex flex-col">
            <a href="{{ route('home') }}" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600">
                <i class="ri-close-line text-2xl"></i>
            </a>

            {{-- Mobile Header --}}
            <div class="md:hidden mb-6 pb-4 border-b border-slate-100">
                <h1 class="text-xl font-bold text-[#212121]">Verify OTP</h1>
                <p class="text-sm text-slate-500 mt-1">Sent to {{ $phone }}</p>
            </div>

            <div class="flex-1 mt-4">
                {{-- Phone hint + change --}}
                <div class="text-[14px] text-slate-600 mb-6 hidden md:block">
                    OTP sent to <span class="font-bold text-[#212121]">+91 {{ $phone }}</span>.
                    <a href="{{ route('mobile') }}" class="text-[#006837] font-medium ml-1 hover:underline">Change</a>
                </div>

                {{-- ✅ DEMO OTP Banner — persists across refreshes --}}
                @if($demo_otp)
                <div class="bg-amber-50 border border-amber-200 rounded-sm p-4 mb-6">
                    <div class="flex items-center gap-3">
                        <i class="ri-shield-keyhole-fill text-amber-500 text-xl shrink-0"></i>
                        <div>
                            <p class="text-[11px] font-bold text-amber-600 uppercase tracking-wider mb-0.5">Demo Mode — Your OTP</p>
                            <p class="text-2xl font-mono font-black tracking-[0.4em] text-[#212121]">{{ $demo_otp }}</p>
                        </div>
                        {{-- Auto-fill button --}}
                        <button type="button" class="ml-auto text-[11px] font-bold text-[#006837] border border-[#006837] px-3 py-1 rounded-sm hover:bg-green-50"
                            @click="
                                '{{ $demo_otp }}'.split('').forEach((c,i) => digits[i] = c);
                                $refs.otpBox.querySelectorAll('input[data-slot]')[5]?.focus();
                            ">
                            Auto Fill
                        </button>
                    </div>
                </div>
                @endif

                {{-- ✅ Error message shown prominently --}}
                <div x-show="error" x-cloak
                     class="bg-red-50 border border-red-200 rounded-sm px-4 py-3 mb-5 flex items-center gap-3 text-[13px] text-red-700">
                    <i class="ri-error-warning-fill text-red-500 text-base shrink-0"></i>
                    <span x-text="error"></span>
                </div>

                @error('otp')
                <div class="bg-red-50 border border-red-200 rounded-sm px-4 py-3 mb-5 flex items-center gap-3 text-[13px] text-red-700">
                    <i class="ri-error-warning-fill text-red-500 text-base shrink-0"></i>
                    <span>{{ $message }}</span>
                </div>
                @enderror

                {{-- OTP Input Boxes --}}
                <form action="{{ route('otp.verify.post') }}" method="POST" x-ref="otpForm" @submit.prevent="submit">
                    @csrf

                    <p class="text-[13px] text-slate-500 mb-4 font-medium">Enter 6-digit OTP</p>

                    {{-- ✅ No Alpine x-for — plain static inputs, reliable binding --}}
                    <div class="flex gap-3 mb-8" x-ref="otpBox">
                        @for($i = 0; $i < 6; $i++)
                        <input type="text" inputmode="numeric" pattern="[0-9]*"
                               data-slot="{{ $i }}"
                               maxlength="1"
                               :value="digits[{{ $i }}]"
                               @input="handleInput({{ $i }}, $event)"
                               @keydown.backspace="handleBackspace({{ $i }}, $event)"
                               @keydown.left="focusPrev({{ $i }})"
                               @keydown.right="focusNext({{ $i }})"
                               @paste.prevent="
                                   const txt = $event.clipboardData.getData('text').replace(/\D/g,'').slice(0,6);
                                   txt.split('').forEach((c,i) => digits[i] = c);
                                   $refs.otpBox.querySelectorAll('input[data-slot]')[Math.min(txt.length-1,5)]?.focus();
                               "
                               class="w-11 h-12 border-b-2 text-center text-xl font-bold outline-none transition-all bg-transparent text-[#212121]
                                      {{ $errors->has('otp') ? 'border-red-400' : 'border-slate-300 focus:border-[#006837]' }}"
                               :class="{
                                   'border-[#006837] text-[#006837]': digits[{{ $i }}],
                                   'border-red-400': error && !digits[{{ $i }}]
                               }">
                        @endfor
                    </div>

                    {{-- Hidden input carries the full OTP string to the server --}}
                    <input type="hidden" name="otp" :value="fullOtp">

                    {{-- Submit Button --}}
                    <button type="submit"
                        :disabled="!isComplete || loading"
                        :class="isComplete && !loading
                            ? 'bg-[#e94f1c] hover:bg-[#cc4214] cursor-pointer'
                            : 'bg-slate-200 text-slate-400 cursor-not-allowed'"
                        class="w-full py-3.5 text-white font-semibold text-[15px] rounded-sm transition-all duration-200 flex items-center justify-center gap-2">
                        <span x-show="!loading">Verify OTP</span>
                        <span x-show="loading" x-cloak class="flex items-center gap-2">
                            <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                            </svg>
                            Verifying...
                        </span>
                    </button>
                </form>

                {{-- Resend --}}
                <div class="mt-8 text-center text-[14px]" x-data="{ countdown: 0 }"
                     x-init="
                         countdown = 30;
                         const t = setInterval(() => { if(countdown > 0) countdown--; else clearInterval(t); }, 1000);
                     ">
                    <span class="text-slate-500">Didn't receive the OTP?</span>
                    <template x-if="countdown > 0">
                        <span class="text-slate-400 ml-1">Resend in <span x-text="countdown" class="font-bold text-[#006837]"></span>s</span>
                    </template>
                    <template x-if="countdown === 0">
                        <form action="{{ route('otp.send') }}" method="POST" class="inline">
                            @csrf
                            <input type="hidden" name="phone" value="{{ $phone }}">
                            <button type="submit" class="text-[#006837] font-semibold ml-1 hover:underline">Resend OTP</button>
                        </form>
                    </template>
                </div>

                {{-- Expiry notice --}}
                <p class="text-center text-[11px] text-slate-400 mt-4">OTP valid for 10 minutes</p>
            </div>
        </div>
    </div>
</div>
@endsection
