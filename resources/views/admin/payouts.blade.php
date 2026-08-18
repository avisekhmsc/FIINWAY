@extends('layouts.admin')

@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Seller Payouts</h1>
        <p class="text-slate-500 text-sm mt-1">Manage pending payouts to sellers.</p>
    </div>
</div>

<div class="bg-indigo-50 border border-indigo-100 rounded-xl p-4 mb-6 flex gap-3">
    <i class="ri-information-fill text-indigo-500 text-xl"></i>
    <div>
        <h4 class="font-bold text-indigo-900 text-sm mb-1">How Payouts Work</h4>
        <p class="text-xs text-indigo-800">When an order is delivered and confirmed by the customer, the seller's earnings enter a <strong>{{ \App\Models\AppSetting::get('payment_hold_days', 2) }}-day hold</strong> period. After this hold, earnings are automatically marked as "Released". If you need to manually release early, you can do so here.</p>
    </div>
</div>

<div class="fk-card p-0">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Seller</th>
                    <th>Order</th>
                    <th>Amount</th>
                    <th>Hold Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($earnings as $earning)
                <tr>
                    <td>
                        <p class="font-bold text-slate-800">{{ $earning->seller->name }}</p>
                        <p class="text-xs text-slate-500">+91 {{ $earning->seller->phone }}</p>
                    </td>
                    <td>
                        <p class="font-semibold text-slate-700">{{ $earning->order->order_number }}</p>
                        <p class="text-xs text-slate-500">Ord Total: ₹{{ number_format($earning->order_amount) }}</p>
                    </td>
                    <td>
                        <p class="font-black text-emerald-600">₹{{ number_format($earning->seller_amount) }}</p>
                        <p class="text-[0.65rem] text-slate-400">Fee: ₹{{ number_format($earning->commission_amount) }}</p>
                    </td>
                    <td>
                        @if($earning->status === 'on_hold')
                            <span class="badge badge-warning">On Hold</span>
                            <p class="text-[0.65rem] text-slate-500 mt-1">Till: {{ $earning->hold_until->format('d M, h:i A') }}</p>
                        @elseif($earning->status === 'customer_ok')
                            <span class="badge badge-info">Customer OK</span>
                        @endif
                    </td>
                    <td>
                        <form action="{{ route('admin.payouts.release', $earning->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="fk-btn-primary btn-sm" onclick="return confirm('Release this payout to seller early?')">Force Release</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center py-8 text-slate-500">No pending payouts found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($earnings->hasPages())
    <div class="p-4 border-t border-slate-100">
        {{ $earnings->links('pagination::tailwind') }}
    </div>
    @endif
</div>
@endsection
