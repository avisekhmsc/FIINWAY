@extends('layouts.app')

@section('title', $pageTitle . ' — FIINWAY')

@section('content')
<div class="bg-[#f1f3f6] min-h-screen pb-8">

    {{-- Hero Banner --}}
    <div style="background: linear-gradient(135deg, #172337 0%, #006837 100%);" class="text-white py-10 px-4">
        <div class="max-w-5xl mx-auto">
            @isset($breadcrumb)
            <p class="text-green-200 text-xs mb-2 uppercase tracking-widest">{{ $breadcrumb }}</p>
            @endisset
            <h1 class="text-2xl md:text-3xl font-bold">{{ $pageTitle }}</h1>
            @isset($pageSubtitle)
            <p class="text-green-100 mt-2 text-sm md:text-base">{{ $pageSubtitle }}</p>
            @endisset
        </div>
    </div>

    {{-- Content --}}
    <div class="max-w-5xl mx-auto px-4 py-8">
        <div class="bg-white rounded-sm shadow-sm p-6 md:p-10">
            @yield('page-content')
        </div>
    </div>

</div>
@endsection
