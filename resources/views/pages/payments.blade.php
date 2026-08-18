@extends('pages.layout')
@php $pageTitle = 'Payment Methods'; $pageSubtitle = "Safe, secure and flexible payment options"; $breadcrumb = 'Help'; @endphp

@section('page-content')
<div class="space-y-8 text-[#212121]">

    <div class="grid sm:grid-cols-2 md:grid-cols-3 gap-4">
        @foreach([
            ['icon'=>'ri-bank-card-line','title'=>'Credit / Debit Cards','desc'=>'Visa, Mastercard, RuPay, Amex — all major cards accepted. 3D Secure authentication for every transaction.'],
            ['icon'=>'ri-smartphone-line','title'=>'UPI','desc'=>'Pay instantly via GPay, PhonePe, Paytm, BHIM and any UPI app. Zero transaction fees.'],
            ['icon'=>'ri-building-2-line','title'=>'Net Banking','desc'=>'Direct bank transfers from 50+ supported Indian banks including SBI, HDFC, ICICI, Axis and more.'],
            ['icon'=>'ri-wallet-3-line','title'=>'FIINWAY Wallet','desc'=>'Store money in your FIINWAY Wallet for quick one-click checkout. Earn cashback on wallet payments.'],
            ['icon'=>'ri-money-rupee-circle-line','title'=>'Cash on Delivery','desc'=>'Pay in cash when your order arrives. COD available for orders up to ₹50,000.'],
            ['icon'=>'ri-calendar-line','title'=>'EMI Options','desc'=>'No-cost EMI on credit cards and Bajaj Finserv. Convert big purchases into easy monthly instalments.'],
        ] as $p)
        <div class="p-5 border border-slate-100 rounded-sm">
            <div class="text-3xl text-[#006837] mb-3"><i class="{{ $p['icon'] }}"></i></div>
            <h3 class="font-bold mb-2">{{ $p['title'] }}</h3>
            <p class="text-sm text-[#878787] leading-relaxed">{{ $p['desc'] }}</p>
        </div>
        @endforeach
    </div>

    <div class="p-5 bg-green-50 border border-green-100 rounded-sm flex items-start gap-4">
        <i class="ri-shield-check-fill text-3xl text-[#388e3c]"></i>
        <div>
            <h3 class="font-bold text-[#212121]">100% Payment Protection</h3>
            <p class="text-sm text-[#878787] mt-1">All payments are encrypted end-to-end with 256-bit SSL. Your card details are never stored on our servers. FIINWAY is PCI DSS Level 1 certified.</p>
        </div>
    </div>

    <div>
        <h2 class="text-xl font-bold mb-4">Frequently Asked Questions</h2>
        <div class="space-y-3">
            @foreach([
                ['q'=>'When will my card be charged?','a'=>'Your card is charged only after you place the order. For COD, payment is collected at delivery.'],
                ['q'=>'Is my card information safe?','a'=>'Yes. We use industry-standard encryption. Your card details are processed directly by our payment partner and are never stored on FIINWAY servers.'],
                ['q'=>'What if my payment fails?','a'=>'If payment is deducted but the order is not placed, the amount will be auto-refunded within 5–7 business days to your original payment method.'],
            ] as $faq)
            <details class="border border-slate-100 rounded-sm">
                <summary class="px-4 py-3 font-medium text-[#212121] cursor-pointer hover:bg-[#f1f3f6]">{{ $faq['q'] }}</summary>
                <p class="px-4 pb-4 text-sm text-[#878787]">{{ $faq['a'] }}</p>
            </details>
            @endforeach
        </div>
    </div>

</div>
@endsection
