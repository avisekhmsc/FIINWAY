@extends('pages.layout')
@php $pageTitle = 'Careers at FIINWAY'; $pageSubtitle = "Join India's most ambitious marketplace team"; $breadcrumb = 'Company'; @endphp

@section('page-content')
<div class="space-y-8">

    <p class="text-[#666] leading-relaxed text-base">
        At FIINWAY, we're building the future of Indian e-commerce. We're looking for bold, passionate people who want to make a real impact on millions of lives across the country.
    </p>

    <div class="grid md:grid-cols-3 gap-4">
        <div class="p-5 bg-[#f1f3f6] rounded-sm text-center">
            <div class="text-3xl mb-2">🌍</div>
            <h3 class="font-bold">Remote-first</h3>
            <p class="text-sm text-[#878787] mt-1">Work from anywhere in India</p>
        </div>
        <div class="p-5 bg-[#f1f3f6] rounded-sm text-center">
            <div class="text-3xl mb-2">📈</div>
            <h3 class="font-bold">Fast growth</h3>
            <p class="text-sm text-[#878787] mt-1">Grow your skills and your career</p>
        </div>
        <div class="p-5 bg-[#f1f3f6] rounded-sm text-center">
            <div class="text-3xl mb-2">💰</div>
            <h3 class="font-bold">Great pay</h3>
            <p class="text-sm text-[#878787] mt-1">Competitive salaries + equity</p>
        </div>
    </div>

    <h2 class="text-xl font-bold text-[#212121]">Open Positions</h2>

    @foreach([
        ['title' => 'Senior Full Stack Engineer', 'dept' => 'Engineering', 'location' => 'Bengaluru / Remote'],
        ['title' => 'Product Manager – Buyer Experience', 'dept' => 'Product', 'location' => 'Mumbai / Remote'],
        ['title' => 'Data Scientist – Recommendations', 'dept' => 'Data & AI', 'location' => 'Hyderabad / Remote'],
        ['title' => 'Seller Success Manager', 'dept' => 'Operations', 'location' => 'Delhi / Remote'],
        ['title' => 'UI/UX Designer', 'dept' => 'Design', 'location' => 'Bengaluru / Remote'],
        ['title' => 'Growth Marketing Lead', 'dept' => 'Marketing', 'location' => 'Pan India / Remote'],
    ] as $job)
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 p-4 border border-slate-100 rounded-sm hover:border-[#006837] hover:shadow-sm transition">
        <div>
            <h3 class="font-bold text-[#212121]">{{ $job['title'] }}</h3>
            <div class="flex items-center gap-3 mt-1 text-xs text-[#878787]">
                <span class="flex items-center gap-1"><i class="ri-briefcase-line"></i> {{ $job['dept'] }}</span>
                <span class="flex items-center gap-1"><i class="ri-map-pin-line"></i> {{ $job['location'] }}</span>
            </div>
        </div>
        <button onclick="alert('Apply feature coming soon! Email your resume to careers@bazaarhub.in')" class="shrink-0 px-5 py-2 bg-[#006837] text-white text-sm font-medium rounded-sm hover:bg-green-800 transition">
            Apply Now
        </button>
    </div>
    @endforeach

    <div class="p-5 bg-green-50 border border-green-100 rounded-sm text-center">
        <p class="text-sm text-[#212121]">Don't see your role? Send us your resume at <strong>careers@bazaarhub.in</strong></p>
    </div>
</div>
@endsection
