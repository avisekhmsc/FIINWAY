<?php

namespace Tests\Feature;

use App\Console\Commands\ReleasePayouts;
use App\Models\AppSetting;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Notification;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Payout;
use App\Models\Product;
use App\Models\Refund;
use App\Models\ReturnRequest;
use App\Models\Review;
use App\Models\SellerEarning;
use App\Models\Shipment;
use App\Models\User;
use App\Models\UserAddress;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ComprehensiveMarketplaceTest extends TestCase
{
    use RefreshDatabase;

    protected User $buyer1;
    protected User $buyer2;
    protected User $seller1;
    protected User $seller2;
    protected User $admin;
    protected Category $category;
    protected UserAddress $address1;
    protected UserAddress $address2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->buyer1 = User::factory()->create(['role' => 'user', 'is_seller' => false, 'phone' => '9000000001']);
        $this->buyer2 = User::factory()->create(['role' => 'user', 'is_seller' => false, 'phone' => '9000000002']);
        $this->seller1 = User::factory()->create(['role' => 'user', 'is_seller' => true, 'phone' => '9000000003']);
        $this->seller2 = User::factory()->create(['role' => 'user', 'is_seller' => true, 'phone' => '9000000004']);
        $this->admin = User::factory()->create(['role' => 'admin', 'is_seller' => false, 'phone' => '9000000005']);

        $this->category = Category::create([
            'name' => 'Electronics',
            'slug' => 'electronics',
            'is_active' => true,
        ]);

        $this->address1 = UserAddress::create([
            'user_id' => $this->buyer1->id,
            'label' => 'Home',
            'full_name' => 'Buyer One',
            'phone' => '9000000001',
            'address_line1' => '123 Main St',
            'city' => 'Metropolis',
            'state' => 'State',
            'pincode' => '110001',
            'is_default' => true,
        ]);

        $this->address2 = UserAddress::create([
            'user_id' => $this->buyer2->id,
            'label' => 'Work',
            'full_name' => 'Buyer Two',
            'phone' => '9000000002',
            'address_line1' => '456 Side St',
            'city' => 'Metropolis',
            'state' => 'State',
            'pincode' => '110002',
            'is_default' => true,
        ]);

        AppSetting::set('commission_percent', 10);
    }

    /* ─── 1. AUTHENTICATION & PROFILE ─── */

    public function test_user_otp_flow_and_profile_setup()
    {
        $response = $this->post('/send-otp', ['phone' => '9876543210']);
        $response->assertRedirect('/verify-otp');
        $this->assertDatabaseHas('users', ['phone' => '9876543210']);

        $user = User::where('phone', '9876543210')->first();
        $user->update(['otp' => '123456', 'otp_expires_at' => now()->addMinutes(5)]);

        $response = $this->withSession(['otp_phone' => '9876543210'])
            ->post('/verify-otp', ['otp' => '123456']);
        
        $response->assertRedirect('/setup-profile');
        $this->assertAuthenticatedAs($user);

        $response = $this->actingAs($user)->post('/setup-profile', [
            'name' => 'New User',
            'city' => 'City',
            'state' => 'State',
            'pincode' => '123456',
        ]);
        $response->assertRedirect('/home');
        $this->assertEquals('New User', $user->fresh()->name);
    }

    /* ─── 2. PRODUCT OWNERSHIP & MANAGEMENT ─── */

    public function test_seller_can_create_and_manage_own_product()
    {
        $product = Product::factory()->create([
            'user_id' => $this->seller1->id,
            'category_id' => $this->category->id,
            'name' => 'Seller 1 Gadget',
            'selling_price' => 500,
            'stock' => 5,
            'status' => 'active',
        ]);

        // Seller 1 edit page
        $response = $this->actingAs($this->seller1)->get("/seller/products/{$product->id}/edit");
        $response->assertStatus(200);

        // Seller 2 edit page forbidden
        $response = $this->actingAs($this->seller2)->get("/seller/products/{$product->id}/edit");
        $response->assertStatus(403);

        // Seller 2 update forbidden
        $response = $this->actingAs($this->seller2)->put("/seller/products/{$product->id}", [
            'name' => 'Hacked Name',
            'selling_price' => 1,
            'description' => 'Hacked',
        ]);
        $response->assertStatus(403);
        $this->assertEquals('Seller 1 Gadget', $product->fresh()->name);
    }

    /* ─── 3. CART ISOLATION & MANIPULATION ─── */

    public function test_cart_item_isolation_between_users()
    {
        $product = Product::factory()->create([
            'user_id' => $this->seller1->id,
            'category_id' => $this->category->id,
            'selling_price' => 100,
            'stock' => 10,
            'status' => 'active',
        ]);

        // Buyer 1 adds to cart
        $this->actingAs($this->buyer1)->post('/cart/add', [
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        $cart1 = Cart::where('user_id', $this->buyer1->id)->first();
        $cartItem1 = $cart1->items()->first();

        // Buyer 2 attempts to delete Buyer 1's cart item
        $response = $this->actingAs($this->buyer2)->delete("/cart/items/{$cartItem1->id}");
        $response->assertStatus(403);
        $this->assertDatabaseHas('cart_items', ['id' => $cartItem1->id]);

        // Buyer 2 attempts to update Buyer 1's cart item quantity
        $response = $this->actingAs($this->buyer2)->patch("/cart/items/{$cartItem1->id}", ['quantity' => 10]);
        $response->assertStatus(403);
    }

    /* ─── 4. CHECKOUT FLOW & MULTI-SELLER SPLITTING ─── */

    public function test_complete_checkout_flow_with_multi_seller_cart()
    {
        $p1 = Product::factory()->create([
            'user_id' => $this->seller1->id,
            'category_id' => $this->category->id,
            'name' => 'P1 Seller 1',
            'selling_price' => 200,
            'stock' => 5,
            'status' => 'active',
        ]);

        $p2 = Product::factory()->create([
            'user_id' => $this->seller2->id,
            'category_id' => $this->category->id,
            'name' => 'P2 Seller 2',
            'selling_price' => 300,
            'stock' => 5,
            'status' => 'active',
        ]);

        // Add both to buyer 1 cart
        $this->actingAs($this->buyer1)->post('/cart/add', ['product_id' => $p1->id, 'quantity' => 1]);
        $this->actingAs($this->buyer1)->post('/cart/add', ['product_id' => $p2->id, 'quantity' => 2]);

        // Place order
        $response = $this->actingAs($this->buyer1)->post('/checkout', [
            'address_id' => $this->address1->id,
            'delivery_option' => 'standard',
        ]);

        $order = Order::where('user_id', $this->buyer1->id)->first();
        $this->assertNotNull($order);
        $response->assertRedirect("/payment/{$order->id}");

        // Subtotal = 200*1 + 300*2 = 800. Delivery = 0 (subtotal > 500)
        $this->assertEquals(800.00, $order->subtotal);
        $this->assertEquals(800.00, $order->total);

        // Verify stock decremented
        $this->assertEquals(4, $p1->fresh()->stock);
        $this->assertEquals(3, $p2->fresh()->stock);

        // Verify 2 order items created
        $this->assertCount(2, $order->items);

        // Verify 2 seller earnings created with 10% commission
        $earning1 = SellerEarning::where('seller_id', $this->seller1->id)->first();
        $this->assertEquals(200.00, $earning1->order_amount);
        $this->assertEquals(20.00, $earning1->commission_amount);
        $this->assertEquals(180.00, $earning1->seller_amount);

        $earning2 = SellerEarning::where('seller_id', $this->seller2->id)->first();
        $this->assertEquals(600.00, $earning2->order_amount);
        $this->assertEquals(60.00, $earning2->commission_amount);
        $this->assertEquals(540.00, $earning2->seller_amount);

        // Verify shipments split per seller
        $this->assertDatabaseHas('shipments', ['order_id' => $order->id, 'seller_id' => $this->seller1->id]);
        $this->assertDatabaseHas('shipments', ['order_id' => $order->id, 'seller_id' => $this->seller2->id]);
    }

    /* ─── 5. STOCK CONCURRENCY SIMULATION ─── */

    public function test_stock_race_condition_prevents_overselling()
    {
        $product = Product::factory()->create([
            'user_id' => $this->seller1->id,
            'category_id' => $this->category->id,
            'name' => 'Limited Edition Item',
            'selling_price' => 1000,
            'stock' => 1,
            'status' => 'active',
        ]);

        // Setup Cart for Buyer 1
        $this->actingAs($this->buyer1)->post('/cart/add', ['product_id' => $product->id, 'quantity' => 1]);
        // Setup Cart for Buyer 2
        $this->actingAs($this->buyer2)->post('/cart/add', ['product_id' => $product->id, 'quantity' => 1]);

        // Buyer 1 places order successfully
        $res1 = $this->actingAs($this->buyer1)->post('/checkout', [
            'address_id' => $this->address1->id,
            'delivery_option' => 'standard',
        ]);

        $order1 = Order::where('user_id', $this->buyer1->id)->first();
        $this->assertNotNull($order1);
        $this->assertEquals(0, $product->fresh()->stock);

        // Buyer 2 attempts checkout immediately after
        $res2 = $this->actingAs($this->buyer2)->post('/checkout', [
            'address_id' => $this->address2->id,
            'delivery_option' => 'standard',
        ]);

        $res2->assertSessionHas('error');
        $order2 = Order::where('user_id', $this->buyer2->id)->first();
        $this->assertNull($order2);
        $this->assertEquals(0, $product->fresh()->stock);
    }

    /* ─── 6. PAYOUT SYSTEM & DOUBLE RELEASE PREVENTION ─── */

    public function test_payout_cannot_be_released_twice()
    {
        $product = Product::factory()->create(['user_id' => $this->seller1->id, 'category_id' => $this->category->id]);
        $order = Order::factory()->create(['user_id' => $this->buyer1->id, 'address_id' => $this->address1->id]);
        $orderItem = OrderItem::create([
            'order_id' => $order->id,
            'seller_id' => $this->seller1->id,
            'product_id' => $product->id,
            'product_name' => 'Test',
            'price' => 500,
            'quantity' => 1,
            'subtotal' => 500,
            'status' => 'delivered',
        ]);

        $earning = SellerEarning::create([
            'seller_id' => $this->seller1->id,
            'order_id' => $order->id,
            'order_item_id' => $orderItem->id,
            'order_amount' => 500,
            'commission_percent' => 10,
            'commission_amount' => 50,
            'seller_amount' => 450,
            'status' => 'on_hold',
            'hold_until' => now()->subHour(),
        ]);

        $this->artisan('payouts:release')->assertExitCode(0);

        $this->assertEquals('released', $earning->fresh()->status);
        $this->assertEquals(450.00, $this->seller1->fresh()->wallet_balance);
        $this->assertDatabaseHas('payouts', ['seller_id' => $this->seller1->id, 'amount' => 450.00]);

        // Run command a second time
        $this->artisan('payouts:release')->assertExitCode(0);

        // Balance should NOT increase again
        $this->assertEquals(450.00, $this->seller1->fresh()->wallet_balance);
        $this->assertEquals(1, Payout::where('seller_id', $this->seller1->id)->count());
    }

    /* ─── 7. RETURNS, REFUNDS & REVIEWS ELIGIBILITY ─── */

    public function test_buyer_return_request_and_admin_refund_workflow()
    {
        $product = Product::factory()->create(['user_id' => $this->seller1->id, 'category_id' => $this->category->id]);
        $order = Order::factory()->create([
            'user_id' => $this->buyer1->id,
            'address_id' => $this->address1->id,
            'status' => 'delivered',
            'payment_status' => 'paid',
            'total' => 500,
        ]);
        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'seller_id' => $this->seller1->id,
            'product_name' => $product->name,
            'price' => 500,
            'quantity' => 1,
            'subtotal' => 500,
            'status' => 'delivered',
        ]);
        Payment::create([
            'order_id' => $order->id,
            'user_id' => $this->buyer1->id,
            'amount' => 500,
            'method' => 'card',
            'transaction_id' => 'TXN123456',
            'status' => 'success',
        ]);

        // Buyer 2 cannot initiate return for Buyer 1's order
        $response = $this->actingAs($this->buyer2)->post('/returns', [
            'order_id' => $order->id,
            'reason' => 'Defective',
        ]);
        $response->assertStatus(404);

        // Buyer 1 submits return request
        $response = $this->actingAs($this->buyer1)->post('/returns', [
            'order_id' => $order->id,
            'reason' => 'Defective',
            'description' => 'Screen cracked',
        ]);
        $response->assertRedirect('/returns');
        $this->assertDatabaseHas('returns', ['order_id' => $order->id, 'status' => 'requested']);

        $returnReq = ReturnRequest::where('order_id', $order->id)->first();

        // Admin approves return
        $response = $this->actingAs($this->admin)->post("/admin/returns/{$returnReq->id}/process", [
            'action' => 'approve',
            'admin_note' => 'Approved for refund',
        ]);
        $response->assertSessionHas('success');
        $this->assertEquals('approved', $returnReq->fresh()->status);
        $this->assertEquals(2, $product->fresh()->stock); // Stock restored from 1 to 2!
        $this->assertDatabaseHas('refunds', ['order_id' => $order->id, 'status' => 'pending']);

        $refund = Refund::where('order_id', $order->id)->first();

        // Admin processes refund
        $response = $this->actingAs($this->admin)->post("/admin/refunds/{$refund->id}/process");
        $response->assertSessionHas('success');
        $this->assertEquals('processed', $refund->fresh()->status);
        $this->assertEquals('refunded', $order->fresh()->payment_status);
    }

    public function test_reviews_only_allowed_for_delivered_purchases()
    {
        $product = Product::factory()->create(['user_id' => $this->seller1->id, 'category_id' => $this->category->id]);
        $order = Order::factory()->create([
            'user_id' => $this->buyer1->id,
            'address_id' => $this->address1->id,
            'status' => 'pending', // Not delivered yet!
        ]);
        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'seller_id' => $this->seller1->id,
            'product_name' => $product->name,
            'price' => 100,
            'quantity' => 1,
            'subtotal' => 100,
        ]);

        // Review attempt should fail when order is pending
        $response = $this->actingAs($this->buyer1)->post('/reviews', [
            'product_id' => $product->id,
            'order_id' => $order->id,
            'rating' => 5,
            'comment' => 'Great!',
        ]);
        $response->assertStatus(404);

        // Update order status to delivered
        $order->update(['status' => 'delivered']);

        // Review attempt should now succeed
        $response = $this->actingAs($this->buyer1)->post('/reviews', [
            'product_id' => $product->id,
            'order_id' => $order->id,
            'rating' => 5,
            'comment' => 'Great!',
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('reviews', ['product_id' => $product->id, 'rating' => 5]);
        $this->assertEquals(5.00, $product->fresh()->rating);
    }

    /* ─── 8. ADMIN DASHBOARD AUTHORIZATION ─── */

    public function test_normal_user_and_seller_cannot_access_admin_panel()
    {
        $this->actingAs($this->buyer1)->get('/admin/dashboard')->assertStatus(403);
        $this->actingAs($this->seller1)->get('/admin/dashboard')->assertStatus(403);
        $this->actingAs($this->admin)->get('/admin/dashboard')->assertStatus(200);
    }
}
