@extends('layouts.app')

@section('title', 'Complete Payment — FIINWAY')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8" x-data="paymentHub()">

    <!-- Header -->
    <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-xs flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Payment Gateway</h1>
            <p class="text-xs font-semibold text-slate-400 mt-1">Order #{{ $order->order_number }} • Subtotal ₹{{ number_format($order->total, 2) }}</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 text-xs font-black uppercase">
                <i class="ri-shield-check-fill"></i> 256-bit SSL Encrypted
            </span>
        </div>
    </div>

    <!-- Amount Banner -->
    <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 text-white p-6 sm:p-8 rounded-3xl shadow-xl flex flex-col sm:flex-row sm:items-center justify-between gap-6">
        <div>
            <span class="text-xs text-indigo-300 font-bold uppercase tracking-wider block">Total Amount Payable</span>
            <h2 class="text-3xl sm:text-4xl font-black text-white mt-1">₹{{ number_format($order->total, 2) }}</h2>
            <p class="text-xs text-slate-400 mt-1">Delivery Address: {{ $order->address->city ?? 'Registered Location' }}</p>
        </div>

        <div class="p-4 rounded-2xl bg-white/10 backdrop-blur-md border border-white/10 text-xs space-y-1 sm:text-right">
            <p class="font-bold text-white">Order Reference: {{ $order->order_number }}</p>
            <p class="text-slate-300">Transaction ID: {{ $payment->gateway_order_id ?? 'PENDING' }}</p>
        </div>
    </div>

    <!-- Payment Modes Selection Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <!-- Left 4 Cols: Payment Options Sidebar -->
        <div class="lg:col-span-4 space-y-2">
            <button type="button" @click="tab = 'upi'" :class="tab === 'upi' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/20' : 'bg-white text-slate-700 hover:bg-slate-100'" class="w-full p-4 rounded-2xl border border-slate-100 font-extrabold text-xs text-left transition-all flex items-center justify-between">
                <span class="flex items-center gap-3">
                    <i class="ri-qr-code-line text-lg"></i> UPI / QR Code
                </span>
                <i class="ri-arrow-right-s-line" :class="tab === 'upi' ? 'text-white' : 'text-slate-400'"></i>
            </button>

            <button type="button" @click="tab = 'card'" :class="tab === 'card' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/20' : 'bg-white text-slate-700 hover:bg-slate-100'" class="w-full p-4 rounded-2xl border border-slate-100 font-extrabold text-xs text-left transition-all flex items-center justify-between">
                <span class="flex items-center gap-3">
                    <i class="ri-bank-card-line text-lg"></i> Credit / Debit Card
                </span>
                <i class="ri-arrow-right-s-line" :class="tab === 'card' ? 'text-white' : 'text-slate-400'"></i>
            </button>

            <button type="button" @click="tab = 'netbanking'" :class="tab === 'netbanking' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/20' : 'bg-white text-slate-700 hover:bg-slate-100'" class="w-full p-4 rounded-2xl border border-slate-100 font-extrabold text-xs text-left transition-all flex items-center justify-between">
                <span class="flex items-center gap-3">
                    <i class="ri-building-line text-lg"></i> Net Banking
                </span>
                <i class="ri-arrow-right-s-line" :class="tab === 'netbanking' ? 'text-white' : 'text-slate-400'"></i>
            </button>

            <button type="button" @click="tab = 'cod'" :class="tab === 'cod' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/20' : 'bg-white text-slate-700 hover:bg-slate-100'" class="w-full p-4 rounded-2xl border border-slate-100 font-extrabold text-xs text-left transition-all flex items-center justify-between">
                <span class="flex items-center gap-3">
                    <i class="ri-hand-coin-line text-lg"></i> Cash on Delivery (COD)
                </span>
                <i class="ri-arrow-right-s-line" :class="tab === 'cod' ? 'text-white' : 'text-slate-400'"></i>
            </button>

            <button type="button" @click="tab = 'voucher'" :class="tab === 'voucher' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/20' : 'bg-white text-slate-700 hover:bg-slate-100'" class="w-full p-4 rounded-2xl border border-slate-100 font-extrabold text-xs text-left transition-all flex items-center justify-between">
                <span class="flex items-center gap-3">
                    <i class="ri-gift-line text-lg"></i> Gift Voucher / Wallet
                </span>
                <i class="ri-arrow-right-s-line" :class="tab === 'voucher' ? 'text-white' : 'text-slate-400'"></i>
            </button>
        </div>

        <!-- Right 8 Cols: Active Mode Form Content -->
        <div class="lg:col-span-8">
            <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-100 shadow-xs space-y-6">

                <!-- 1. UPI / QR CODE TAB -->
                <div x-show="tab === 'upi'" class="space-y-6" x-transition.opacity>
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-black text-slate-900">Scan & Pay with UPI</h3>
                            <p class="text-xs text-slate-400 font-semibold">Scan QR code using any UPI App (GPay, PhonePe, Paytm, BHIM)</p>
                        </div>
                        <span class="px-3 py-1 rounded-full bg-indigo-50 text-indigo-700 text-xs font-bold">Instant Verification</span>
                    </div>

                    <!-- Dynamic QR Code Container -->
                    <div class="p-6 rounded-3xl bg-slate-50 border border-slate-200 flex flex-col sm:flex-row items-center justify-center gap-6 text-center sm:text-left">
                        <div class="w-48 h-48 p-3 bg-white rounded-2xl border border-slate-200 shadow-md flex items-center justify-center shrink-0">
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=upi%3A%2F%2Fpay%3Fpa%3Dbazaarhub%40upi%26pn%3DFIINWAY%26am%3D{{ $order->total }}%26cu%3DINR" 
                                 alt="UPI QR Code" class="w-full h-full object-contain">
                        </div>
                        <div class="space-y-3">
                            <div class="space-y-1">
                                <span class="text-[10px] font-bold uppercase text-slate-400 tracking-wider">UPI ID / VPA</span>
                                <p class="text-sm font-black text-slate-900 font-mono">bazaarhub@upi</p>
                            </div>
                            <div class="flex items-center justify-center sm:justify-start gap-2 pt-2">
                                <span class="px-2.5 py-1 bg-white border border-slate-200 rounded-lg text-xs font-bold text-slate-700">Google Pay</span>
                                <span class="px-2.5 py-1 bg-white border border-slate-200 rounded-lg text-xs font-bold text-slate-700">PhonePe</span>
                                <span class="px-2.5 py-1 bg-white border border-slate-200 rounded-lg text-xs font-bold text-slate-700">Paytm</span>
                            </div>
                        </div>
                    </div>

                    <!-- Enter UPI ID manually -->
                    <div class="space-y-2">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Or Enter UPI ID / VPA</label>
                        <div class="flex gap-2">
                            <input type="text" x-model="upiId" placeholder="e.g. mobile@upi or username@okaxis" class="w-full px-4 py-3 rounded-xl border border-slate-200 text-xs font-medium outline-none focus:ring-2 focus:ring-indigo-600">
                        </div>
                    </div>

                    <button type="button" @click="submitPayment('upi')" class="w-full py-4 rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white font-extrabold text-sm shadow-xl shadow-indigo-600/20 transition-all active:scale-95 flex items-center justify-center gap-2">
                        <i class="ri-qr-code-line text-lg"></i> Complete UPI Payment of ₹{{ number_format($order->total, 2) }}
                    </button>
                </div>

                <!-- 2. CREDIT / DEBIT CARD TAB -->
                <div x-show="tab === 'card'" class="space-y-6" x-transition.opacity>
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-black text-slate-900">Credit / Debit Card</h3>
                        <div class="flex gap-2 text-xl text-slate-400">
                            <i class="ri-visa-line"></i>
                            <i class="ri-mastercard-line"></i>
                        </div>
                    </div>

                    <!-- Interactive Card Preview -->
                    <div class="p-6 rounded-3xl bg-gradient-to-tr from-slate-900 via-indigo-900 to-slate-900 text-white space-y-6 shadow-xl">
                        <div class="flex justify-between items-center">
                            <span class="text-xs font-bold tracking-widest text-indigo-300 uppercase">FIINWAY Secure Card</span>
                            <i class="ri-rfid-line text-2xl text-slate-300"></i>
                        </div>
                        <div class="font-mono text-lg sm:text-xl font-bold tracking-widest" x-text="cardNumber || '•••• •••• •••• ••••'"></div>
                        <div class="flex justify-between items-end text-xs">
                            <div>
                                <span class="text-[9px] text-slate-400 block uppercase">Card Holder</span>
                                <span class="font-bold text-white tracking-wider" x-text="cardName.toUpperCase() || 'YOUR NAME'"></span>
                            </div>
                            <div>
                                <span class="text-[9px] text-slate-400 block uppercase">Expires</span>
                                <span class="font-bold text-white font-mono" x-text="cardExpiry || 'MM/YY'"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Card Form -->
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Card Number</label>
                            <input type="text" x-model="cardNumber" placeholder="4532 •••• •••• 8901" maxlength="19" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-mono font-medium outline-none focus:ring-2 focus:ring-indigo-600">
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Expiry Date</label>
                                <input type="text" x-model="cardExpiry" placeholder="MM/YY" maxlength="5" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-mono font-medium outline-none focus:ring-2 focus:ring-indigo-600">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">CVV</label>
                                <input type="password" maxlength="3" placeholder="•••" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-mono font-medium outline-none focus:ring-2 focus:ring-indigo-600">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Cardholder Name</label>
                            <input type="text" x-model="cardName" placeholder="Name on card" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-medium outline-none focus:ring-2 focus:ring-indigo-600">
                        </div>
                    </div>

                    <button type="button" @click="submitPayment('card')" class="w-full py-4 rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white font-extrabold text-sm shadow-xl shadow-indigo-600/20 transition-all active:scale-95 flex items-center justify-center gap-2">
                        <i class="ri-lock-line"></i> Pay ₹{{ number_format($order->total, 2) }} Now
                    </button>
                </div>

                <!-- 3. NET BANKING TAB -->
                <div x-show="tab === 'netbanking'" class="space-y-6" x-transition.opacity>
                    <div>
                        <h3 class="text-lg font-black text-slate-900">Select Bank for Net Banking</h3>
                        <p class="text-xs text-slate-400 font-semibold mt-0.5">Redirects to your bank's secure authorization portal</p>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                        <label class="p-4 rounded-2xl border-2 cursor-pointer transition-all flex flex-col items-center justify-center gap-2 text-center"
                               :class="selectedBank === 'hdfc' ? 'border-indigo-600 bg-indigo-50/30' : 'border-slate-100 hover:border-slate-200'"
                               @click="selectedBank = 'hdfc'">
                            <i class="ri-building-4-line text-2xl text-blue-800"></i>
                            <span class="font-extrabold text-xs text-slate-900">HDFC Bank</span>
                        </label>

                        <label class="p-4 rounded-2xl border-2 cursor-pointer transition-all flex flex-col items-center justify-center gap-2 text-center"
                               :class="selectedBank === 'icici' ? 'border-indigo-600 bg-indigo-50/30' : 'border-slate-100 hover:border-slate-200'"
                               @click="selectedBank = 'icici'">
                            <i class="ri-bank-line text-2xl text-orange-700"></i>
                            <span class="font-extrabold text-xs text-slate-900">ICICI Bank</span>
                        </label>

                        <label class="p-4 rounded-2xl border-2 cursor-pointer transition-all flex flex-col items-center justify-center gap-2 text-center"
                               :class="selectedBank === 'sbi' ? 'border-indigo-600 bg-indigo-50/30' : 'border-slate-100 hover:border-slate-200'"
                               @click="selectedBank = 'sbi'">
                            <i class="ri-government-line text-2xl text-green-700"></i>
                            <span class="font-extrabold text-xs text-slate-900">State Bank of India</span>
                        </label>

                        <label class="p-4 rounded-2xl border-2 cursor-pointer transition-all flex flex-col items-center justify-center gap-2 text-center"
                               :class="selectedBank === 'axis' ? 'border-indigo-600 bg-indigo-50/30' : 'border-slate-100 hover:border-slate-200'"
                               @click="selectedBank = 'axis'">
                            <i class="ri-building-line text-2xl text-rose-700"></i>
                            <span class="font-extrabold text-xs text-slate-900">Axis Bank</span>
                        </label>

                        <label class="p-4 rounded-2xl border-2 cursor-pointer transition-all flex flex-col items-center justify-center gap-2 text-center"
                               :class="selectedBank === 'kotak' ? 'border-indigo-600 bg-indigo-50/30' : 'border-slate-100 hover:border-slate-200'"
                               @click="selectedBank = 'kotak'">
                            <i class="ri-store-3-line text-2xl text-red-600"></i>
                            <span class="font-extrabold text-xs text-slate-900">Kotak Bank</span>
                        </label>

                        <label class="p-4 rounded-2xl border-2 cursor-pointer transition-all flex flex-col items-center justify-center gap-2 text-center"
                               :class="selectedBank === 'pnb' ? 'border-indigo-600 bg-indigo-50/30' : 'border-slate-100 hover:border-slate-200'"
                               @click="selectedBank = 'pnb'">
                            <i class="ri-community-line text-2xl text-purple-700"></i>
                            <span class="font-extrabold text-xs text-slate-900">Punjab National Bank</span>
                        </label>
                    </div>

                    <button type="button" @click="submitPayment('netbanking')" class="w-full py-4 rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white font-extrabold text-sm shadow-xl shadow-indigo-600/20 transition-all active:scale-95 flex items-center justify-center gap-2">
                        Proceed with Net Banking (₹{{ number_format($order->total, 2) }})
                    </button>
                </div>

                <!-- 4. CASH ON DELIVERY TAB -->
                <div x-show="tab === 'cod'" class="space-y-6" x-transition.opacity>
                    <div>
                        <h3 class="text-lg font-black text-slate-900">Cash on Delivery (COD)</h3>
                        <p class="text-xs text-slate-400 font-semibold mt-0.5">Pay in cash when your package is delivered</p>
                    </div>

                    <div class="p-5 rounded-2xl bg-amber-50 border border-amber-200 space-y-2 text-xs font-semibold text-amber-900">
                        <div class="flex items-center gap-2 text-amber-800 font-bold">
                            <i class="ri-information-fill text-lg"></i> Notice for Cash on Delivery Orders:
                        </div>
                        <ul class="space-y-1 text-amber-700 font-medium pl-6 list-disc">
                            <li>Please keep exact change of ₹{{ number_format($order->total, 2) }} ready at delivery.</li>
                            <li>OTP verification will be requested by delivery partner upon package arrival.</li>
                        </ul>
                    </div>

                    <button type="button" @click="submitPayment('cod')" class="w-full py-4 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-sm shadow-xl shadow-emerald-600/20 transition-all active:scale-95 flex items-center justify-center gap-2">
                        <i class="ri-checkbox-circle-line text-lg"></i> Confirm Order with COD
                    </button>
                </div>

                <!-- 5. GIFT VOUCHER / WALLET TAB -->
                <div x-show="tab === 'voucher'" class="space-y-6" x-transition.opacity>
                    <div>
                        <h3 class="text-lg font-black text-slate-900">Gift Voucher & Wallet</h3>
                        <p class="text-xs text-slate-400 font-semibold mt-0.5">Redeem store credit or promo gift vouchers</p>
                    </div>

                    <div class="p-5 rounded-2xl bg-indigo-50 border border-indigo-100 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-indigo-600 text-white flex items-center justify-center font-black text-lg">
                                <i class="ri-wallet-3-line"></i>
                            </div>
                            <div>
                                <h4 class="font-extrabold text-slate-900 text-sm">FIINWAY Wallet</h4>
                                <p class="text-xs text-slate-500 font-medium">Available Balance: ₹{{ number_format(Auth::user()->wallet_balance ?? 500, 2) }}</p>
                            </div>
                        </div>
                        <span class="px-3 py-1 rounded-full bg-emerald-600 text-white text-xs font-black">Active</span>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Gift Voucher Code</label>
                        <div class="flex gap-2">
                            <input type="text" placeholder="GIFT-XXXX-XXXX" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-mono font-bold uppercase outline-none focus:ring-2 focus:ring-indigo-600">
                            <button type="button" class="px-5 py-2.5 rounded-xl bg-slate-900 text-white text-xs font-bold hover:bg-slate-800">
                                Apply
                            </button>
                        </div>
                    </div>

                    <button type="button" @click="submitPayment('voucher')" class="w-full py-4 rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white font-extrabold text-sm shadow-xl shadow-indigo-600/20 transition-all active:scale-95 flex items-center justify-center gap-2">
                        Pay ₹{{ number_format($order->total, 2) }} via Wallet / Gift Voucher
                    </button>
                </div>

            </div>
        </div>
    </div>

    <!-- Hidden Form for Payment Verification Submission -->
    <form id="paymentProcessForm" action="{{ route('payment.process', $order->id) }}" method="POST" class="hidden">
        @csrf
        <input type="hidden" name="payment_method" :value="paymentMethod">
        <input type="hidden" name="razorpay_order_id" value="{{ $payment->gateway_order_id }}">
        <input type="hidden" name="razorpay_payment_id" value="{{ $simulatedPaymentId }}">
        <input type="hidden" name="razorpay_signature" value="{{ $simulatedSignature }}">
    </form>

    <!-- Processing Overlay Modal -->
    <div x-show="processing" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/80 backdrop-blur-md" x-cloak>
        <div class="bg-white rounded-3xl p-8 max-w-sm w-full text-center space-y-4 shadow-2xl">
            <div class="w-16 h-16 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center mx-auto text-3xl animate-bounce">
                <i class="ri-shield-keyhole-line"></i>
            </div>
            <h3 class="text-xl font-black text-slate-900">Processing Payment...</h3>
            <p class="text-xs text-slate-500 font-medium">Verifying security signatures with payment gateway...</p>
            <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                <div class="bg-indigo-600 h-full animate-pulse w-full"></div>
            </div>
        </div>
    </div>

</div>

<script>
function paymentHub() {
    return {
        tab: 'upi',
        paymentMethod: 'upi',
        upiId: '',
        cardNumber: '',
        cardExpiry: '',
        cardName: '{{ Auth::user()->name }}',
        selectedBank: 'hdfc',
        processing: false,

        submitPayment(method) {
            this.paymentMethod = method;
            this.processing = true;

            setTimeout(() => {
                document.getElementById('paymentProcessForm').submit();
            }, 1000);
        }
    }
}
</script>
@endsection
