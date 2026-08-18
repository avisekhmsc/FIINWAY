@props([
    'product' => null,
    'path' => null,
    'alt' => null,
    'aspect' => 'square', // square, video, 4/3
    'class' => '',
    'lazy' => true
])

@php
    $imagePath = null;
    if ($path) {
        $imagePath = $path;
    } elseif ($product) {
        $primary = $product->images->firstWhere('is_primary', true) ?? $product->images->first();
        $imagePath = $primary ? $primary->image_path : null;
    }

    $url = null;
    if ($imagePath) {
        // If it's already a full URL (CDN), use directly; otherwise resolve from storage
        $url = (str_starts_with($imagePath, 'http://') || str_starts_with($imagePath, 'https://'))
            ? $imagePath
            : asset('storage/' . ltrim($imagePath, '/'));
    } else {
        $url = asset('storage/products/default.webp');
    }
    $altText = $alt ?? ($product ? $product->name : 'Product Image');

    $aspectClass = match($aspect) {
        'video' => 'aspect-video',
        '4/3'   => 'aspect-[4/3]',
        default => 'aspect-square',
    };
@endphp

<div class="relative overflow-hidden bg-slate-100 dark:bg-slate-800 rounded-xl {{ $aspectClass }} {{ $class }}">
    <img 
        src="{{ $url }}" 
        alt="{{ $altText }}" 
        @if($lazy) loading="lazy" @endif
        class="w-full h-full object-cover object-center transition-transform duration-500 hover:scale-105"
        onerror="this.onerror=null; this.src='{{ asset('storage/products/default.webp') }}';"
    />
</div>
