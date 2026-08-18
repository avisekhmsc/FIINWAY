@extends('layouts.app')

@section('title', $product->name . ' — Buy Online at Best Price in India')

@section('content')
@php
    $imagesCount = $product->images->count();
@endphp

<div class="bg-white min-h-screen pb-20" x-data="{ ...productGallery({{ $imagesCount > 0 ? $imagesCount : 1 }}), showReviewForm: false, rating: 0 }">

    <!-- Top Breadcrumb -->
    <div class="max-w-7xl mx-auto px-4 py-3 text-xs font-medium text-slate-500 flex items-center gap-2 overflow-x-auto border-b border-slate-100">
        <a href="{{ route('home') }}" class="hover:text-green-700 shrink-0">Home</a>
        <i class="ri-arrow-right-s-line shrink-0 text-slate-400"></i>
        <a href="{{ route('products') }}" class="hover:text-green-700 shrink-0">Electronics</a>
        <i class="ri-arrow-right-s-line shrink-0 text-slate-400"></i>
        <span class="text-slate-800 shrink-0">{{ $product->name }}</span>
    </div>

    <div class="max-w-7xl mx-auto flex flex-col md:flex-row">
        
        <!-- Left Column: Sticky Image Gallery -->
        <div class="w-full md:w-[40%] lg:w-[35%] p-4 md:border-r border-slate-200 relative">
            <div class="sticky top-20">
                <!-- Main Image -->
                <div class="relative w-full aspect-square flex items-center justify-center mb-4 border border-slate-100 p-4">
                    @foreach($product->images as $idx => $img)
                        <div x-show="activeImage === {{ $idx }}" class="w-full h-full flex items-center justify-center">
                            <x-product-image :path="$img->image_path" :alt="$product->name" aspect="square" class="max-w-full max-h-full object-contain" />
                        </div>
                    @endforeach
                    @if($product->images->isEmpty())
                        <div class="w-full h-full flex items-center justify-center">
                            <x-product-image :product="$product" aspect="square" class="max-w-full max-h-full object-contain" />
                        </div>
                    @endif
                    
                    <button class="absolute top-4 right-4 w-10 h-10 rounded-full bg-white shadow flex items-center justify-center border border-slate-100 text-slate-400 hover:text-red-500">
                        <i class="ri-heart-3-fill text-xl"></i>
                    </button>
                </div>

                <!-- Thumbnails -->
                @if($imagesCount > 1)
                <div class="flex items-center gap-2 overflow-x-auto pb-2 justify-center">
                    @foreach($product->images as $idx => $img)
                        <button @click="activeImage = {{ $idx }}" 
                                class="w-16 h-16 border-2 flex items-center justify-center shrink-0 p-1 transition-colors"
                                :class="activeImage === {{ $idx }} ? 'border-green-700' : 'border-slate-200'">
                            <x-product-image :path="$img->image_path" :alt="$product->name" aspect="square" class="max-w-full max-h-full object-contain" />
                        </button>
                    @endforeach
                </div>
                @endif

                <!-- Desktop Action Buttons -->
                <div class="hidden md:flex items-center gap-2 mt-4">
                    @auth
                        <form action="{{ route('cart.add') }}" method="POST" class="flex-1">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <button type="submit" class="w-full py-4 bg-[#ff9f00] text-white font-bold text-base flex items-center justify-center gap-2 rounded-sm shadow hover:bg-[#f39800]">
                                <i class="ri-shopping-cart-2-fill text-xl"></i> ADD TO CART
                            </button>
                        </form>
                        <form action="{{ route('cart.add') }}" method="POST" class="flex-1">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <input type="hidden" name="action" value="buy_now">
                            <button type="submit" class="w-full py-4 bg-[#e94f1c] text-white font-bold text-base flex items-center justify-center gap-2 rounded-sm shadow hover:bg-[#cc4214]">
                                <i class="ri-flashlight-fill text-xl"></i> BUY NOW
                            </button>
                        </form>
                    @else
                        <a href="{{ route('mobile') }}" class="flex-1 py-4 bg-[#ff9f00] text-white font-bold text-base flex items-center justify-center gap-2 rounded-sm shadow hover:bg-[#f39800]">
                            <i class="ri-shopping-cart-2-fill text-xl"></i> ADD TO CART
                        </a>
                        <a href="{{ route('mobile') }}" class="flex-1 py-4 bg-[#e94f1c] text-white font-bold text-base flex items-center justify-center gap-2 rounded-sm shadow hover:bg-[#cc4214]">
                            <i class="ri-flashlight-fill text-xl"></i> BUY NOW
                        </a>
                    @endauth
                </div>
            </div>
        </div>

        <!-- Right Column: Product Details -->
        <div class="w-full md:w-[60%] lg:w-[65%] p-4 sm:p-6">
            
            <div class="border-b border-slate-200 pb-4 mb-4 space-y-2">
                <h1 class="text-[18px] sm:text-[22px] text-[#212121] leading-snug">{{ $product->name }}</h1>
                
                <div class="flex items-center gap-3">
                    @if($product->rating)
                    <span class="fk-star flex items-center gap-1 px-1.5 py-0.5 text-[13px]">
                        {{ number_format($product->rating, 1) }} <i class="ri-star-fill text-[10px]"></i>
                    </span>
                    <span class="text-slate-500 font-medium text-sm">{{ $product->rating_count ?? 0 }} Ratings &amp; {{ $product->reviews->count() }} Reviews</span>
                    @else
                    <span class="text-slate-500 font-medium text-sm">{{ $product->reviews->count() }} Reviews</span>
                    @endif
                    <div class="flex items-center gap-1 bg-[#006837] text-white text-[10px] px-1.5 py-0.5 rounded-sm font-black italic tracking-wide">
                        FIINWAY <span class="text-white/80 font-semibold">Assured</span> <i class="ri-checkbox-circle-fill text-[#e94f1c] text-[12px]"></i>
                    </div>
                </div>

                <div class="text-[#388e3c] font-medium text-sm">Special price</div>
                <div class="flex items-end gap-3 mt-1">
                    <span class="text-3xl text-[#212121] font-medium">₹{{ number_format($product->selling_price) }}</span>
                    @if($product->original_price > $product->selling_price)
                        <span class="text-base text-[#878787] line-through mb-1">₹{{ number_format($product->original_price) }}</span>
                        <span class="text-base text-[#388e3c] font-medium mb-1">{{ round($product->discount_percent) }}% off</span>
                    @endif
                </div>
            </div>

            <!-- Offers Section -->
            <div class="mb-6 space-y-3">
                <h3 class="text-base font-medium text-[#212121]">Available offers</h3>
                <ul class="space-y-2 text-sm text-[#212121]">
                    <li class="flex items-start gap-2">
                        <i class="ri-price-tag-3-fill text-[#18ab56] mt-0.5"></i>
                        <span><strong class="font-medium">Bank Offer:</strong> 5% Unlimited Cashback on FIINWAY Axis Bank Credit Card</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <i class="ri-price-tag-3-fill text-[#18ab56] mt-0.5"></i>
                        <span><strong class="font-medium">Special Price:</strong> Get extra 10% off (price inclusive of cashback/coupon)</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <i class="ri-calendar-check-fill text-[#18ab56] mt-0.5"></i>
                        <span><strong class="font-medium">EMI:</strong> No cost EMI ₹{{ number_format($product->selling_price / 6) }}/month. Standard EMI also available</span>
                    </li>
                </ul>
            </div>

            <!-- Service Info -->
            <div class="flex flex-wrap items-center gap-6 py-4 border-y border-slate-200 mb-6 text-sm font-medium text-[#212121]">
                <div class="flex items-center gap-2">
                    <i class="ri-truck-fill text-[#006837] text-xl"></i> Free Delivery by Tomorrow
                </div>
                <div class="flex items-center gap-2">
                    <i class="ri-refresh-line text-[#006837] text-xl"></i> 7 Days Replacement Policy
                </div>
                <div class="flex items-center gap-2">
                    <i class="ri-money-rupee-circle-line text-[#006837] text-xl"></i> Cash on Delivery available
                </div>
            </div>

            <!-- Product Condition & History (Old Products Only) -->
            @if(strtolower($product->condition_type) === 'old')
                <div class="border border-slate-200 rounded-sm mb-6">
                    <div class="px-6 py-4 text-lg font-medium border-b border-slate-200 text-[#212121]">Product Condition & History</div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-4 gap-x-8 text-sm">
                            <div class="flex flex-col">
                                <span class="text-[#878787] mb-1">Age</span>
                                <span class="text-[#212121]">{{ $product->product_age_months ? $product->product_age_months . ' months' : 'Not provided' }}</span>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-[#878787] mb-1">Condition</span>
                                <span class="text-[#212121] capitalize">{{ $product->condition_label ?: 'Not provided' }}</span>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-[#878787] mb-1">Bill Available</span>
                                <span class="text-[#212121]">{{ $product->bill_available ? 'Yes' : 'No' }}</span>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-[#878787] mb-1">Warranty Available</span>
                                <span class="text-[#212121]">{{ $product->warranty_available ? 'Yes' : 'No' }}</span>
                            </div>
                        </div>
                        
                        <div class="mt-4 pt-4 border-t border-slate-100 flex flex-col gap-4 text-sm">
                            @if($product->warranty_available && $product->warranty_info)
                                <div>
                                    <span class="text-[#878787] block mb-1">Warranty Info</span>
                                    <span class="text-[#212121] block whitespace-pre-line">{{ $product->warranty_info }}</span>
                                </div>
                            @endif
                            
                            <div>
                                <span class="text-[#878787] block mb-1">Damage / Repair Details</span>
                                <span class="text-[#212121] block whitespace-pre-line">{{ $product->damage_details ?: 'Not provided' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Seller Info -->
            <div class="flex items-start gap-12 mb-6 text-sm">
                <div class="text-[#878787] font-medium w-24 shrink-0">Seller</div>
                <div>
                    <div class="text-[#006837] font-medium text-base mb-1">{{ $product->seller->name }}</div>
                    <ul class="list-disc list-inside text-[#212121] space-y-1">
                        <li>7 Days Replacement Policy</li>
                        <li>GST invoice available</li>
                    </ul>
                </div>
            </div>

            <!-- Description -->
            <div class="border border-slate-200 rounded-sm mb-6">
                <div class="px-6 py-4 text-lg font-medium border-b border-slate-200 text-[#212121]">Product Description</div>
                <div class="p-6 text-sm text-[#212121] leading-relaxed whitespace-pre-line">
                    {{ $product->description }}
                </div>
            </div>

            <!-- Ratings & Reviews -->
            <div class="border border-slate-200 rounded-sm">
                <div class="px-6 py-4 text-lg font-medium border-b border-slate-200 text-[#212121] flex items-center justify-between">
                    <span>Ratings & Reviews</span>
                    @auth
                        @php
                            $eligibleOrder = \App\Models\Order::where('user_id', Auth::id())
                                ->where('status', 'delivered')
                                ->whereHas('items', function($q) use($product) {
                                    $q->where('product_id', $product->id);
                                })->first();
                        @endphp
                        @if($eligibleOrder)
                            <button @click="showReviewForm = !showReviewForm" class="px-4 py-2 bg-white text-[#212121] font-medium text-sm border border-slate-300 rounded shadow-sm hover:bg-slate-50">
                                Rate Product
                            </button>
                        @endif
                    @endauth
                </div>

                @auth
                    @if(isset($eligibleOrder))
                        <div x-show="showReviewForm" style="display: none;" class="p-6 border-b border-slate-200 bg-slate-50">
                            <form action="{{ route('reviews.store') }}" method="POST" class="space-y-4">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                <input type="hidden" name="order_id" value="{{ $eligibleOrder->id }}">
                                
                                <div>
                                    <label class="block text-sm font-medium text-[#212121] mb-2">Rate this product</label>
                                    <div class="flex items-center gap-2">
                                        <template x-for="i in 5">
                                            <button type="button" @click="rating = i" class="text-2xl" :class="rating >= i ? 'text-[#ff9f00]' : 'text-slate-300'">
                                                <i class="ri-star-fill"></i>
                                            </button>
                                        </template>
                                    </div>
                                    <input type="hidden" name="rating" x-model="rating" required>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-[#212121] mb-2">Review this product</label>
                                    <textarea name="comment" rows="3" class="w-full p-3 border border-slate-300 rounded outline-none focus:border-[#006837] text-sm" placeholder="Description..."></textarea>
                                </div>

                                <div class="flex justify-end gap-2">
                                    <button type="button" @click="showReviewForm = false" class="px-6 py-2 text-[#212121] font-medium">Cancel</button>
                                    <button type="submit" class="px-6 py-2 bg-[#e94f1c] text-white font-medium rounded shadow-sm hover:bg-[#cc4214]">Submit</button>
                                </div>
                            </form>
                        </div>
                    @endif
                @endauth

                <div class="p-6 space-y-6">
                    @forelse($product->reviews as $review)
                        <div class="border-b border-slate-100 pb-4 last:border-0 last:pb-0">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="fk-star flex items-center gap-1 px-1.5 py-0.5 text-[11px] {{ $review->rating < 3 ? 'bg-red-500' : ($review->rating == 3 ? 'bg-orange-700' : 'bg-[#388e3c]') }}">
                                    {{ $review->rating }} <i class="ri-star-fill text-[9px]"></i>
                                </span>
                                <span class="font-medium text-sm text-[#212121]">{{ $review->title ?: 'Verified Review' }}</span>
                            </div>
                            <p class="text-sm text-[#212121] mb-3">{{ $review->comment }}</p>
                            <div class="flex items-center gap-4 text-xs font-medium text-[#878787]">
                                <span>{{ $review->user->name ?: 'Verified Buyer' }}</span>
                                <span class="flex items-center gap-1"><i class="ri-checkbox-circle-fill text-slate-400"></i> Certified Buyer</span>
                                <span>{{ $review->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="text-sm text-[#878787] text-center py-4">No reviews yet for this product. Be the first to review!</div>
                    @endforelse
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Mobile Sticky Footer Actions -->
<div class="fixed bottom-0 left-0 right-0 md:hidden bg-white border-t border-slate-200 flex items-center z-50">
    @auth
        <form action="{{ route('cart.add') }}" method="POST" class="w-1/2">
            @csrf
            <input type="hidden" name="product_id" value="{{ $product->id }}">
            <button type="submit" class="w-full py-3.5 bg-white text-[#212121] font-bold text-sm flex items-center justify-center uppercase">
                ADD TO CART
            </button>
        </form>
        <form action="{{ route('cart.add') }}" method="POST" class="w-1/2">
            @csrf
            <input type="hidden" name="product_id" value="{{ $product->id }}">
            <input type="hidden" name="action" value="buy_now">
            <button type="submit" class="w-full py-3.5 bg-[#e94f1c] text-white font-bold text-sm flex items-center justify-center uppercase">
                <i class="ri-flashlight-fill mr-1 text-lg"></i> BUY NOW
            </button>
        </form>
    @else
        <a href="{{ route('mobile') }}" class="w-1/2 py-3.5 bg-white text-[#212121] font-bold text-sm flex items-center justify-center uppercase">
            ADD TO CART
        </a>
        <a href="{{ route('mobile') }}" class="w-1/2 py-3.5 bg-[#e94f1c] text-white font-bold text-sm flex items-center justify-center uppercase">
            <i class="ri-flashlight-fill mr-1 text-lg"></i> BUY NOW
        </a>
    @endauth
</div>

<script>
function productGallery(total) {
    return {
        activeImage: 0,
        totalImages: total,
    }
}
</script>
@endsection
