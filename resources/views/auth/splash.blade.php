@extends('layouts.app', ['hideNav' => true, 'hideHeader' => true, 'hideFooter' => true])

@section('title', 'FIINWAY')

@section('content')
<style>
    @keyframes pulse-grow {
        0% { transform: scale(0.8); opacity: 0; }
        50% { opacity: 1; }
        100% { transform: scale(1.1); opacity: 1; }
    }
    .fk-splash-bg {
        background-color: white; /* Clean white background for the logo to shine */
    }
    .fk-logo-animate {
        animation: pulse-grow 1s ease-out forwards;
    }
</style>

<div class="fixed inset-0 flex items-center justify-center fk-splash-bg z-[100]">
    <div class="fk-logo-animate flex flex-col items-center">
        <!-- Logo Image -->
        <img src="{{ asset('logo.png') }}" alt="FIINWAY Logo" class="w-auto h-24 sm:h-32 object-contain mb-4">
        
        <!-- Animated dots -->
        <div class="flex gap-2 mt-4">
            <div class="w-2.5 h-2.5 rounded-full bg-[#006837] animate-bounce" style="animation-delay: 0ms"></div>
            <div class="w-2.5 h-2.5 rounded-full bg-[#e94f1c] animate-bounce" style="animation-delay: 150ms"></div>
            <div class="w-2.5 h-2.5 rounded-full bg-[#006837] animate-bounce" style="animation-delay: 300ms"></div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        setTimeout(() => {
            window.location.href = "{{ route('home') }}";
        }, 2000);
    });
</script>
@endsection
