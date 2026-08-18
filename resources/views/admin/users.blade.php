@extends('layouts.admin')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-slate-800">Users</h1>
    
    <div class="flex gap-2">
        @foreach(['all' => 'All Users', 'sellers' => 'Sellers', 'buyers' => 'Buyers Only', 'blocked' => 'Blocked'] as $val => $label)
            <a href="{{ route('admin.users', ['filter' => $val]) }}" class="btn btn-sm {{ $filter === $val ? 'btn-primary' : 'btn-outline bg-white border-slate-200 text-slate-600' }}">
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
                    <th>User Info</th>
                    <th>Contact</th>
                    <th>Location</th>
                    <th>Role</th>
                    <th>Wallet / Referral</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr>
                    <td>
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center font-bold text-slate-500">
                                {{ substr($user->name, 0, 1) }}
                            </div>
                            <div>
                                <p class="font-bold text-slate-800">{{ $user->name }}</p>
                                <p class="text-[0.65rem] text-slate-400">Joined: {{ $user->created_at->format('d M, Y') }}</p>
                            </div>
                        </div>
                    </td>
                    <td>
                        <p class="font-semibold text-slate-700">+91 {{ $user->phone }}</p>
                        <p class="text-xs text-slate-500">{{ $user->email ?? 'No email' }}</p>
                    </td>
                    <td>
                        <p class="text-sm">{{ $user->city ?? 'N/A' }}</p>
                        <p class="text-xs text-slate-500">{{ $user->state }}</p>
                    </td>
                    <td>
                        @if($user->isAdmin())
                            <span class="badge badge-purple">Admin</span>
                        @elseif($user->is_seller)
                            <span class="badge badge-success">Seller</span>
                        @else
                            <span class="badge badge-primary">Buyer</span>
                        @endif
                        
                        @if($user->is_blocked)
                            <span class="badge badge-danger ml-1">Blocked</span>
                        @endif
                    </td>
                    <td>
                        <p class="font-bold text-[#006837]">₹{{ number_format($user->wallet_balance) }}</p>
                        <p class="text-xs text-slate-500">Ref: {{ $user->referral_code }}</p>
                    </td>
                    <td>
                        @if(!$user->isAdmin())
                            <form action="{{ route('admin.users.toggle-block', $user->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-sm {{ $user->is_blocked ? 'btn-success' : 'btn-danger' }}">
                                    {{ $user->is_blocked ? 'Unblock' : 'Block' }}
                                </button>
                            </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center py-8 text-slate-500">No users found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($users->hasPages())
    <div class="p-4 border-t border-slate-100">
        {{ $users->links('pagination::tailwind') }}
    </div>
    @endif
</div>
@endsection
