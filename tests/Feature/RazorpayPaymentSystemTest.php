<?php

namespace Tests\Feature;

use App\Models\AppSetting;
use App\Models\Category;
use App\Models\Notification;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Refund;
use App\Models\SellerEarning;
use App\Models\User;
use App\Models\UserAddress;
use App\Models\WebhookEvent;
use App\Services\RazorpayService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RazorpayPaymentSystemTest extends TestCase
{
    use RefreshDatabase;

    protected User $buyer;
    protected User $seller;
    protected User $admin;
    protected Category $category;
    protected UserAddress $address;
    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->buyer = User::factory()->create(['role' => 'user', 'is_seller' => false, 'phone' => '9111111111']);
        $this->seller = User::factory()->create(['role' => 'user', 'is_seller' => true, 'phone' => '9222222222']);
        $this->admin = User::factory()->create(['role' => 'admin', 'is_seller' => false, 'phone' => '9333333333']);

        $this->category = Category::create(['name' => 'Tech', 'slug' => 'tech', 'is_active' => true]);

        $this->address = UserAddress::create([
            'user_id' => $this->buyer->id,
            'label' => 'Home',
            'full_name' => 'Buyer Test',
            'phone' => '9111111111',
            'address_line1' => 'Street 1',
            'city' => 'Metropolis',
            'state' => 'State',
            'pincode' => '110001',
            'is_default' => true,
        ]);

        $this->product = Product::factory()->create([
            'user_id' => $this->seller->id,
            'category_id' => $this->category->id,
            'name' => 'Gadget',
            'selling_price' => 1000,
            'stock' => 10,
            'status' => 'active',
        ]);

        AppSetting::set('commission_percent', 10);
    }

    /** Helper to create an order with pending payment */
    protected function createPendingOrderAndPayment(float $amount = 1000.00): array
    {
        $order = Order::factory()->create([
            'user_id' => $this->buyer->id,
            'address_id' => $this->address->id,
            'subtotal' => $amount,
            'total' => $amount,
            'status' => 'pending',
            'payment_status' => 'pending',
        ]);

        $orderItem = OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $this->product->id,
            'seller_id' => $this->seller->id,
            'product_name' => $this->product->name,
            'price' => $amount,
            'quantity' => 1,
            'subtotal' => $amount,
            'status' => 'pending',
        ]);

        SellerEarning::create([
            'seller_id' => $this->seller->id,
            'order_id' => $order->id,
            'order_item_id' => $orderItem->id,
            'order_amount' => $amount,
            'commission_percent' => 10,
            'commission_amount' => $amount * 0.10,
            'seller_amount' => $amount * 0.90,
            'status' => 'pending',
        ]);

        $rzpOrderId = 'order_test_' . uniqid();

        $payment = Payment::create([
            'order_id' => $order->id,
            'user_id' => $this->buyer->id,
            'gateway' => 'razorpay',
            'gateway_order_id' => $rzpOrderId,
            'amount' => $amount,
            'currency' => 'INR',
            'method' => 'razorpay',
            'status' => 'pending',
        ]);

        return [$order, $payment, $rzpOrderId];
    }

    /* ─── 1. PAYMENT VERIFICATION WITH VALID SIGNATURE ─── */

    public function test_valid_signature_verifies_payment_and_confirms_order()
    {
        [$order, $payment, $rzpOrderId] = $this->createPendingOrderAndPayment(1000.00);
        $rzpPaymentId = 'pay_test_' . uniqid();

        $secret = config('services.razorpay.key_secret');
        $validSignature = hash_hmac('sha256', $rzpOrderId . '|' . $rzpPaymentId, $secret);

        $response = $this->actingAs($this->buyer)->post("/payment/{$order->id}/verify", [
            'razorpay_order_id' => $rzpOrderId,
            'razorpay_payment_id' => $rzpPaymentId,
            'razorpay_signature' => $validSignature,
        ]);

        $response->assertRedirect("/payment/{$order->id}/success");
        $this->assertEquals('paid', $order->fresh()->payment_status);
        $this->assertEquals('confirmed', $order->fresh()->status);
        $this->assertEquals('success', $payment->fresh()->status);
        $this->assertEquals($rzpPaymentId, $payment->fresh()->gateway_payment_id);
    }

    /* ─── 2. INVALID SIGNATURE REJECTION ─── */

    public function test_invalid_signature_is_rejected_and_order_remains_unpaid()
    {
        [$order, $payment, $rzpOrderId] = $this->createPendingOrderAndPayment(1000.00);

        $response = $this->actingAs($this->buyer)->post("/payment/{$order->id}/verify", [
            'razorpay_order_id' => $rzpOrderId,
            'razorpay_payment_id' => 'pay_fake_123',
            'razorpay_signature' => 'invalid_signature_hash',
        ]);

        $response->assertSessionHas('error');
        $this->assertEquals('pending', $order->fresh()->payment_status);
        $this->assertEquals('pending', $payment->fresh()->status);
    }

    /* ─── 3. DUPLICATE PAYMENT VERIFICATION (IDEMPOTENCY) ─── */

    public function test_duplicate_payment_verification_is_idempotent()
    {
        [$order, $payment, $rzpOrderId] = $this->createPendingOrderAndPayment(1000.00);
        $rzpPaymentId = 'pay_test_' . uniqid();

        $secret = config('services.razorpay.key_secret');
        $validSignature = hash_hmac('sha256', $rzpOrderId . '|' . $rzpPaymentId, $secret);

        $params = [
            'razorpay_order_id' => $rzpOrderId,
            'razorpay_payment_id' => $rzpPaymentId,
            'razorpay_signature' => $validSignature,
        ];

        // First verification submission
        $this->actingAs($this->buyer)->post("/payment/{$order->id}/verify", $params);
        $notificationCount1 = Notification::where('user_id', $this->buyer->id)->count();

        // Second verification submission (replay)
        $response2 = $this->actingAs($this->buyer)->post("/payment/{$order->id}/verify", $params);
        $response2->assertRedirect("/payment/{$order->id}/success");

        $this->assertEquals(1, Payment::where('order_id', $order->id)->count());
        $this->assertEquals($notificationCount1, Notification::where('user_id', $this->buyer->id)->count());
    }

    /* ─── 4. AMOUNT TAMPERING / MISMATCH REJECTION ─── */

    public function test_amount_or_order_mismatch_is_rejected()
    {
        [$order, $payment, $rzpOrderId] = $this->createPendingOrderAndPayment(1000.00);

        $response = $this->actingAs($this->buyer)->post("/payment/{$order->id}/verify", [
            'razorpay_order_id' => 'order_tampered_999',
            'razorpay_payment_id' => 'pay_tampered_123',
            'razorpay_signature' => 'some_sig',
        ]);

        $response->assertSessionHas('error', 'Gateway order mismatch detected.');
        $this->assertEquals('pending', $order->fresh()->payment_status);
    }

    /* ─── 5. WEBHOOK SIGNATURE VERIFICATION & SUCCESS EVENT ─── */

    public function test_valid_webhook_event_processes_payment_success()
    {
        [$order, $payment, $rzpOrderId] = $this->createPendingOrderAndPayment(1000.00);
        $rzpPaymentId = 'pay_webhook_' . uniqid();

        $webhookSecret = config('services.razorpay.webhook_secret');
        $payload = [
            'event_id' => 'evt_' . uniqid(),
            'event' => 'order.paid',
            'payload' => [
                'payment' => [
                    'entity' => [
                        'id' => $rzpPaymentId,
                        'order_id' => $rzpOrderId,
                        'amount' => 100000,
                        'currency' => 'INR',
                        'status' => 'captured',
                    ]
                ]
            ]
        ];

        $rawBody = json_encode($payload);
        $signature = hash_hmac('sha256', $rawBody, $webhookSecret);

        $response = $this->call('POST', '/webhooks/razorpay', [], [], [], [
            'HTTP_X-Razorpay-Signature' => $signature,
            'CONTENT_TYPE' => 'application/json',
        ], $rawBody);

        $response->assertStatus(200);
        $response->assertJson(['status' => 'success']);

        $this->assertEquals('paid', $order->fresh()->payment_status);
        $this->assertEquals('confirmed', $order->fresh()->status);
        $this->assertEquals('success', $payment->fresh()->status);
    }

    /* ─── 6. IMMUTABLE WEBHOOK EVENT DEDUPLICATION ─── */

    public function test_immutable_webhook_event_deduplication()
    {
        [$order, $payment, $rzpOrderId] = $this->createPendingOrderAndPayment(1000.00);
        $eventId = 'evt_unique_' . uniqid();

        $webhookSecret = config('services.razorpay.webhook_secret');
        $payload = [
            'event_id' => $eventId,
            'event' => 'order.paid',
            'payload' => [
                'payment' => [
                    'entity' => [
                        'id' => 'pay_dedup_123',
                        'order_id' => $rzpOrderId,
                        'amount' => 100000,
                        'currency' => 'INR',
                        'status' => 'captured',
                    ]
                ]
            ]
        ];

        $rawBody = json_encode($payload);
        $signature = hash_hmac('sha256', $rawBody, $webhookSecret);

        // First attempt
        $res1 = $this->call('POST', '/webhooks/razorpay', [], [], [], ['HTTP_X-Razorpay-Signature' => $signature], $rawBody);
        $res1->assertStatus(200);
        $res1->assertJson(['status' => 'success']);
        $this->assertEquals(1, WebhookEvent::where('event_id', $eventId)->count());

        // Replay attempt with same event_id
        $res2 = $this->call('POST', '/webhooks/razorpay', [], [], [], ['HTTP_X-Razorpay-Signature' => $signature], $rawBody);
        $res2->assertStatus(200);
        $res2->assertJson(['status' => 'already_processed']);
        $this->assertEquals(1, WebhookEvent::where('event_id', $eventId)->count());
    }

    /* ─── 7. RACE CONDITION: VERIFY PAYMENT + WEBHOOK ─── */

    public function test_concurrent_payment_verification_and_webhook_race_condition()
    {
        [$order, $payment, $rzpOrderId] = $this->createPendingOrderAndPayment(1000.00);
        $rzpPaymentId = 'pay_race_' . uniqid();
        $secret = config('services.razorpay.key_secret');
        $validSignature = hash_hmac('sha256', $rzpOrderId . '|' . $rzpPaymentId, $secret);

        // Path 1: User verifies payment
        $this->actingAs($this->buyer)->post("/payment/{$order->id}/verify", [
            'razorpay_order_id' => $rzpOrderId,
            'razorpay_payment_id' => $rzpPaymentId,
            'razorpay_signature' => $validSignature,
        ]);

        $notificationCountAfterVerify = Notification::where('user_id', $this->buyer->id)->count();

        // Path 2: Webhook arrives simultaneously for the same order
        $webhookSecret = config('services.razorpay.webhook_secret');
        $payload = [
            'event_id' => 'evt_race_' . uniqid(),
            'event' => 'payment.captured',
            'payload' => [
                'payment' => [
                    'entity' => [
                        'id' => $rzpPaymentId,
                        'order_id' => $rzpOrderId,
                        'amount' => 100000,
                        'status' => 'captured',
                    ]
                ]
            ]
        ];
        $rawBody = json_encode($payload);
        $wbSig = hash_hmac('sha256', $rawBody, $webhookSecret);

        $res = $this->call('POST', '/webhooks/razorpay', [], [], [], ['HTTP_X-Razorpay-Signature' => $wbSig], $rawBody);
        $res->assertStatus(200);

        // Assert zero duplicate earnings, zero extra notifications, zero extra order items
        $this->assertEquals('paid', $order->fresh()->payment_status);
        $this->assertEquals(1, SellerEarning::where('order_id', $order->id)->count());
        $this->assertEquals($notificationCountAfterVerify, Notification::where('user_id', $this->buyer->id)->count());
    }

    /* ─── 8. PAYOUT CANCELLED UPON REFUND / RETURN ─── */

    public function test_payout_is_prevented_when_order_is_returned_or_refunded()
    {
        [$order, $payment, $rzpOrderId] = $this->createPendingOrderAndPayment(1000.00);
        $payment->update(['status' => 'success', 'gateway_payment_id' => 'pay_refund_earning_123']);
        $order->update(['payment_status' => 'paid', 'status' => 'delivered']);

        $refund = Refund::create([
            'order_id' => $order->id,
            'payment_id' => $payment->id,
            'amount' => 1000.00,
            'reason' => 'Defective item',
            'status' => 'pending',
        ]);

        // Process refund
        $this->actingAs($this->admin)->post("/admin/refunds/{$refund->id}/process");

        // Assert seller earning status was marked as 'failed'
        $earning = SellerEarning::where('order_id', $order->id)->first();
        $this->assertEquals('failed', $earning->status);

        // Run payout release artisan command
        $this->artisan('payouts:release')
            ->expectsOutput('Processing eligible payouts...')
            ->assertExitCode(0);

        // Seller wallet balance must remain 0
        $this->assertEquals(0, $this->seller->fresh()->wallet_balance);
    }
}
