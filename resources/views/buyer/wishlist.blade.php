@extends('layouts.app')

@section('title', 'My Wishlist — FIINWAY')

@section('content')
<div class="bg-[#f1f3f6] min-h-screen pb-16 md:pb-0">
    <div class="max-w-7xl mx-auto px-2 sm:px-4 py-4 sm:py-6 space-y-3">

        {{-- Header --}}
        <div class="bg-white rounded-sm shadow-sm p-4 flex items-center justify-between">
            <h1 class="text-xl font-medium text-[#212121] flex items-center gap-2">
                <i class="ri-heart-3-fill text-red-500"></i> My Wishlist
                <span class="text-sm text-slate-400 font-normal">({{ $items->count() }} items)</span>
            </h1>
            <a href="{{ route('products') }}" class="text-sm text-[#006837] font-medium hover:underline">Continue Shopping</a>
        </div>

        @if($items->isEmpty())
            <div class="bg-white rounded-sm shadow-sm p-16 text-center">
                <i class="ri-heart-3-line text-6xl text-slate-200 block mb-4"></i>
                <h3 class="text-lg font-medium text-[#212121] mb-2">Your Wishlist is empty!</h3>
                <p class="text-sm text-slate-500 mb-6">Add items that you like to your wishlist. Review them anytime and easily move them to the bag.</p>
                <a href="{{ route('products') }}" class="px-8 py-3 bg-[#e94f1c] text-white font-bold text-sm rounded-sm hover:bg-[#cc4214] inline-block">Continue Shopping</a>
            </div>
        @else
            <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                @foreach($items as $item)
                    @if($item->product)
                        <x-product-card :product="$item->product" />
                    @endif
                @endforeach
            </div>
        @endif

    </div>
</div>
@endsection
