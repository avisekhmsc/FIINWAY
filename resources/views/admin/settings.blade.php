@extends('layouts.admin')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-slate-800">Platform Settings</h1>
</div>

<div class="grid grid-cols-2 gap-8">
    <div class="fk-card p-6">
        <h2 class="text-lg font-bold text-slate-800 mb-6 flex items-center gap-2">
            <i class="ri-money-rupee-circle-fill text-indigo-500"></i> Business Logic
        </h2>
        
        <form action="{{ route('admin.settings.update') }}" method="POST" class="space-y-4">
            @csrf
            
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Platform Commission (%)</label>
                <div class="flex items-center gap-2">
                    <input type="number" step="0.1" name="settings[commission_percent]" class="input flex-1" value="{{ \App\Models\AppSetting::get('commission_percent', 10) }}" required>
                    <span class="text-slate-500 font-bold">%</span>
                </div>
                <p class="text-xs text-slate-500 mt-1">Deducted from seller payout on every sale.</p>
            </div>
            
            <div class="pt-4">
                <label class="block text-sm font-semibold text-slate-700 mb-1">Payment Hold Period (Days)</label>
                <input type="number" name="settings[payment_hold_days]" class="input w-full" value="{{ \App\Models\AppSetting::get('payment_hold_days', 2) }}" required>
                <p class="text-xs text-slate-500 mt-1">Number of days to hold seller payment after customer receives product.</p>
            </div>
            
            <div class="pt-6">
                <button type="submit" class="fk-btn-primary">Save Business Settings</button>
            </div>
        </form>
    </div>

    <div class="fk-card p-6">
        <h2 class="text-lg font-bold text-slate-800 mb-6 flex items-center gap-2">
            <i class="ri-user-add-fill text-green-500"></i> Referral & Rewards
        </h2>
        
        <form action="{{ route('admin.settings.update') }}" method="POST" class="space-y-4">
            @csrf
            
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Referral Bonus (₹)</label>
                <div class="flex items-center gap-2">
                    <span class="text-slate-500 font-bold">₹</span>
                    <input type="number" name="settings[referral_reward]" class="input flex-1" value="{{ \App\Models\AppSetting::get('referral_reward', 50) }}" required>
                </div>
                <p class="text-xs text-slate-500 mt-1">Amount credited to both referrer and referee wallets.</p>
            </div>
            
            <div class="pt-6">
                <button type="submit" class="fk-btn-primary">Save Reward Settings</button>
            </div>
        </form>
    </div>
</div>
@endsection
