@extends('layouts.app', ['hideNav' => true])

@section('content')
<div class="min-h-screen flex flex-col bg-white">
    <div class="flex-1 flex flex-col items-center justify-center p-8 text-center relative overflow-hidden">
        
        <!-- Decoration -->
        <div class="absolute top-0 right-0 -mr-20 -mt-20 w-64 h-64 rounded-full bg-gradient-to-br from-indigo-100 to-purple-100 blur-3xl opacity-60"></div>
        <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-64 h-64 rounded-full bg-gradient-to-tr from-pink-100 to-orange-100 blur-3xl opacity-60"></div>

        <div class="relative z-10 w-full max-w-sm mx-auto">
            <div class="w-20 h-20 bg-indigo-50 rounded-2xl flex items-center justify-center mx-auto mb-8 shadow-sm">
                <i class="ri-shopping-bag-3-fill text-4xl text-indigo-600"></i>
            </div>
            
            <h1 class="text-3xl font-black text-slate-900 mb-4">Welcome to FIINWAY</h1>
            <p class="text-slate-500 mb-8 leading-relaxed">Discover amazing products, or sell your own items to millions of buyers.</p>

            <div class="space-y-4 w-full">
                <a href="{{ route('mobile') }}" class="btn btn-primary btn-block btn-lg shadow-xl shadow-indigo-200">
                    Get Started <i class="ri-arrow-right-line ml-1"></i>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
