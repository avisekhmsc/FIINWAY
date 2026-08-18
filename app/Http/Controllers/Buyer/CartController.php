<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Coupon;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    private function getOrCreateCart()
    {
        return Auth::user()->cart()->firstOrCreate(['user_id' => Auth::id()]);
    }

    public function index()
    {
        $cart = $this->getOrCreateCart();
        $cart->load(['items.product.images', 'items.product.seller']);

        $subtotal = $cart->subtotal;
        $standardDelivery = (float)\App\Models\AppSetting::get('standard_delivery_fee', 49);
        $freeDeliveryThreshold = (float)\App\Models\AppSetting::get('free_delivery_threshold', 500);
        $delivery = $subtotal > $freeDeliveryThreshold ? 0 : $standardDelivery;
        $total = $subtotal + $delivery;

        return view('buyer.cart', compact('cart', 'subtotal', 'delivery', 'total'));
    }

    public function add(Request $request)
    {
        $request->validate(['product_id' => 'required|exists:products,id', 'quantity' => 'integer|min:1|max:10']);

        $product = Product::findOrFail($request->product_id);

        if ($product->status !== 'active') {
            return back()->withErrors(['error' => 'Product is not available.']);
        }

        $cart = $this->getOrCreateCart();
        $existing = $cart->items()->where('product_id', $product->id)->first();

        if ($existing) {
            $existing->increment('quantity', $request->quantity ?? 1);
        } else {
            $cart->items()->create([
                'product_id' => $product->id,
                'quantity'   => $request->quantity ?? 1,
                'price'      => $product->selling_price,
            ]);
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'cart_count' => $cart->items()->sum('quantity')]);
        }

        if ($request->input('action') === 'buy_now') {
            return redirect()->route('checkout');
        }

        return back()->with('success', 'Product added to cart!');
    }

    public function update(Request $request, CartItem $item)
    {
        $request->validate(['quantity' => 'required|integer|min:1|max:10']);

        // Make sure item belongs to user's cart
        $cart = $this->getOrCreateCart();
        // Basic check since we don't have a CartItem policy
        if ($item->cart_id !== $cart->id) abort(403);

        $item->update(['quantity' => $request->quantity]);

        return back()->with('success', 'Cart updated.');
    }

    public function remove(CartItem $item)
    {
        $cart = $this->getOrCreateCart();
        // Basic check since we don't have a CartItem policy
        if ($item->cart_id !== $cart->id) abort(403);
        $item->delete();

        return back()->with('success', 'Item removed from cart.');
    }

    public function applyCoupon(Request $request)
    {
        $request->validate(['code' => 'required|string']);

        $cart = $this->getOrCreateCart();
        $coupon = Coupon::where('code', strtoupper($request->code))->first();

        if (!$coupon || !$coupon->isValid($cart->subtotal)) {
            return back()->withErrors(['coupon' => 'Invalid or expired coupon code.']);
        }

        session(['coupon_code' => $coupon->code, 'coupon_discount' => $coupon->calculateDiscount($cart->subtotal)]);
        return back()->with('success', 'Coupon applied!');
    }

    public function removeCoupon()
    {
        session()->forget(['coupon_code', 'coupon_discount']);
        return back()->with('success', 'Coupon removed.');
    }
}
