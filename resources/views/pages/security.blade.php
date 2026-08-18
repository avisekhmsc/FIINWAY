@extends('pages.layout')
@php $pageTitle = 'Security'; $pageSubtitle = "Your safety is our highest priority"; $breadcrumb = 'Policy'; @endphp

@section('page-content')
<div class="space-y-8 text-[#212121]">

    <div class="grid sm:grid-cols-2 gap-5">
        @foreach([
            ['icon'=>'ri-lock-2-line','title'=>'256-bit SSL Encryption','desc'=>'Every page on FIINWAY is served over HTTPS with 256-bit SSL encryption, ensuring your data is always protected in transit.'],
            ['icon'=>'ri-shield-check-line','title'=>'PCI DSS Compliant','desc'=>'Our payment systems are fully PCI DSS Level 1 compliant — the highest standard for card payment processing security.'],
            ['icon'=>'ri-fingerprint-line','title'=>'Two-Factor Authentication','desc'=>'OTP-based login ensures only you can access your account. We never store your passwords in plain text.'],
            ['icon'=>'ri-eye-off-line','title'=>'No Card Data Stored','desc'=>'We never store your full card details on our servers. Payments are processed directly by our certified payment gateway.'],
            ['icon'=>'ri-bug-line','title'=>'Fraud Detection','desc'=>'AI-powered real-time monitoring flags suspicious transactions and logins before they cause harm.'],
            ['icon'=>'ri-customer-service-2-line','title'=>'24/7 Security Team','desc'=>'Our dedicated security team monitors the platform round the clock and responds to threats within minutes.'],
        ] as $item)
        <div class="flex gap-4 p-5 border border-slate-100 rounded-sm">
            <div class="text-3xl text-[#006837]"><i class="{{ $item['icon'] }}"></i></div>
            <div>
                <h3 class="font-bold mb-1">{{ $item['title'] }}</h3>
                <p class="text-sm text-[#878787]">{{ $item['desc'] }}</p>
            </div>
        </div>
        @endforeach
    </div>

    <div class="p-5 bg-[#f1f3f6] rounded-sm">
        <h2 class="text-lg font-bold mb-3">Report a Security Issue</h2>
        <p class="text-sm text-[#666] mb-4">Found a vulnerability? We take all security reports seriously. Our team will investigate and respond within 48 hours.</p>
        <a href="mailto:security@bazaarhub.in" class="inline-block px-6 py-2 bg-[#006837] text-white text-sm font-medium rounded-sm hover:bg-green-800 transition">
            <i class="ri-mail-line mr-1"></i> security@bazaarhub.in
        </a>
    </div>

    <div class="p-5 bg-amber-50 border border-amber-100 rounded-sm">
        <h3 class="font-bold text-[#212121] mb-2">⚠️ Stay Safe Online</h3>
        <ul class="text-sm text-[#666] space-y-1.5">
            <li>• FIINWAY will <strong>never</strong> ask for your OTP, password or full card number via phone or email.</li>
            <li>• Always check you are on <strong>bazaarhub.in</strong> (padlock icon in browser).</li>
            <li>• Do not share your account credentials with anyone.</li>
            <li>• Report suspicious calls or messages to our helpline immediately.</li>
        </ul>
    </div>

</div>
@endsection
