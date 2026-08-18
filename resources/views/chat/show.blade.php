@extends('layouts.app')

@section('title', 'Chat with Seller')

@section('content')
<div class="max-w-2xl mx-auto p-4 py-8">
    <div class="bg-white rounded-lg shadow border border-slate-200 flex flex-col h-[600px]">
        <!-- Header -->
        <div class="p-4 border-b border-slate-100 flex items-center justify-between bg-slate-50 rounded-t-lg">
            <div>
                <h3 class="font-bold text-[#212121]">Chat with {{ $seller->name }}</h3>
                <p class="text-xs text-[#878787]">Order #{{ $order->order_number }}</p>
            </div>
            <a href="{{ url()->previous() }}" class="text-[#006837] text-sm font-medium hover:underline">Back to Order</a>
        </div>

        <!-- Messages -->
        <div class="flex-1 overflow-y-auto p-4 space-y-4">
            @forelse($messages as $msg)
                <div class="flex flex-col {{ $msg->sender_id === Auth::id() ? 'items-end' : 'items-start' }}">
                    <div class="max-w-[80%] p-3 rounded-lg text-sm {{ $msg->sender_id === Auth::id() ? 'bg-[#006837] text-white' : 'bg-slate-100 text-[#212121]' }}">
                        {{ $msg->body }}
                    </div>
                    <span class="text-[10px] text-slate-400 mt-1">{{ $msg->created_at->format('h:i A') }}</span>
                </div>
            @empty
                <div class="text-center text-slate-500 text-sm mt-10">
                    No messages yet. Start the conversation!
                </div>
            @endforelse
        </div>

        <!-- Input -->
        <div class="p-4 border-t border-slate-100 bg-white rounded-b-lg">
            <form action="{{ route('chat.store', $conversation) }}" method="POST" class="flex gap-2">
                @csrf
                <input type="text" name="body" class="flex-1 border border-slate-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-[#006837]" placeholder="Type your message..." required>
                <button type="submit" class="px-4 py-2 bg-[#e94f1c] text-white rounded font-medium text-sm hover:bg-[#cc4214]">Send</button>
            </form>
        </div>
    </div>
</div>
@endsection
