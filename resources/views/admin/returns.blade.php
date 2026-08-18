@extends('layouts.admin')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-slate-800">Return Requests</h1>
    
    <div class="flex gap-2">
        @foreach(['all' => 'All', 'requested' => 'New Requests', 'approved' => 'Approved', 'rejected' => 'Rejected'] as $val => $label)
            <a href="{{ route('admin.returns', ['status' => $val]) }}" class="btn btn-sm {{ $status === $val ? 'btn-primary' : 'btn-outline bg-white border-slate-200 text-slate-600' }}">
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
                    <th>Date</th>
                    <th>Order & Buyer</th>
                    <th>Reason</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($returns as $req)
                <tr>
                    <td class="text-sm text-slate-500">{{ $req->created_at->format('d M, Y') }}</td>
                    <td>
                        <p class="font-bold text-slate-800">{{ $req->order->order_number }}</p>
                        <p class="text-xs text-slate-500">{{ $req->buyer->name }} • {{ $req->buyer->phone }}</p>
                    </td>
                    <td>
                        <p class="font-semibold text-slate-700 text-sm">{{ $req->reason }}</p>
                        <p class="text-xs text-slate-500 line-clamp-1 max-w-[250px]">{{ $req->description }}</p>
                    </td>
                    <td>
                        <span class="badge badge-{{ $req->statusColor() }}">{{ $req->statusLabel() }}</span>
                    </td>
                    <td>
                        @if($req->status === 'requested')
                        <div class="flex gap-2" x-data="{ open: false }">
                            <button @click="open = true" class="fk-btn-primary btn-sm">Process</button>
                            
                            <!-- Process Modal -->
                            <div x-show="open" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40" x-cloak>
                                <div class="bg-white rounded-xl p-6 w-[450px] shadow-xl" @click.away="open = false">
                                    <h3 class="font-bold text-lg mb-4">Process Return Request</h3>
                                    
                                    <div class="mb-4 bg-slate-50 p-3 rounded-lg border border-slate-100 text-sm">
                                        <p><strong>Reason:</strong> {{ $req->reason }}</p>
                                        <p class="mt-1"><strong>Details:</strong> {{ $req->description }}</p>
                                    </div>

                                    <form action="{{ route('admin.returns.process', $req->id) }}" method="POST">
                                        @csrf
                                        <div class="mb-4">
                                            <label class="block text-sm font-bold text-slate-700 mb-2">Admin Note (Sent to buyer)</label>
                                            <textarea name="admin_note" class="input w-full" rows="3" placeholder="Provide instructions or reason..."></textarea>
                                        </div>
                                        <div class="flex justify-end gap-2">
                                            <button type="submit" name="action" value="reject" class="btn btn-danger btn-sm">Reject Request</button>
                                            <button type="submit" name="action" value="approve" class="fk-btn-primary btn-sm">Approve & Queue Refund</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @else
                            @if($req->admin_note)
                                <button class="fk-btn-outline btn-sm" onclick="alert('Admin Note: {{ addslashes($req->admin_note) }}')">View Note</button>
                            @endif
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center py-8 text-slate-500">No return requests found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($returns->hasPages())
    <div class="p-4 border-t border-slate-100">
        {{ $returns->links('pagination::tailwind') }}
    </div>
    @endif
</div>
@endsection
