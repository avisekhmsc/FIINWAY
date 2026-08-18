<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use App\Models\UserAddress;
use App\Services\OrderStatusService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MarketplaceFixesTest extends TestCase
{
    use RefreshDatabase;

    protected User    $buyer;
    protected User    $seller;
    protected Category $category;
    protected UserAddress $address;

    protected function setUp(): void
    {
        parent::setUp();

        $this->buyer  = User::factory()->create(['is_seller' => false]);
        $this->seller = User::factory()->create(['is_seller' => true]);

        $this->category = Category::create([
            'name'      => 'Electronics',
            'slug'      => 'electronics',
            'is_active' => true,
        ]);

        $this->address = UserAddress::create([
            'user_id'       => $this->buyer->id,
            'label'         => 'Home',
            'full_name'     => 'Test Buyer',
            'phone'         => '9999999999',
            'address_line1' => '123 Test St',
            'city'          => 'Mumbai',
            'state'         => 'Maharashtra',
            'pincode'       => '400001',
            'is_default'    => true,
        ]);
    }

    // ─── Fix 2: Sold Product Status ───────────────────────────────────────────

    public function test_product_becomes_sold_when_stock_reaches_zero()
    {
        $product = Product::factory()->create([
            'user_id'       => $this->seller->id,
            'category_id'   => $this->category->id,
            'stock'         => 1,
            'selling_price' => 100,
            'status'        => 'active',
        ]);

        $this->actingAs($this->buyer)->post(route('cart.add'), [
            'product_id' => $product->id,
            'quantity'   => 1,
        ]);

        $this->actingAs($this->buyer)->post(route('order.place'), [
            'address_id'      => $this->address->id,
            'delivery_option' => 'standard',
        ]);

        $product->refresh();
        $this->assertEquals('sold', $product->status);
        $this->assertEquals(0, $product->stock);
    }

    public function test_product_is_not_sold_when_stock_remains()
    {
        $product = Product::factory()->create([
            'user_id'       => $this->seller->id,
            'category_id'   => $this->category->id,
            'stock'         => 5,
            'selling_price' => 100,
            'status'        => 'active',
        ]);

        $this->actingAs($this->buyer)->post(route('cart.add'), [
            'product_id' => $product->id,
            'quantity'   => 1,
        ]);

        $this->actingAs($this->buyer)->post(route('order.place'), [
            'address_id'      => $this->address->id,
            'delivery_option' => 'standard',
        ]);

        $product->refresh();
        $this->assertEquals('active', $product->status);
        $this->assertEquals(4, $product->stock);
    }

    // ─── Fix 3: Shipping State Machine ───────────────────────────────────────

    public function test_state_machine_allows_valid_transitions()
    {
        $service = new OrderStatusService();

        $this->assertTrue($service->isValidTransition('pending', 'confirmed'));
        $this->assertTrue($service->isValidTransition('confirmed', 'packed'));
        $this->assertTrue($service->isValidTransition('packed', 'shipped'));
        $this->assertTrue($service->isValidTransition('shipped', 'out_for_delivery'));
        $this->assertTrue($service->isValidTransition('out_for_delivery', 'delivered'));
    }

    public function test_state_machine_rejects_all_backward_transitions()
    {
        $service = new OrderStatusService();

        $this->assertFalse($service->isValidTransition('delivered', 'packed'));
        $this->assertFalse($service->isValidTransition('delivered', 'shipped'));
        $this->assertFalse($service->isValidTransition('shipped', 'confirmed'));
        $this->assertFalse($service->isValidTransition('packed', 'confirmed'));
        $this->assertFalse($service->isValidTransition('out_for_delivery', 'packed'));
    }

    public function test_state_machine_assert_throws_exception_for_invalid_transition()
    {
        $service = new OrderStatusService();
        $item    = new OrderItem(['status' => OrderStatusService::STATE_DELIVERED]);

        $this->expectException(\Exception::class);
        $service->assertValidTransition($item, OrderStatusService::STATE_PACKED);
    }

    // ─── Fix 4: Nearby Filter ─────────────────────────────────────────────────

    public function test_nearby_filter_includes_same_prefix_pincodes()
    {
        // buyer pincode 400001 → prefix 400
        $this->buyer->update(['pincode' => '400001']);

        $local   = Product::factory()->create([
            'category_id' => $this->category->id,
            'user_id'     => $this->seller->id,
            'pincode'     => '400055',   // same 400 prefix
            'status'      => 'active',
        ]);
        $distant = Product::factory()->create([
            'category_id' => $this->category->id,
            'user_id'     => $this->seller->id,
            'pincode'     => '110010',   // different prefix
            'status'      => 'active',
        ]);

        $response = $this->actingAs($this->buyer)
            ->get(route('products', ['nearby' => '1']));

        $response->assertSee($local->name);
        $response->assertDontSee($distant->name);
    }

    public function test_nearby_filter_excludes_products_outside_locality()
    {
        $this->buyer->update(['pincode' => '560001']);

        $outOfLocality = Product::factory()->create([
            'category_id' => $this->category->id,
            'user_id'     => $this->seller->id,
            'pincode'     => '400001',
            'status'      => 'active',
        ]);

        $response = $this->actingAs($this->buyer)
            ->get(route('products', ['nearby' => '1']));

        $response->assertDontSee($outOfLocality->name);
    }

    // ─── Fix 5: Chat Authorization ───────────────────────────────────────────

    public function test_chat_accessible_by_buyer_and_seller()
    {
        $order = Order::factory()->create([
            'user_id'    => $this->buyer->id,
            'address_id' => $this->address->id,
        ]);

        // Buyer can access
        $this->actingAs($this->buyer)
            ->get(route('chat.show', ['order' => $order->id, 'seller' => $this->seller->id]))
            ->assertStatus(200);

        // Seller can access
        $this->actingAs($this->seller)
            ->get(route('chat.show', ['order' => $order->id, 'seller' => $this->seller->id]))
            ->assertStatus(200);
    }

    public function test_chat_forbidden_for_unrelated_users()
    {
        $randomUser = User::factory()->create();
        $order      = Order::factory()->create([
            'user_id'    => $this->buyer->id,
            'address_id' => $this->address->id,
        ]);

        $this->actingAs($randomUser)
            ->get(route('chat.show', ['order' => $order->id, 'seller' => $this->seller->id]))
            ->assertStatus(403);
    }
}
