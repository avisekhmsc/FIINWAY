@props(['product'])

@php
    $liked = auth()->check() && auth()->user()->hasWishlisted($product->id);
@endphp

<div class="fk-card group relative overflow-hidden hover:shadow-xl transition-shadow duration-200 flex flex-col"
     x-data="wishlistCard({{ $product->id }}, {{ $liked ? 'true' : 'false' }})">

    {{-- Image Area --}}
    <div class="relative overflow-hidden bg-white flex items-center justify-center p-2" style="aspect-ratio:1/1;">
        <a href="{{ route('products.show', $product->slug) }}" class="block w-full h-full">
            <x-product-image :product="$product" aspect="square" class="w-full h-full object-contain transition-transform duration-300 group-hover:scale-105" />
        </a>

        {{-- Condition Badge top-left --}}
        <div class="absolute top-2 left-2 z-10">
            @if($product->condition_type === 'new')
                <span class="fk-tag-new">NEW</span>
            @else
                <span class="fk-tag-used">{{ strtoupper($product->condition_label ?? 'PRE-OWNED') }}</span>
            @endif
        </div>

        {{-- Wishlist Heart Button top-right --}}
        @auth
            <button type="button" @click.prevent="toggleWishlist()"
                class="absolute top-2 right-2 z-10 w-8 h-8 rounded-full bg-white shadow flex items-center justify-center transition-all hover:scale-110 active:scale-95"
                :title="liked ? 'Remove from Wishlist' : 'Save'">
                <i :class="liked ? 'ri-heart-fill text-red-500' : 'ri-heart-3-line text-slate-400 hover:text-red-400'" class="text-base transition-all"></i>
            </button>
        @else
            <a href="{{ route('mobile') }}" class="absolute top-2 right-2 z-10 w-8 h-8 rounded-full bg-white shadow flex items-center justify-center" title="Login to Save">
                <i class="ri-heart-3-line text-slate-400 text-base"></i>
            </a>
        @endauth

        {{-- Discount badge bottom-left --}}
        @if($product->discount_percent > 0)
            <div class="absolute bottom-2 left-2 z-10">
                <span class="text-[10px] font-bold bg-orange-500 text-slate-900 px-1.5 py-0.5 rounded-sm">{{ round($product->discount_percent) }}% off</span>
            </div>
        @endif
    </div>

    {{-- Product Info --}}
    <div class="p-3 flex flex-col flex-1 justify-between border-t border-slate-100">
        <div>
            {{-- Brand/Seller --}}
            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wide mb-0.5">
                {{ $product->brand ?: ($product->category->name ?? 'General') }}
            </div>

            {{-- Title --}}
            <h3 class="text-sm font-medium text-slate-800 leading-snug line-clamp-2 mb-1">
                <a href="{{ route('products.show', $product->slug) }}" class="hover:text-green-700">
                    {{ $product->name }}
                </a>
            </h3>

            {{-- Star Rating --}}
            @if($product->rating)
            <div class="flex items-center gap-1.5 mb-2">
                <span class="fk-star flex items-center gap-0.5">
                    {{ number_format($product->rating, 1) }}
                    <i class="ri-star-fill text-[9px]"></i>
                </span>
                @if($product->rating_count)
                <span class="text-[10px] text-slate-400">({{ $product->rating_count }})</span>
                @endif
            </div>
            @endif
        </div>

        {{-- Price Row --}}
        <div class="flex items-center justify-between">
            <div>
                <span class="fk-price">₹{{ number_format($product->selling_price) }}</span>
                @if($product->original_price && $product->original_price > $product->selling_price)
                    <span class="fk-mrp ml-1">₹{{ number_format($product->original_price) }}</span>
                    <span class="fk-discount ml-1">{{ round($product->discount_percent) }}% off</span>
                @endif
            </div>

            {{-- Add to Cart --}}
            @auth
                <form action="{{ route('cart.add') }}" method="POST">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <input type="hidden" name="quantity" value="1">
                    <button type="submit"
                            class="w-8 h-8 rounded-full flex items-center justify-center text-white transition-all active:scale-95 hover:brightness-90"
                            style="background:#006837;" title="Add to Cart">
                        <i class="ri-shopping-cart-2-line text-base"></i>
                    </button>
                </form>
            @else
                <a href="{{ route('mobile') }}"
                   class="w-8 h-8 rounded-full flex items-center justify-center text-white"
                   style="background:#006837;" title="Login to Add to Cart">
                    <i class="ri-shopping-cart-2-line text-base"></i>
                </a>
            @endauth
        </div>
    </div>
</div>

<script>
function wishlistCard(productId, initialLiked) {
    return {
        liked: initialLiked,
        loading: false,
        async toggleWishlist() {
            if (this.loading) return;
            this.loading = true;
            this.liked = !this.liked;
            try {
                const resp = await fetch(`/wishlist/${productId}/toggle`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                });
                if (!resp.ok) throw new Error('Server error');
                const data = await resp.json();
                this.liked = data.liked;
            } catch (e) {
                this.liked = !this.liked;
            } finally {
                this.loading = false;
            }
        }
    }
}
</script>
