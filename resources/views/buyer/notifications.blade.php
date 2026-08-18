@extends('layouts.app')
@section('title', 'Notifications — FIINWAY')

@section('content')
<div class="bg-[#f1f3f6] min-h-screen pb-16">
    <div class="max-w-2xl mx-auto px-2 sm:px-4 py-4 space-y-3">

        {{-- Mark all as read button (top right, subtle) --}}
        @if($notifications->isNotEmpty())
        <div class="flex justify-end">
            <form action="{{ route('notifications.read-all') }}" method="POST">
                @csrf
                <button type="submit" class="text-xs text-[#006837] font-medium hover:underline">Mark all as read</button>
            </form>
        </div>
        @endif
        @forelse($notifications as $notif)
        <div class="bg-white rounded-sm shadow-sm border-l-4 overflow-hidden {{ $notif->is_read ? 'border-l-transparent' : 'border-l-[#006837]' }}">
            <div class="flex items-start gap-3 p-4">
                <div class="w-9 h-9 rounded-full flex items-center justify-center shrink-0 text-white text-base
                    {{ match($notif->type) {
                        'order'   => 'bg-[#006837]',
                        'payout'  => 'bg-[#388e3c]',
                        'product' => 'bg-[#ff9f00]',
                        'return'  => 'bg-[#e94f1c]',
                        'refund'  => 'bg-[#006837]',
                        default   => 'bg-slate-400',
                    } }}">
                    <i class="{{ match($notif->type) {
                        'order'   => 'ri-shopping-bag-3-line',
                        'payout'  => 'ri-wallet-3-line',
                        'product' => 'ri-store-2-line',
                        'return'  => 'ri-arrow-go-back-line',
                        'refund'  => 'ri-refund-2-line',
                        default   => 'ri-notification-3-line',
                    } }}"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex justify-between items-start gap-2">
                        <h3 class="text-sm font-medium text-[#212121] leading-snug">{{ $notif->title }}</h3>
                        @if(!$notif->is_read)
                            <form action="{{ route('notifications.read', $notif->id) }}" method="POST" class="shrink-0">
                                @csrf
                                <button type="submit" class="w-6 h-6 rounded-full bg-green-50 text-[#006837] flex items-center justify-center hover:bg-green-100" title="Mark as read">
                                    <i class="ri-check-line text-xs"></i>
                                </button>
                            </form>
                        @endif
                    </div>
                    <p class="text-xs text-slate-500 leading-relaxed mt-0.5">{{ $notif->body }}</p>
                    <p class="text-[10px] text-slate-400 mt-1.5">{{ $notif->created_at->diffForHumans() }}</p>
                </div>
            </div>
        </div>
        @empty
        <div class="bg-white rounded-sm shadow-sm p-16 text-center">
            <i class="ri-notification-off-line text-5xl text-slate-200 block mb-4"></i>
            <h3 class="text-base font-medium text-[#212121] mb-1">No notifications yet</h3>
            <p class="text-sm text-slate-500">You're all caught up!</p>
        </div>
        @endforelse

        <div class="mt-2">{{ $notifications->links('pagination::tailwind') }}</div>
    </div>
</div>
@endsection
