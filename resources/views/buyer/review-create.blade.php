@extends('layouts.app', ['hideNav' => true])
@section('title', 'Write a Review — FIINWAY')

@section('content')
<div class="bg-slate-50 min-h-screen pb-24">
    <div class="sticky top-0 z-40 bg-white border-b border-slate-100 p-4 flex items-center justify-between shadow-sm">
        <div class="flex items-center gap-3">
            <a href="{{ route('orders.show', $order->id) }}" class="w-10 h-10 rounded-full bg-slate-50 flex items-center justify-center text-slate-700">
                <i class="ri-arrow-left-line text-lg"></i>
            </a>
            <h1 class="text-lg font-bold text-slate-900">Rate Product</h1>
        </div>
    </div>

    <div class="p-4">
        <!-- Product Info -->
        <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-100 mb-4 flex items-center gap-4">
            <img src="{{ $product->primary_image_url }}" alt="{{ $product->name }}" class="w-16 h-16 rounded-xl object-cover">
            <div>
                <p class="font-bold text-slate-900 line-clamp-1">{{ $product->name }}</p>
                <p class="text-xs text-slate-500">Order: {{ $order->order_number }}</p>
            </div>
        </div>

        <form action="{{ route('reviews.store') }}" method="POST" class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
            @csrf
            <input type="hidden" name="product_id" value="{{ $product->id }}">
            <input type="hidden" name="order_id" value="{{ $order->id }}">

            <div class="text-center mb-6">
                <h3 class="font-bold text-lg mb-2">How would you rate it?</h3>
                <div class="star-rating inline-flex justify-center mx-auto">
                    @for($i = 5; $i >= 1; $i--)
                        <input type="radio" id="star{{ $i }}" name="rating" value="{{ $i }}" {{ old('rating', $existing->rating ?? 0) == $i ? 'checked' : '' }} required>
                        <label for="star{{ $i }}" title="{{ $i }} stars">★</label>
                    @endfor
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-bold text-slate-700 mb-2">Write your review (Optional)</label>
                <textarea name="comment" class="input" rows="4" placeholder="What did you like or dislike about this product?">{{ old('comment', $existing->comment ?? '') }}</textarea>
            </div>

            <button type="submit" class="btn btn-primary btn-block btn-lg">
                {{ $existing ? 'Update Review' : 'Submit Review' }}
            </button>
        </form>
    </div>
</div>
@endsection
