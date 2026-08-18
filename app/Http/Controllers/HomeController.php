<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {
        $categories = Category::where('is_active', true)
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->get();

        $recommended = Product::with(['images', 'seller', 'category'])
            ->where('status', 'active')
            ->orderByDesc('rating')
            ->orderByDesc('view_count')
            ->limit(12)
            ->get();

        $newProducts = Product::with(['images', 'seller'])
            ->where('status', 'active')
            ->where('condition_type', 'new')
            ->latest()
            ->limit(8)
            ->get();

        $oldProducts = Product::with(['images', 'seller'])
            ->where('status', 'active')
            ->where('condition_type', 'old')
            ->latest()
            ->limit(8)
            ->get();

        $cartCount = 0;
        if (Auth::check()) {
            $cart = Auth::user()->cart()->with('items')->first();
            $cartCount = $cart ? $cart->items_count : 0;
        }

        return view('home', compact('categories', 'recommended', 'newProducts', 'oldProducts', 'cartCount'));
    }
}
