@extends('pages.layout')
@php $pageTitle = 'About FIINWAY'; $pageSubtitle = "India's fastest growing online marketplace"; $breadcrumb = 'Company'; @endphp

@section('page-content')
<div class="space-y-8 text-[#212121]">

    <div class="flex flex-col md:flex-row items-center gap-8">
        <div class="flex-1">
            <h2 class="text-2xl font-bold text-[#006837] mb-4">Our Story</h2>
            <p class="text-[#666] leading-relaxed mb-4">
                FIINWAY was founded with a simple mission — to make quality products accessible to every Indian. From electronics and fashion to home goods and groceries, we bring the best of India's sellers directly to your doorstep.
            </p>
            <p class="text-[#666] leading-relaxed">
                With millions of products across hundreds of categories, and a seller network spanning every corner of India, FIINWAY is more than just a marketplace — it's a community of buyers and sellers building a better tomorrow together.
            </p>
        </div>
        <div class="shrink-0">
            <div style="background: linear-gradient(135deg, #006837, #172337);" class="w-48 h-48 rounded-full flex items-center justify-center">
                <span class="text-white font-black text-4xl italic tracking-tight">B</span>
            </div>
        </div>
    </div>

    <hr class="border-slate-100">

    <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
        <div class="p-4 bg-[#f1f3f6] rounded-sm">
            <div class="text-3xl font-black text-[#006837]">50M+</div>
            <div class="text-sm text-[#878787] mt-1">Happy Customers</div>
        </div>
        <div class="p-4 bg-[#f1f3f6] rounded-sm">
            <div class="text-3xl font-black text-[#006837]">2M+</div>
            <div class="text-sm text-[#878787] mt-1">Registered Sellers</div>
        </div>
        <div class="p-4 bg-[#f1f3f6] rounded-sm">
            <div class="text-3xl font-black text-[#006837]">500M+</div>
            <div class="text-sm text-[#878787] mt-1">Products Listed</div>
        </div>
        <div class="p-4 bg-[#f1f3f6] rounded-sm">
            <div class="text-3xl font-black text-[#006837]">19k+</div>
            <div class="text-sm text-[#878787] mt-1">Pin Codes Served</div>
        </div>
    </div>

    <hr class="border-slate-100">

    <div>
        <h2 class="text-xl font-bold mb-4">Our Values</h2>
        <div class="grid md:grid-cols-3 gap-4">
            <div class="p-5 border border-slate-100 rounded-sm">
                <div class="text-3xl mb-3">🛡️</div>
                <h3 class="font-bold text-[#212121] mb-2">Trust & Safety</h3>
                <p class="text-sm text-[#878787]">Every product is verified, every payment is secure, every transaction is protected.</p>
            </div>
            <div class="p-5 border border-slate-100 rounded-sm">
                <div class="text-3xl mb-3">🚀</div>
                <h3 class="font-bold text-[#212121] mb-2">Speed & Reliability</h3>
                <p class="text-sm text-[#878787]">Fast deliveries, real-time tracking, and round-the-clock support for every order.</p>
            </div>
            <div class="p-5 border border-slate-100 rounded-sm">
                <div class="text-3xl mb-3">🤝</div>
                <h3 class="font-bold text-[#212121] mb-2">Empowering Sellers</h3>
                <p class="text-sm text-[#878787]">Giving India's small businesses the tools, reach, and resources to grow and succeed.</p>
            </div>
        </div>
    </div>

</div>
@endsection
