@extends('pages.layout')
@php $pageTitle = 'Return Policy'; $pageSubtitle = "Easy, hassle-free returns within 7 days"; $breadcrumb = 'Policy'; @endphp

@section('page-content')
<div class="space-y-8 text-[#212121]">

    <div class="grid sm:grid-cols-3 gap-4 text-center">
        <div class="p-5 bg-[#f1f3f6] rounded-sm">
            <div class="text-4xl mb-2">📅</div>
            <h3 class="font-bold">7-Day Returns</h3>
            <p class="text-sm text-[#878787] mt-1">Most products eligible for return within 7 days of delivery</p>
        </div>
        <div class="p-5 bg-[#f1f3f6] rounded-sm">
            <div class="text-4xl mb-2">🚚</div>
            <h3 class="font-bold">Free Pickup</h3>
            <p class="text-sm text-[#878787] mt-1">We'll pick up your return item from your doorstep</p>
        </div>
        <div class="p-5 bg-[#f1f3f6] rounded-sm">
            <div class="text-4xl mb-2">💰</div>
            <h3 class="font-bold">Quick Refund</h3>
            <p class="text-sm text-[#878787] mt-1">Refund processed in 5–7 business days after pickup</p>
        </div>
    </div>

    <div>
        <h2 class="text-xl font-bold mb-4">How to Return</h2>
        <div class="space-y-3">
            @foreach([
                ['step'=>'1','title'=>'Initiate Return','desc'=>'Go to My Orders → Select the item → Click "Return Item" and choose your reason.'],
                ['step'=>'2','title'=>'Schedule Pickup','desc'=>'Choose a pickup date and address. Our logistics partner will collect the item.'],
                ['step'=>'3','title'=>'Item Inspected','desc'=>'Once received, the item is quality-checked within 24–48 hours.'],
                ['step'=>'4','title'=>'Refund Issued','desc'=>'Approved refunds are credited to your original payment method in 5–7 business days.'],
            ] as $s)
            <div class="flex gap-4 p-4 border border-slate-100 rounded-sm">
                <div class="w-8 h-8 rounded-full bg-[#006837] text-white font-bold flex items-center justify-center shrink-0 text-sm">{{ $s['step'] }}</div>
                <div>
                    <h3 class="font-bold text-[#212121]">{{ $s['title'] }}</h3>
                    <p class="text-sm text-[#878787] mt-0.5">{{ $s['desc'] }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <div>
        <h2 class="text-xl font-bold mb-4">Non-Returnable Items</h2>
        <ul class="list-disc list-inside space-y-1.5 text-sm text-[#666]">
            <li>Perishables — food, groceries, flowers</li>
            <li>Undergarments, lingerie, swimwear (hygiene)</li>
            <li>Digital downloads and software licenses</li>
            <li>Items marked "non-returnable" on the product page</li>
            <li>Items with tampered packaging or missing serial numbers</li>
        </ul>
    </div>

    <div class="p-5 bg-orange-50 border border-orange-100 rounded-sm">
        <h3 class="font-bold text-[#212121] mb-1">Need to return something?</h3>
        <p class="text-sm text-[#878787] mb-3">Start a return request directly from your orders page.</p>
        <a href="{{ route('returns.create') }}" class="inline-block px-6 py-2 bg-[#e94f1c] text-white text-sm font-medium rounded-sm hover:bg-[#cc4214] transition">Start a Return</a>
    </div>

</div>
@endsection
