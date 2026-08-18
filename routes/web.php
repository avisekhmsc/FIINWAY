<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Buyer\AddressController;
use App\Http\Controllers\Buyer\WishlistController;
use App\Http\Controllers\Buyer\CartController;
use App\Http\Controllers\Buyer\NotificationController;
use App\Http\Controllers\Buyer\OrderController as BuyerOrderController;
use App\Http\Controllers\Buyer\ProductController as BuyerProductController;
use App\Http\Controllers\Buyer\ReturnController;
use App\Http\Controllers\Buyer\ReviewController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\Seller\DashboardController as SellerDashboard;
use App\Http\Controllers\Seller\EarningsController;
use App\Http\Controllers\Seller\OrderController as SellerOrderController;
use App\Http\Controllers\Seller\ProductController as SellerProductController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Middleware\AdminMiddleware;
use Illuminate\Support\Facades\Route;

// ─── Static / Info Pages ────────────────────────────────────────────────────
Route::get('/about',         [PageController::class, 'about'])->name('page.about');
Route::get('/contact',       [PageController::class, 'contact'])->name('page.contact');
Route::get('/careers',       [PageController::class, 'careers'])->name('page.careers');
Route::get('/press',         [PageController::class, 'press'])->name('page.press');
Route::get('/help/payments', [PageController::class, 'payments'])->name('page.payments');
Route::get('/help/shipping', [PageController::class, 'shipping'])->name('page.shipping');
Route::get('/return-policy', [PageController::class, 'returnPolicy'])->name('page.return-policy');
Route::get('/terms',         [PageController::class, 'terms'])->name('page.terms');
Route::get('/security',      [PageController::class, 'security'])->name('page.security');
Route::get('/privacy',       [PageController::class, 'privacy'])->name('page.privacy');
Route::get('/sell-online',   [PageController::class, 'sellOnline'])->name('page.sell-online');

// ─── Auth Routes ─────────────────────────────────────────────────────────────
Route::get('/', [AuthController::class, 'splash'])->name('splash');
Route::get('/welcome', [AuthController::class, 'welcome'])->name('welcome');
Route::get('/login', [AuthController::class, 'showMobile'])->name('mobile');
Route::post('/send-otp', [AuthController::class, 'sendOtp'])->middleware('throttle:5,1')->name('otp.send');
Route::get('/verify-otp', [AuthController::class, 'showOtp'])->name('otp.verify');
Route::post('/verify-otp', [AuthController::class, 'verifyOtp'])->name('otp.verify.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ─── Razorpay Webhook (Exempt from Auth & CSRF) ─────────────────────────────
Route::post('/webhooks/razorpay', [\App\Http\Controllers\Webhook\RazorpayWebhookController::class, 'handleWebhook'])->name('webhooks.razorpay');

// ─── Profile Setup (after OTP) ────────────────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/setup-profile', [AuthController::class, 'showProfileSetup'])->name('profile.setup');
    Route::post('/setup-profile', [AuthController::class, 'saveProfile'])->name('profile.save');
});

// ─── Home ─────────────────────────────────────────────────────────────────────
Route::get('/home', [HomeController::class, 'index'])->name('home');

// ─── Product Browsing (public) ────────────────────────────────────────────────
Route::get('/products', [BuyerProductController::class, 'index'])->name('products');
Route::get('/products/{product:slug}', [BuyerProductController::class, 'show'])->name('products.show');

// ─── Buyer Protected Routes ───────────────────────────────────────────────────
Route::middleware('auth')->group(function () {
    // Addresses
    Route::post('/addresses', [AddressController::class, 'store'])->name('addresses.store');
    Route::delete('/addresses/{address}', [AddressController::class, 'destroy'])->name('addresses.destroy');

    // Wishlist (like/unlike)
    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist');
    Route::post('/wishlist/{product}/toggle', [WishlistController::class, 'toggle'])->name('wishlist.toggle');

    // Cart
    Route::get('/cart', [CartController::class, 'index'])->name('cart');
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::patch('/cart/items/{item}', [CartController::class, 'update'])->name('cart.update');
    Route::post('/cart/remove/{item}', [CartController::class, 'remove'])->name('cart.remove');

    // Profile
    Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'index'])->name('profile');
    Route::post('/profile', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');

    // Checkout & Orders
    Route::post('/cart/coupon', [CartController::class, 'applyCoupon'])->name('cart.coupon');
    Route::delete('/cart/coupon', [CartController::class, 'removeCoupon'])->name('cart.coupon.remove');

    // Checkout & Orders
    Route::get('/checkout', [BuyerOrderController::class, 'checkout'])->name('checkout');
    Route::post('/checkout', [BuyerOrderController::class, 'placeOrder'])->name('order.place');
    Route::get('/payment/{order}', [BuyerOrderController::class, 'payment'])->name('payment');
    Route::post('/payment/{order}', [BuyerOrderController::class, 'processPayment'])->name('payment.process');
    Route::post('/payment/{order}/verify', [BuyerOrderController::class, 'verifyPayment'])->name('payment.verify');
    Route::get('/payment/{order}/success', [BuyerOrderController::class, 'paymentSuccess'])->name('payment.success');

    // My Orders
    Route::get('/orders', [BuyerOrderController::class, 'myOrders'])->name('orders');
    Route::get('/orders/{order}', [BuyerOrderController::class, 'show'])->name('orders.show');
    Route::get('/orders/{order}/track', [BuyerOrderController::class, 'track'])->name('orders.track');
    Route::post('/orders/{order}/confirm-receipt', [BuyerOrderController::class, 'confirmReceipt'])->name('orders.confirm-receipt');

    // Reviews
    Route::get('/reviews/create', [ReviewController::class, 'create'])->name('reviews.create');
    Route::post('/reviews', [ReviewController::class, 'store'])->name('reviews.store');

    // Returns
    Route::get('/returns', [ReturnController::class, 'index'])->name('returns.index');
    Route::get('/returns/create', [ReturnController::class, 'create'])->name('returns.create');
    Route::post('/returns', [ReturnController::class, 'store'])->name('returns.store');

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.read-all');

    // Chat
    Route::get('/chat/{order}/{seller}', [ChatController::class, 'show'])->name('chat.show');
    Route::post('/chat/{conversation}', [ChatController::class, 'store'])->name('chat.store');
});

// ─── Seller Routes ────────────────────────────────────────────────────────────
Route::middleware('auth')->prefix('seller')->name('seller.')->group(function () {
    Route::get('/dashboard', [SellerDashboard::class, 'index'])->name('dashboard');

    // Products
    Route::get('/products', [SellerProductController::class, 'index'])->name('products');
    Route::get('/products/create', [SellerProductController::class, 'create'])->name('products.create');
    Route::post('/products', [SellerProductController::class, 'store'])->name('products.store');
    Route::get('/products/{product}/edit', [SellerProductController::class, 'edit'])->name('products.edit');
    Route::put('/products/{product}', [SellerProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{product}', [SellerProductController::class, 'destroy'])->name('products.destroy');

    // Orders
    Route::get('/orders', [SellerOrderController::class, 'index'])->name('orders');
    Route::get('/orders/{item}', [SellerOrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{item}/confirm', [SellerOrderController::class, 'confirm'])->name('orders.confirm');
    Route::post('/orders/{item}/pack', [SellerOrderController::class, 'pack'])->name('orders.pack');
    Route::post('/orders/{item}/ship', [SellerOrderController::class, 'ship'])->name('orders.ship');
    Route::post('/orders/{item}/out-for-delivery', [SellerOrderController::class, 'outForDelivery'])->name('orders.out-for-delivery');
    Route::post('/orders/{item}/deliver', [SellerOrderController::class, 'deliver'])->name('orders.deliver');

    // Earnings
    Route::get('/earnings', [EarningsController::class, 'index'])->name('earnings');
});

// ─── Admin Routes ─────────────────────────────────────────────────────────────
Route::middleware(['auth', AdminMiddleware::class])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    // Products
    Route::get('/products', [AdminController::class, 'products'])->name('products');
    Route::post('/products/{product}/approve', [AdminController::class, 'approveProduct'])->name('products.approve');
    Route::post('/products/{product}/reject', [AdminController::class, 'rejectProduct'])->name('products.reject');
    Route::post('/products/{product}/toggle', [AdminController::class, 'toggleProduct'])->name('products.toggle');
    Route::get('/products/{product}/edit', [AdminController::class, 'editProduct'])->name('products.edit');
    Route::put('/products/{product}', [AdminController::class, 'updateProduct'])->name('products.update');

    // Users
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::post('/users/{user}/toggle-block', [AdminController::class, 'toggleBlock'])->name('users.toggle-block');

    // Orders
    Route::get('/orders', [AdminController::class, 'orders'])->name('orders');

    // Payouts
    Route::get('/payouts', [AdminController::class, 'payouts'])->name('payouts');
    Route::post('/payouts/{earning}/release', [AdminController::class, 'releasePayout'])->name('payouts.release');

    // Categories
    Route::get('/categories', [AdminController::class, 'categories'])->name('categories');
    Route::post('/categories', [AdminController::class, 'storeCategory'])->name('categories.store');

    // Settings
    Route::get('/settings', [AdminController::class, 'settings'])->name('settings');
    Route::post('/settings', [AdminController::class, 'updateSettings'])->name('settings.update');

    // Referrals
    Route::get('/referrals', [AdminController::class, 'referrals'])->name('referrals');

    // Returns
    Route::get('/returns', [AdminController::class, 'returns'])->name('returns');
    Route::post('/returns/{return}/process', [AdminController::class, 'processReturn'])->name('returns.process');

    // Refunds
    Route::get('/refunds', [AdminController::class, 'refunds'])->name('refunds');
    Route::post('/refunds/{refund}/process', [AdminController::class, 'processRefund'])->name('refunds.process');
});

