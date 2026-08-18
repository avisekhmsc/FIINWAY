<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\UserAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'full_name'     => 'required|string|max:255',
            'phone'         => 'required|string|digits:10',
            'address_line1' => 'required|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'city'          => 'required|string|max:100',
            'state'         => 'required|string|max:100',
            'pincode'       => 'required|string|digits:6',
            'label'         => 'required|in:Home,Work,Other',
            'is_default'    => 'nullable|boolean',
        ]);

        $user = Auth::user();
        $isFirst = $user->addresses()->count() === 0;

        if ($request->boolean('is_default') || $isFirst) {
            $user->addresses()->update(['is_default' => false]);
            $validated['is_default'] = true;
        }

        $address = $user->addresses()->create($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'address' => $address,
                'message' => 'Address added successfully.',
            ]);
        }

        return back()->with('success', 'Delivery address added successfully.');
    }

    public function destroy(UserAddress $address)
    {
        if ($address->user_id !== Auth::id()) {
            abort(403);
        }

        $address->delete();

        return back()->with('success', 'Address removed successfully.');
    }
}
