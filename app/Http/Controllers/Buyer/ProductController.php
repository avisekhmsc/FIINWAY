<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['images', 'seller', 'category'])
            ->where('status', 'active');

        // Search
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%')
                  ->orWhereHas('category', fn($c) => $c->where('name', 'like', '%' . $request->search . '%'));
            });
        }

        // Filters
        if ($request->condition) $query->where('condition_type', $request->condition);
        if ($request->category) $query->where('category_id', $request->category);
        if ($request->min_price) $query->where('selling_price', '>=', $request->min_price);
        if ($request->max_price) $query->where('selling_price', '<=', $request->max_price);
        if ($request->city) $query->where('city', 'like', '%' . $request->city . '%');
        if ($request->min_rating) $query->where('rating', '>=', $request->min_rating);
        
        // Nearby Filtering (Locality based matching on Pincode since no GPS coords exist)
        if ($request->nearby && Auth::check()) {
            $userPincode = Auth::user()->pincode;
            if ($userPincode) {
                // Assuming same pincode or starting with same 3 digits means nearby
                $prefix = substr($userPincode, 0, 3);
                $query->where('pincode', 'like', $prefix . '%');
            }
        }

        // Sort
        match ($request->sort) {
            'price_asc'  => $query->orderBy('selling_price'),
            'price_desc' => $query->orderByDesc('selling_price'),
            'newest'     => $query->latest(),
            'rating'     => $query->orderByDesc('rating'),
            default      => $query->orderByDesc('view_count'),
        };

        $products = $query->paginate(12)->withQueryString();
        $categories = Category::where('is_active', true)->get();

        if ($request->ajax() || $request->wantsJson()) {
            $html = '';
            foreach ($products as $product) {
                $html .= view('components.product-card', ['product' => $product])->render();
            }
            return response()->json([
                'html'     => $html,
                'hasMore'  => $products->hasMorePages(),
                'nextPage' => $products->currentPage() + 1,
                'total'    => $products->total(),
            ]);
        }

        return view('buyer.products.index', compact('products', 'categories'));
    }

    public function show(Product $product)
    {
        if ($product->status !== 'active') abort(404);

        $product->increment('view_count');
        $product->load(['images', 'seller', 'category', 'reviews.user']);

        $related = Product::with(['images'])
            ->where('status', 'active')
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->limit(4)
            ->get();

        $inCart = false;
        if (Auth::check()) {
            $cart = Auth::user()->cart()->first();
            if ($cart) {
                $inCart = $cart->items()->where('product_id', $product->id)->exists();
            }
        }

        return view('buyer.products.show', compact('product', 'related', 'inCart'));
    }
}
