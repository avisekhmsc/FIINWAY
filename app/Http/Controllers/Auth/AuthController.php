<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use App\Models\Referral;
use App\Models\ReferralReward;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    // Show splash / welcome
    public function splash()
    {
        if (Auth::check()) return redirect()->route('home');
        return view('auth.splash');
    }

    public function welcome()
    {
        if (Auth::check()) return redirect()->route('home');
        return view('auth.welcome');
    }

    // Show mobile input form
    public function showMobile()
    {
        if (Auth::check()) return redirect()->route('home');
        return view('auth.mobile');
    }

    // Send OTP
    public function sendOtp(Request $request)
    {
        if (Auth::check()) return redirect()->route('home');
        $request->validate(['phone' => 'required|digits:10']);

        $phone = $request->phone;
        // Generate 6-digit OTP (in production send via SMS)
        $otp = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        $isNew = !User::where('phone', $phone)->exists();

        $user = User::firstOrCreate(
            ['phone' => $phone],
            [
                'name' => 'User',
                'password' => Hash::make(Str::random(16)),
                'referral_code' => strtoupper(Str::random(8)),
            ]
        );

        $user->update([
            'otp' => $otp,
            'otp_expires_at' => now()->addMinutes(10),
        ]);

        // Store in session for OTP verification
        session(['otp_phone' => $phone, 'otp_is_new' => $isNew]);

        // In real app: send SMS. For demo, flash OTP
        return redirect()->route('otp.verify')->with('demo_otp', $otp)->with('is_new', $isNew);
    }

    // Show OTP form
    public function showOtp()
    {
        if (Auth::check()) return redirect()->route('home');
        if (!session('otp_phone')) return redirect()->route('mobile');
        return view('auth.otp');
    }

    // Verify OTP
    public function verifyOtp(Request $request)
    {
        $request->validate(['otp' => 'required|digits:6']);

        $phone = session('otp_phone');
        if (!$phone) return redirect()->route('mobile')->withErrors(['phone' => 'Session expired. Please try again.']);

        $user = User::where('phone', $phone)->first();

        if (!$user || $user->otp !== $request->otp || $user->otp_expires_at < now()) {
            return back()->withErrors(['otp' => 'Invalid or expired OTP.']);
        }

        $user->update(['otp' => null, 'otp_expires_at' => null, 'phone_verified_at' => now()]);

        Auth::login($user);
        session()->forget(['otp_phone', 'otp_is_new']);

        // Check if profile is complete
        if ($user->name === 'User' || !$user->city) {
            return redirect()->route('profile.setup');
        }

        return redirect()->route('home');
    }

    // Show profile setup
    public function showProfileSetup()
    {
        return view('auth.profile-setup');
    }

    // Save profile
    public function saveProfile(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'pincode' => 'required|digits:6',
            'referred_by' => 'nullable|string|max:10',
        ]);

        $user = Auth::user();
        $user->update($request->only(['name', 'city', 'state', 'pincode']));

        // Handle referral
        if ($request->referred_by && $request->referred_by !== $user->referral_code) {
            $referrer = User::where('referral_code', $request->referred_by)->first();
            if ($referrer && !Referral::where('referred_id', $user->id)->exists()) {
                $ref = Referral::create([
                    'referrer_id' => $referrer->id,
                    'referred_id' => $user->id,
                    'referral_code' => $request->referred_by,
                ]);
                // Give reward when eligible action is done (first order)
            }
        }

        return redirect()->route('home');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('splash');
    }
}
