@extends('pages.layout')
@php $pageTitle = 'Terms of Use'; $pageSubtitle = "Please read these terms carefully before using FIINWAY"; $breadcrumb = 'Legal'; @endphp

@section('page-content')
<div class="space-y-6 text-sm text-[#444] leading-relaxed">

    <p class="text-xs text-[#878787]">Last updated: 1 August 2026</p>

    @foreach([
        ['h'=>'1. Acceptance of Terms','body'=>'By accessing or using FIINWAY (bazaarhub.in), you agree to be bound by these Terms of Use and all applicable laws and regulations. If you do not agree with any of these terms, you are prohibited from using or accessing this site.'],
        ['h'=>'2. Use of Platform','body'=>'FIINWAY is an online marketplace connecting buyers and sellers. You agree to use the platform only for lawful purposes and in a manner that does not infringe the rights of others. You must be at least 18 years old or have parental consent to use this platform.'],
        ['h'=>'3. Account Responsibility','body'=>'You are responsible for maintaining the confidentiality of your account credentials. Any activity under your account is your responsibility. Notify us immediately at security@bazaarhub.in if you suspect unauthorized access.'],
        ['h'=>'4. Product Listings','body'=>'Sellers are responsible for the accuracy of product descriptions, images, and pricing. FIINWAY reserves the right to remove any listing that violates our policies or applicable laws.'],
        ['h'=>'5. Payments & Pricing','body'=>'All prices are in Indian Rupees (₹) and inclusive of applicable taxes unless stated otherwise. FIINWAY does not collect payments on behalf of sellers — all transactions are processed through our secure payment gateway.'],
        ['h'=>'6. Intellectual Property','body'=>'All content on FIINWAY including logos, text, images and software is the property of FIINWAY Marketplace Pvt. Ltd. or its content suppliers and is protected under Indian copyright law.'],
        ['h'=>'7. Limitation of Liability','body'=>'FIINWAY shall not be liable for any indirect, incidental, special, or consequential damages resulting from your use of the platform. Our liability is limited to the value of the transaction in question.'],
        ['h'=>'8. Governing Law','body'=>'These terms shall be governed by and construed in accordance with the laws of India. Any disputes arising shall be subject to the exclusive jurisdiction of courts in Bengaluru, Karnataka.'],
        ['h'=>'9. Changes to Terms','body'=>'FIINWAY reserves the right to modify these terms at any time. Continued use of the platform after changes constitutes acceptance of the new terms.'],
        ['h'=>'10. Contact','body'=>'For questions about these Terms, contact us at legal@bazaarhub.in or write to FIINWAY Marketplace Pvt. Ltd., 12th Floor, Embassy TechVillage, Bengaluru – 560103.'],
    ] as $s)
    <div>
        <h2 class="text-base font-bold text-[#212121] mb-2">{{ $s['h'] }}</h2>
        <p>{{ $s['body'] }}</p>
    </div>
    @endforeach

</div>
@endsection
