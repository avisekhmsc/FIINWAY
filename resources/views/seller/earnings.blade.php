@extends('layouts.app')

@section('content')
<div class="bg-[#f1f3f6] min-h-screen pb-16">
    <!-- Header -->
    <div class="bg-[#172337] text-white p-6 pb-12 shadow-sm relative overflow-hidden">
        <div class="absolute right-0 top-0 opacity-[0.03]">
            <i class="ri-money-rupee-circle-fill text-[150px]"></i>
        </div>
        <div class="max-w-4xl mx-auto relative z-10">
            <div class="flex items-center gap-3 mb-6">
                <a href="{{ route('seller.dashboard') }}" class="w-9 h-9 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center transition-colors">
                    <i class="ri-arrow-left-line text-lg"></i>
                </a>
                <h1 class="text-xl font-medium">Earnings & Payouts</h1>
            </div>
            
            <p class="text-green-200 text-xs font-medium uppercase tracking-wider mb-1">Available for Withdrawal</p>
            <h2 class="text-4xl font-bold mb-4">₹{{ number_format($stats['released_amount']) }}</h2>
            
            <div class="inline-flex items-center gap-2 bg-white/10 rounded-sm px-4 py-2 text-sm font-medium border border-white/10">
                <i class="ri-time-line text-[#e94f1c]"></i>
                <span>Pending: ₹{{ number_format($stats['pending_amount']) }}</span>
            </div>
        </div>
    </div>

    <div class="px-2 sm:px-4 -mt-6 relative z-20 space-y-4 max-w-4xl mx-auto">
        
        <!-- Summary Stats -->
        <div class="bg-white rounded-sm p-4 shadow-sm border border-slate-100">
            <h3 class="text-sm font-medium text-[#212121] mb-4">Business Summary</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-y-4 gap-x-2 text-sm border-t border-slate-100 pt-4">
                <div>
                    <p class="text-[#878787] mb-1 text-xs uppercase tracking-wide">Total Sales</p>
                    <p class="font-bold text-[#212121]">₹{{ number_format($stats['total_sales']) }}</p>
                </div>
                <div>
                    <p class="text-[#878787] mb-1 text-xs uppercase tracking-wide">Platform Fee</p>
                    <p class="font-bold text-red-500">-₹{{ number_format($stats['total_commission']) }}</p>
                </div>
                <div>
                    <p class="text-[#878787] mb-1 text-xs uppercase tracking-wide">Referral Bonus</p>
                    <p class="font-bold text-[#388e3c]">+₹{{ number_format($stats['referral_earning']) }}</p>
                </div>
                <div>
                    <p class="text-[#878787] mb-1 text-xs uppercase tracking-wide">Total Earnings</p>
                    <p class="font-bold text-[#006837]">₹{{ number_format($stats['total_earning']) }}</p>
                </div>
            </div>
        </div>

        <!-- 2 Day Hold Info -->
        <div class="bg-orange-50 border border-orange-200 rounded-sm p-4 flex gap-3">
            <i class="ri-shield-keyhole-line text-2xl text-[#e94f1c]"></i>
            <div>
                <h4 class="text-sm font-medium text-orange-900 mb-1">2-Day Payment Protection</h4>
                <p class="text-xs text-orange-800 leading-relaxed">When a buyer confirms receiving your product, payments are held for {{ \App\Models\AppSetting::get('payment_hold_days', 2) }} days for security before being released to your wallet.</p>
            </div>
        </div>

        <!-- Transactions -->
        <div class="bg-white rounded-sm shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-4 border-b border-slate-100">
                <h3 class="text-sm font-medium text-[#212121]">Recent Earnings</h3>
            </div>
            
            <div class="divide-y divide-slate-50">
                @forelse($earnings as $earning)
                <div class="p-4 flex items-center justify-between hover:bg-slate-50">
                    <div>
                        <p class="text-sm font-medium text-[#212121] mb-1 line-clamp-1 pr-4">{{ $earning->orderItem->product_name ?? 'Order Earning' }}</p>
                        <p class="text-xs text-[#878787]">Ord: {{ $earning->order->order_number }} • {{ $earning->created_at->format('d M') }}</p>
                    </div>
                    <div class="text-right shrink-0">
                        <p class="text-sm font-bold text-[#388e3c] mb-1">+₹{{ number_format($earning->seller_amount) }}</p>
                        <span class="text-[10px] font-bold uppercase tracking-wider {{ $earning->status === 'released' ? 'text-[#388e3c]' : ($earning->status === 'pending' ? 'text-[#006837]' : 'text-[#e94f1c]') }}">
                            {{ str_replace('_', ' ', $earning->status) }}
                        </span>
                    </div>
                </div>
                @empty
                <div class="p-16 text-center">
                    <i class="ri-wallet-3-line text-5xl text-slate-200 block mb-4"></i>
                    <p class="text-sm font-medium text-[#212121]">No earnings recorded yet.</p>
                </div>
                @endforelse
            </div>
            @if($earnings->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $earnings->links('pagination::tailwind') }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
