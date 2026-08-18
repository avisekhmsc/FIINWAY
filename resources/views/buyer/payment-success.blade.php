@extends('layouts.app', ['hideNav' => true])

@section('content')
<div class="min-h-screen bg-slate-50 flex flex-col relative overflow-hidden">
    <!-- Confetti Background -->
    <div class="absolute inset-0 z-0 opacity-40 mix-blend-multiply" style="background-image: radial-gradient(circle at center, #8b5cf6 2px, transparent 2px), radial-gradient(circle at center, #10b981 2px, transparent 2px); background-size: 30px 30px, 40px 40px; background-position: 0 0, 15px 15px;"></div>

    <div class="flex-1 flex flex-col items-center justify-center p-6 text-center relative z-10 pt-16">
        
        <!-- Success Animation Circle -->
        <div class="relative w-32 h-32 mb-8">
            <div class="absolute inset-0 bg-green-100 rounded-full animate-ping opacity-50"></div>
            <div class="absolute inset-2 bg-green-200 rounded-full animate-pulse"></div>
            <div class="absolute inset-4 bg-gradient-to-tr from-green-500 to-emerald-400 rounded-full flex items-center justify-center text-white shadow-xl shadow-green-200 z-10 transform transition-transform scale-110">
                <i class="ri-check-line text-6xl"></i>
            </div>
        </div>

        <h1 class="text-3xl font-black text-slate-900 tracking-tight mb-2">Payment Successful!</h1>
        <p class="text-slate-500 mb-8">Your order has been placed successfully.</p>

        <!-- Order Summary Card -->
        <div class="bg-white rounded-2xl w-full p-5 shadow-sm border border-slate-100 mb-8 text-left relative overflow-hidden">
            <!-- Receipt jagged edge top -->
            <div class="absolute top-0 left-0 right-0 h-2 bg-slate-50" style="mask-image: radial-gradient(circle at 4px 0, transparent 4px, black 5px); mask-size: 10px 10px; mask-position: -4px 0;"></div>
            
            <div class="pt-2">
                <div class="flex justify-between items-center mb-4">
                    <span class="text-xs text-slate-500 uppercase tracking-wider font-semibold">Amount Paid</span>
                    <span class="text-xl font-black text-slate-900">₹{{ number_format($order->total) }}</span>
                </div>
                
                <div class="space-y-2 mb-4">
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-500">Order ID</span>
                        <span class="font-semibold text-slate-800">{{ $order->order_number }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-500">Transaction ID</span>
                        <span class="font-semibold text-slate-800">{{ $order->transaction_id }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-500">Payment Method</span>
                        <span class="font-semibold text-slate-800 uppercase">{{ $order->payment_method }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-500">Date & Time</span>
                        <span class="font-semibold text-slate-800">{{ $order->paid_at->format('d M Y, h:i A') }}</span>
                    </div>
                </div>

                <div class="divider border-dashed border-slate-200"></div>

                <div class="flex items-center gap-3 mt-4">
                    <div class="w-10 h-10 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                        <i class="ri-truck-line text-lg"></i>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500 mb-0.5">Expected Delivery</p>
                        <p class="text-sm font-bold text-slate-900">
                            {{ $order->delivery_option === 'express' ? now()->addDays(2)->format('d M, l') : now()->addDays(5)->format('d M, l') }}
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- Receipt jagged edge bottom -->
            <div class="absolute bottom-0 left-0 right-0 h-2 bg-slate-50" style="mask-image: radial-gradient(circle at 4px 10px, transparent 4px, black 5px); mask-size: 10px 10px; mask-position: -4px 0;"></div>
        </div>

        <div class="w-full space-y-3">
            <a href="{{ route('orders.show', $order->id) }}" class="btn btn-primary btn-block btn-lg shadow-xl shadow-indigo-200">
                Track Order <i class="ri-map-pin-line ml-1"></i>
            </a>
            <a href="{{ route('home') }}" class="btn btn-ghost btn-block btn-lg">
                Continue Shopping
            </a>
        </div>
    </div>
</div>
@endsection
