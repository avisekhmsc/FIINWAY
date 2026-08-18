<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    /**
     * Toggle like/unlike on a product.
     * Returns JSON so the frontend can update the heart icon immediately.
     */
    public function toggle(Product $product)
    {
        $user = Auth::user();

        $existing = Wishlist::where('user_id', $user->id)
            ->where('product_id', $product->id)
            ->first();

        if ($existing) {
            $existing->delete();
            $liked = false;
        } else {
            Wishlist::create([
                'user_id'    => $user->id,
                'product_id' => $product->id,
            ]);
            $liked = true;
        }

        return response()->json([
            'liked' => $liked,
            'count' => Wishlist::where('product_id', $product->id)->count(),
        ]);
    }

    /**
     * Show the logged-in user's wishlist.
     */
    public function index()
    {
        $items = Wishlist::with('product.images', 'product.seller')
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('buyer.wishlist', compact('items'));
    }
}
