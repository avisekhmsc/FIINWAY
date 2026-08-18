@extends('pages.layout')
@php $pageTitle = 'Contact Us'; $pageSubtitle = "We're here to help — reach out any time"; $breadcrumb = 'Support'; @endphp

@section('page-content')
<div class="grid md:grid-cols-2 gap-10">

    {{-- Contact Form --}}
    <div>
        <h2 class="text-xl font-bold text-[#212121] mb-5">Send us a message</h2>
        <form class="space-y-4" onsubmit="alert('Thank you! We will get back to you within 24 hours.'); return false;">
            <div>
                <label class="block text-sm font-medium text-[#212121] mb-1">Full Name</label>
                <input type="text" placeholder="Your name" class="w-full px-3 py-2.5 border border-slate-200 rounded-sm text-sm outline-none focus:border-[#006837]">
            </div>
            <div>
                <label class="block text-sm font-medium text-[#212121] mb-1">Email / Phone</label>
                <input type="text" placeholder="email@example.com or 98XXXXXXXX" class="w-full px-3 py-2.5 border border-slate-200 rounded-sm text-sm outline-none focus:border-[#006837]">
            </div>
            <div>
                <label class="block text-sm font-medium text-[#212121] mb-1">Subject</label>
                <select class="w-full px-3 py-2.5 border border-slate-200 rounded-sm text-sm outline-none focus:border-[#006837] bg-white">
                    <option>Order Issue</option>
                    <option>Payment Problem</option>
                    <option>Return / Refund</option>
                    <option>Seller Support</option>
                    <option>Other</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-[#212121] mb-1">Message</label>
                <textarea rows="5" placeholder="Describe your issue in detail..." class="w-full px-3 py-2.5 border border-slate-200 rounded-sm text-sm outline-none focus:border-[#006837] resize-none"></textarea>
            </div>
            <button type="submit" class="w-full py-3 bg-[#006837] text-white font-medium rounded-sm hover:bg-green-800 transition uppercase tracking-wide">
                Submit
            </button>
        </form>
    </div>

    {{-- Contact Details --}}
    <div>
        <h2 class="text-xl font-bold text-[#212121] mb-5">Other ways to reach us</h2>
        <div class="space-y-5">
            <div class="flex items-start gap-4 p-4 bg-[#f1f3f6] rounded-sm">
                <div class="text-2xl text-[#006837]"><i class="ri-headphone-line"></i></div>
                <div>
                    <h3 class="font-bold text-[#212121] text-sm">Customer Support</h3>
                    <p class="text-[#878787] text-sm mt-1">1800-202-9898 (Toll Free)</p>
                    <p class="text-[#878787] text-xs mt-0.5">Mon – Sat, 9 AM – 8 PM</p>
                </div>
            </div>
            <div class="flex items-start gap-4 p-4 bg-[#f1f3f6] rounded-sm">
                <div class="text-2xl text-[#006837]"><i class="ri-mail-line"></i></div>
                <div>
                    <h3 class="font-bold text-[#212121] text-sm">Email Support</h3>
                    <p class="text-[#878787] text-sm mt-1">support@bazaarhub.in</p>
                    <p class="text-[#878787] text-xs mt-0.5">Response within 24 hours</p>
                </div>
            </div>
            <div class="flex items-start gap-4 p-4 bg-[#f1f3f6] rounded-sm">
                <div class="text-2xl text-[#006837]"><i class="ri-map-pin-line"></i></div>
                <div>
                    <h3 class="font-bold text-[#212121] text-sm">Head Office</h3>
                    <p class="text-[#878787] text-sm mt-1">FIINWAY Marketplace Pvt. Ltd.<br>12th Floor, Embassy TechVillage<br>Bengaluru, Karnataka – 560103</p>
                </div>
            </div>
            <div class="flex gap-4 mt-4">
                <a href="https://facebook.com" target="_blank" class="flex items-center gap-2 px-4 py-2 bg-[#1877f2] text-white text-sm rounded-sm hover:opacity-90 transition">
                    <i class="ri-facebook-fill"></i> Facebook
                </a>
                <a href="https://twitter.com" target="_blank" class="flex items-center gap-2 px-4 py-2 bg-[#000] text-white text-sm rounded-sm hover:opacity-90 transition">
                    <i class="ri-twitter-x-fill"></i> Twitter
                </a>
                <a href="https://instagram.com" target="_blank" class="flex items-center gap-2 px-4 py-2 text-white text-sm rounded-sm hover:opacity-90 transition" style="background: linear-gradient(135deg, #f09433, #e6683c, #dc2743, #cc2366, #bc1888);">
                    <i class="ri-instagram-fill"></i> Instagram
                </a>
            </div>
        </div>
    </div>

</div>
@endsection
