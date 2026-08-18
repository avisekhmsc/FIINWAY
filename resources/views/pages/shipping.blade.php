@extends('pages.layout')
@php $pageTitle = 'Shipping & Delivery'; $pageSubtitle = "Fast, reliable delivery across India"; $breadcrumb = 'Help'; @endphp

@section('page-content')
<div class="space-y-8 text-[#212121]">

    <div class="grid sm:grid-cols-3 gap-4 text-center">
        <div class="p-5 bg-[#f1f3f6] rounded-sm">
            <div class="text-4xl mb-2">📦</div>
            <h3 class="font-bold">Standard Delivery</h3>
            <p class="text-sm text-[#878787] mt-1">3–7 business days</p>
            <p class="text-[#388e3c] font-medium text-sm mt-1">FREE on orders above ₹499</p>
        </div>
        <div class="p-5 bg-[#f1f3f6] rounded-sm">
            <div class="text-4xl mb-2">⚡</div>
            <h3 class="font-bold">Express Delivery</h3>
            <p class="text-sm text-[#878787] mt-1">1–2 business days</p>
            <p class="text-[#e94f1c] font-medium text-sm mt-1">₹49 – ₹99 delivery fee</p>
        </div>
        <div class="p-5 bg-[#f1f3f6] rounded-sm">
            <div class="text-4xl mb-2">🏙️</div>
            <h3 class="font-bold">Same Day Delivery</h3>
            <p class="text-sm text-[#878787] mt-1">Select metros only</p>
            <p class="text-[#e94f1c] font-medium text-sm mt-1">₹199 delivery fee</p>
        </div>
    </div>

    <div>
        <h2 class="text-xl font-bold mb-4">Shipping Policy</h2>
        <div class="space-y-4 text-sm text-[#666] leading-relaxed">
            <p><strong class="text-[#212121]">Order Processing:</strong> Orders placed before 2 PM on weekdays are processed the same day. Orders placed after 2 PM or on weekends are processed the next business day.</p>
            <p><strong class="text-[#212121]">Coverage:</strong> FIINWAY delivers to 19,000+ pin codes across India. Enter your pin code on the product page to check availability and estimated delivery date.</p>
            <p><strong class="text-[#212121]">Tracking:</strong> Once your order is shipped, you will receive an SMS and email with a tracking link. You can also track in real time from <a href="{{ route('orders') }}" class="text-[#006837]">My Orders</a>.</p>
            <p><strong class="text-[#212121]">Delays:</strong> During sale events, festive seasons, or due to natural events, deliveries may take longer. We always communicate delays proactively.</p>
        </div>
    </div>

    <div class="p-5 bg-green-50 border border-green-100 rounded-sm">
        <h3 class="font-bold text-[#212121] mb-2">📍 Track Your Order</h3>
        <p class="text-sm text-[#878787] mb-3">Check the live status of your shipment from your orders page.</p>
        <a href="{{ route('orders') }}" class="inline-block px-6 py-2 bg-[#006837] text-white text-sm font-medium rounded-sm hover:bg-green-800 transition">Go to My Orders</a>
    </div>

</div>
@endsection
