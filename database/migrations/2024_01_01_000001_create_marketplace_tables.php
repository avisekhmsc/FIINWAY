<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // User Addresses
        Schema::create('user_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('label')->default('Home'); // Home, Work, Other
            $table->string('full_name');
            $table->string('phone');
            $table->string('address_line1');
            $table->string('address_line2')->nullable();
            $table->string('city');
            $table->string('state');
            $table->string('pincode', 10);
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });

        // Categories
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('icon')->nullable(); // emoji or icon class
            $table->string('image')->nullable();
            $table->foreignId('parent_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Products
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // seller
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->enum('condition_type', ['new', 'old'])->default('new');
            $table->string('condition_label')->nullable(); // Like New, Good, Fair
            $table->decimal('selling_price', 12, 2);
            $table->decimal('original_price', 12, 2)->nullable();
            $table->decimal('discount_percent', 5, 2)->default(0);
            $table->integer('stock')->default(1);
            $table->string('brand')->nullable();
            $table->enum('delivery_type', ['self', 'courier', 'both'])->default('courier');
            $table->integer('delivery_days')->default(3);
            $table->boolean('pickup_available')->default(false);
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('pincode', 10)->nullable();
            // Old product extras
            $table->integer('product_age_months')->nullable();
            $table->boolean('bill_available')->default(false);
            $table->boolean('warranty_available')->default(false);
            $table->string('warranty_info')->nullable();
            $table->text('damage_details')->nullable();
            // Status
            $table->enum('status', ['pending', 'active', 'inactive', 'rejected', 'sold'])->default('pending');
            $table->string('reject_reason')->nullable();
            $table->decimal('rating', 3, 2)->default(0);
            $table->integer('rating_count')->default(0);
            $table->integer('view_count')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        // Product Images
        Schema::create('product_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('image_path');
            $table->boolean('is_primary')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Cart
        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });

        // Cart Items
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cart_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->integer('quantity')->default(1);
            $table->decimal('price', 12, 2); // price at time of adding
            $table->timestamps();
        });

        // Orders
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // buyer
            $table->foreignId('address_id')->constrained('user_addresses');
            $table->decimal('subtotal', 12, 2);
            $table->decimal('delivery_charge', 12, 2)->default(0);
            $table->decimal('discount', 12, 2)->default(0);
            $table->decimal('total', 12, 2);
            $table->string('coupon_code')->nullable();
            $table->enum('delivery_option', ['standard', 'express'])->default('standard');
            $table->enum('status', ['pending', 'confirmed', 'packed', 'shipped', 'out_for_delivery', 'delivered', 'cancelled', 'returned'])->default('pending');
            $table->enum('payment_status', ['pending', 'paid', 'failed', 'refunded'])->default('pending');
            $table->string('payment_method')->nullable();
            $table->string('transaction_id')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->boolean('customer_confirmed')->default(false);
            $table->timestamp('customer_confirmed_at')->nullable();
            $table->timestamps();
        });

        // Order Items
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained();
            $table->foreignId('seller_id')->constrained('users');
            $table->string('product_name');
            $table->decimal('price', 12, 2);
            $table->integer('quantity');
            $table->decimal('subtotal', 12, 2);
            $table->enum('status', ['pending', 'confirmed', 'packed', 'shipped', 'out_for_delivery', 'delivered', 'cancelled'])->default('pending');
            $table->timestamps();
        });

        // Shipments / Tracking
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('seller_id')->constrained('users');
            $table->string('courier_name')->nullable();
            $table->string('tracking_id')->nullable();
            $table->string('tracking_url')->nullable();
            $table->enum('status', ['confirmed', 'packed', 'shipped', 'out_for_delivery', 'delivered'])->default('confirmed');
            $table->date('expected_delivery')->nullable();
            $table->timestamps();
        });

        // Tracking Events
        Schema::create('tracking_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipment_id')->constrained()->cascadeOnDelete();
            $table->string('status');
            $table->string('description');
            $table->string('location')->nullable();
            $table->timestamp('event_at');
            $table->timestamps();
        });

        // Payments
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained(); // buyer
            $table->decimal('amount', 12, 2);
            $table->string('method'); // upi, card, wallet, netbanking
            $table->string('transaction_id')->nullable();
            $table->enum('status', ['pending', 'success', 'failed'])->default('pending');
            $table->json('gateway_response')->nullable();
            $table->timestamps();
        });

        // Seller Earnings / Commissions
        Schema::create('seller_earnings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_id')->constrained('users');
            $table->foreignId('order_id')->constrained();
            $table->foreignId('order_item_id')->constrained();
            $table->decimal('order_amount', 12, 2);
            $table->decimal('commission_percent', 5, 2)->default(10);
            $table->decimal('commission_amount', 12, 2);
            $table->decimal('seller_amount', 12, 2); // order_amount - commission
            $table->enum('status', ['pending', 'customer_ok', 'on_hold', 'released', 'failed'])->default('pending');
            $table->timestamp('customer_ok_at')->nullable();
            $table->timestamp('hold_until')->nullable();
            $table->timestamp('released_at')->nullable();
            $table->timestamps();
        });

        // Payouts
        Schema::create('payouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_id')->constrained('users');
            $table->decimal('amount', 12, 2);
            $table->string('bank_name')->nullable();
            $table->string('account_number')->nullable();
            $table->string('ifsc_code')->nullable();
            $table->string('upi_id')->nullable();
            $table->enum('status', ['pending', 'processing', 'done', 'failed'])->default('pending');
            $table->string('transaction_ref')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
        });

        // Referrals
        Schema::create('referrals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('referrer_id')->constrained('users'); // who referred
            $table->foreignId('referred_id')->constrained('users'); // who was referred
            $table->string('referral_code');
            $table->boolean('eligible_action_done')->default(false);
            $table->timestamp('eligible_at')->nullable();
            $table->timestamps();
        });

        // Referral Rewards
        Schema::create('referral_rewards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained(); // who gets reward
            $table->foreignId('referral_id')->constrained();
            $table->decimal('amount', 10, 2);
            $table->enum('status', ['pending', 'credited', 'expired'])->default('pending');
            $table->timestamp('credited_at')->nullable();
            $table->timestamps();
        });

        // Notifications
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('body');
            $table->string('type')->default('general'); // order, payment, system
            $table->string('icon')->nullable();
            $table->json('data')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamps();
        });

        // Reviews
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('order_id')->constrained();
            $table->tinyInteger('rating'); // 1-5
            $table->text('comment')->nullable();
            $table->boolean('is_approved')->default(true);
            $table->timestamps();
        });

        // Returns
        Schema::create('returns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained();
            $table->foreignId('user_id')->constrained(); // buyer
            $table->string('reason');
            $table->text('description')->nullable();
            $table->enum('status', ['requested', 'approved', 'rejected', 'completed'])->default('requested');
            $table->timestamps();
        });

        // Refunds
        Schema::create('refunds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained();
            $table->foreignId('payment_id')->constrained();
            $table->decimal('amount', 12, 2);
            $table->string('reason')->nullable();
            $table->enum('status', ['pending', 'processed', 'failed'])->default('pending');
            $table->string('transaction_ref')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
        });

        // Coupons
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('description')->nullable();
            $table->enum('type', ['percent', 'flat'])->default('percent');
            $table->decimal('value', 10, 2);
            $table->decimal('min_order', 10, 2)->default(0);
            $table->decimal('max_discount', 10, 2)->nullable();
            $table->integer('usage_limit')->nullable();
            $table->integer('used_count')->default(0);
            $table->date('expires_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // App Settings
        Schema::create('app_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('group')->default('general');
            $table->string('label')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('app_settings');
        Schema::dropIfExists('coupons');
        Schema::dropIfExists('refunds');
        Schema::dropIfExists('returns');
        Schema::dropIfExists('reviews');
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('referral_rewards');
        Schema::dropIfExists('referrals');
        Schema::dropIfExists('payouts');
        Schema::dropIfExists('seller_earnings');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('tracking_events');
        Schema::dropIfExists('shipments');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('cart_items');
        Schema::dropIfExists('carts');
        Schema::dropIfExists('product_images');
        Schema::dropIfExists('products');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('user_addresses');
    }
};
