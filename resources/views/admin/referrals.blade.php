@extends('layouts.admin')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-slate-800">Referrals & Rewards</h1>
</div>

<div class="grid grid-cols-3 gap-6 mb-8">
    <div class="stat-card">
        <div class="stat-icon bg-green-100 text-green-600"><i class="ri-user-add-fill"></i></div>
        <div>
            <div class="stat-value">{{ $referrals->total() }}</div>
            <div class="stat-label">Total Referrals</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon bg-indigo-100 text-[#006837]"><i class="ri-wallet-3-fill"></i></div>
        <div>
            <div class="stat-value">₹{{ number_format($referrals->sum('reward_amount') * 2) }}</div>
            <div class="stat-label">Total Amount Distributed</div>
        </div>
    </div>
</div>

<div class="fk-card p-0">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Referrer (Sender)</th>
                    <th>Referee (Joined)</th>
                    <th>Reward per User</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($referrals as $ref)
                <tr>
                    <td class="text-sm text-slate-500">{{ $ref->created_at->format('d M Y, h:i A') }}</td>
                    <td>
                        <p class="font-bold text-slate-800">{{ $ref->referrer->name }}</p>
                        <p class="text-xs text-slate-500">{{ $ref->referrer->phone }}</p>
                    </td>
                    <td>
                        <p class="font-bold text-slate-800">{{ $ref->referred->name }}</p>
                        <p class="text-xs text-slate-500">{{ $ref->referred->phone }}</p>
                    </td>
                    <td class="font-bold text-green-600">₹{{ number_format($ref->reward_amount) }}</td>
                    <td>
                        <span class="badge {{ $ref->status === 'completed' ? 'badge-success' : 'badge-warning' }}">{{ ucfirst($ref->status) }}</span>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center py-8 text-slate-500">No referrals found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($referrals->hasPages())
    <div class="p-4 border-t border-slate-100">
        {{ $referrals->links('pagination::tailwind') }}
    </div>
    @endif
</div>
@endsection
