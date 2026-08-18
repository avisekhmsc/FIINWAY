<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Safely compute stats — guards against missing tables/columns on fresh deploys
        $stats = [
            'orders'   => 0,
            'wishlist' => 0,
            'reviews'  => 0,
            'returns'  => 0,
        ];

        try { $stats['orders']   = $user->orders()->count(); }   catch (\Throwable $e) {}
        try { $stats['wishlist'] = $user->wishlist()->count(); }  catch (\Throwable $e) {}
        try { $stats['reviews']  = $user->reviews()->count(); }   catch (\Throwable $e) {}
        try { $stats['returns']  = $user->returns()->count(); }   catch (\Throwable $e) {}

        return view('profile.index', compact('user', 'stats'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone'   => ['required', 'string', 'size:10', Rule::unique('users')->ignore($user->id)],
            'city'    => 'nullable|string|max:100',
            'state'   => 'nullable|string|max:100',
            'pincode' => 'nullable|string|max:10',
        ]);

        $user->update($validated);

        if ($request->filled('password')) {
            $request->validate([
                'password' => 'required|string|min:8|confirmed',
            ]);
            $user->update(['password' => Hash::make($request->password)]);
        }

        return back()->with('success', 'Profile updated successfully.');
    }
}
