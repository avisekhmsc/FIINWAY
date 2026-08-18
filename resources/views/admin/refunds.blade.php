@extends('layouts.admin')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-slate-800">Refunds</h1>
    
    <div class="flex gap-2">
        @foreach(['all' => 'All', 'pending' => 'Pending', 'processed' => 'Processed'] as $val => $label)
            <a href="{{ route('admin.refunds', ['status' => $val]) }}" class="btn btn-sm {{ $status === $val ? 'btn-primary' : 'btn-outline bg-white border-slate-200 text-slate-600' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>
</div>


<div class="fk-card p-0">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Refund Date</th>
                    <th>Order & Buyer</th>
                    <th>Payment Method</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($refunds as $refund)
                <tr>
                    <td class="text-sm text-slate-500">{{ $refund->created_at->format('d M, Y') }}</td>
                    <td>
                        <p class="font-bold text-slate-800">{{ $refund->order->order_number }}</p>
                        <p class="text-xs text-slate-500">{{ $refund->order->buyer->name }}</p>
                    </td>
                    <td>
                        <p class="font-semibold text-slate-700 text-sm uppercase">{{ $refund->payment->method }}</p>
                        <p class="text-[0.65rem] text-slate-400">Orig Ref: {{ $refund->payment->transaction_id }}</p>
                    </td>
                    <td class="font-bold text-danger">₹{{ number_format($refund->amount) }}</td>
                    <td>
                        <span class="badge {{ $refund->status === 'processed' ? 'badge-success' : 'badge-warning' }}">{{ $refund->statusLabel() }}</span>
                        @if($refund->transaction_ref)
                            <p class="text-[0.65rem] text-slate-500 mt-1">Ref: {{ $refund->transaction_ref }}</p>
                        @endif
                    </td>
                    <td>
                        @if($refund->status === 'pending')
                            <form action="{{ route('admin.refunds.process', $refund->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="fk-btn-primary btn-sm" onclick="return confirm('Process this refund via Razorpay? This cannot be undone.')">Process Refund</button>
                            </form>
                        @else
                            <span class="text-xs text-slate-400 font-semibold">Processed</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center py-8 text-slate-500">No refunds found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($refunds->hasPages())
    <div class="p-4 border-t border-slate-100">
        {{ $refunds->links('pagination::tailwind') }}
    </div>
    @endif
</div>
@endsection
