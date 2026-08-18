@extends('pages.layout')
@php $pageTitle = 'Press & Media'; $pageSubtitle = "Latest news, press releases and media resources"; $breadcrumb = 'Company'; @endphp

@section('page-content')
<div class="space-y-8">

    <div class="grid md:grid-cols-3 gap-5">
        @foreach([
            ['date'=>'Aug 2026','headline'=>'FIINWAY crosses 50 million registered users milestone','tag'=>'Growth'],
            ['date'=>'Jul 2026','headline'=>'FIINWAY partners with India Post for last-mile rural delivery','tag'=>'Partnership'],
            ['date'=>'Jun 2026','headline'=>'FIINWAY raises Series C funding led by Tiger Global','tag'=>'Funding'],
            ['date'=>'May 2026','headline'=>'New seller hub launched with AI-powered inventory forecasting','tag'=>'Product'],
            ['date'=>'Apr 2026','headline'=>'FIINWAY expands to 19,000+ pin codes across India','tag'=>'Expansion'],
            ['date'=>'Mar 2026','headline'=>'FIINWAY introduces Buy Now Pay Later with Bajaj Finance','tag'=>'Finance'],
        ] as $item)
        <div class="border border-slate-100 rounded-sm p-5 hover:shadow-sm transition">
            <span class="inline-block px-2 py-0.5 bg-green-50 text-[#006837] text-[10px] font-bold uppercase rounded mb-3">{{ $item['tag'] }}</span>
            <p class="text-xs text-[#878787] mb-2">{{ $item['date'] }}</p>
            <h3 class="font-medium text-[#212121] text-sm leading-snug">{{ $item['headline'] }}</h3>
            <button class="mt-3 text-xs text-[#006837] font-medium hover:underline">Read more →</button>
        </div>
        @endforeach
    </div>

    <hr class="border-slate-100">

    <div>
        <h2 class="text-xl font-bold text-[#212121] mb-4">Media Enquiries</h2>
        <div class="flex flex-col sm:flex-row gap-4">
            <div class="flex-1 p-5 bg-[#f1f3f6] rounded-sm">
                <h3 class="font-bold text-[#212121] mb-1">Press Contact</h3>
                <p class="text-sm text-[#878787]">press@bazaarhub.in</p>
                <p class="text-sm text-[#878787]">+91 80-4600-0000</p>
            </div>
            <div class="flex-1 p-5 bg-[#f1f3f6] rounded-sm">
                <h3 class="font-bold text-[#212121] mb-1">Media Kit</h3>
                <p class="text-sm text-[#878787] mb-3">Download our brand assets, logos and press kit.</p>
                <button onclick="alert('Media kit download coming soon!')" class="px-4 py-2 bg-[#006837] text-white text-sm rounded-sm hover:bg-green-800 transition">Download Kit</button>
            </div>
        </div>
    </div>

</div>
@endsection
