<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Payout;
use App\Models\SellerEarning;
use Illuminate\Support\Facades\Auth;

class EarningsController extends Controller
{
    public function index()
    {
        $seller = Auth::user();

        $earnings = SellerEarning::with(['order', 'orderItem.product'])
            ->where('seller_id', $seller->id)
            ->latest()
            ->paginate(15);

        $stats = [
            'total_sales'       => SellerEarning::where('seller_id', $seller->id)->sum('order_amount'),
            'total_commission'  => SellerEarning::where('seller_id', $seller->id)->sum('commission_amount'),
            'pending_amount'    => SellerEarning::where('seller_id', $seller->id)->whereIn('status', ['pending', 'customer_ok', 'on_hold'])->sum('seller_amount'),
            'released_amount'   => SellerEarning::where('seller_id', $seller->id)->where('status', 'released')->sum('seller_amount'),
            'referral_earning'  => \App\Models\ReferralReward::where('user_id', $seller->id)->where('status', 'credited')->sum('amount'),
        ];

        $stats['total_earning'] = $stats['released_amount'] + $stats['referral_earning'];

        $payouts = Payout::where('seller_id', $seller->id)->latest()->limit(5)->get();

        return view('seller.earnings', compact('earnings', 'stats', 'payouts'));
    }
}
