@extends('layouts.app', ['hideNav' => true])
@section('title', 'Return Request — FIINWAY')

@section('content')
<div class="bg-slate-50 min-h-screen pb-24">
    <div class="sticky top-0 z-40 bg-white border-b border-slate-100 p-4 flex items-center justify-between shadow-sm">
        <div class="flex items-center gap-3">
            <a href="{{ route('orders.show', $order->id) }}" class="w-10 h-10 rounded-full bg-slate-50 flex items-center justify-center text-slate-700">
                <i class="ri-arrow-left-line text-lg"></i>
            </a>
            <h1 class="text-lg font-bold text-slate-900">Request Return</h1>
        </div>
    </div>

    <div class="p-4">
        @if($existing)
            <div class="alert alert-warning">
                <i class="ri-error-warning-fill"></i>
                You already have an active return request for this order. Status: <strong>{{ $existing->statusLabel() }}</strong>.
            </div>
            <a href="{{ route('returns.index') }}" class="btn btn-outline btn-block mt-4">View My Returns</a>
        @else
            <!-- Order Info -->
            <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-100 mb-4">
                <div class="flex justify-between items-center mb-3 border-b border-slate-50 pb-2">
                    <span class="text-xs font-bold text-slate-500">Order ID: {{ $order->order_number }}</span>
                    <span class="text-xs font-bold text-slate-500">{{ $order->created_at->format('d M, Y') }}</span>
                </div>
                
                @foreach($order->items as $item)
                <div class="flex items-center gap-3 mb-2 last:mb-0">
                    <img src="{{ $item->product->primary_image_url }}" class="w-12 h-12 rounded-lg object-cover">
                    <div class="flex-1">
                        <p class="font-bold text-slate-900 text-sm line-clamp-1">{{ $item->product_name }}</p>
                        <p class="text-xs text-slate-500">Qty: {{ $item->quantity }} • ₹{{ number_format($item->price) }}</p>
                    </div>
                </div>
                @endforeach
            </div>

            <form action="{{ route('returns.store') }}" method="POST" class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
                @csrf
                <input type="hidden" name="order_id" value="{{ $order->id }}">

                <div class="mb-4">
                    <label class="block text-sm font-bold text-slate-700 mb-2">Why are you returning this?</label>
                    <select name="reason" class="input" required>
                        <option value="">Select a reason</option>
                        <option value="Item defective or doesn't work">Item defective or doesn't work</option>
                        <option value="Product damaged but shipping box OK">Product damaged but shipping box OK</option>
                        <option value="Wrong item was sent">Wrong item was sent</option>
                        <option value="Missing parts or accessories">Missing parts or accessories</option>
                        <option value="Item and shipping box both damaged">Item and shipping box both damaged</option>
                        <option value="Description on website was inaccurate">Description on website was inaccurate</option>
                        <option value="No longer needed">No longer needed</option>
                    </select>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-bold text-slate-700 mb-2">More Details (Required)</label>
                    <textarea name="description" class="input" rows="4" placeholder="Please provide specific details about the issue..." required></textarea>
                </div>

                <button type="submit" class="btn btn-primary btn-block btn-lg">Submit Return Request</button>
            </form>
            
            <p class="text-xs text-slate-500 text-center mt-4 px-4">
                By submitting this request, you agree to our Return Policy. Items must be in original condition with all accessories.
            </p>
        @endif
    </div>
</div>
@endsection
