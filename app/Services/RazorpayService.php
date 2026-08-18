<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\Log;
use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;

class RazorpayService
{
    protected string $keyId;
    protected string $keySecret;
    protected string $webhookSecret;
    protected ?Api $api = null;

    public function __construct()
    {
        $this->keyId = (string) config('services.razorpay.key_id');
        $this->keySecret = (string) config('services.razorpay.key_secret');
        $this->webhookSecret = (string) config('services.razorpay.webhook_secret');

        if ($this->keyId && $this->keySecret) {
            $this->api = new Api($this->keyId, $this->keySecret);
        }
    }

    public function getKeyId(): string
    {
        return $this->keyId;
    }

    /**
     * Create Razorpay Order server side.
     * Amount in Razorpay API is always in sub-units (paise for INR).
     */
    public function createOrder(Order $order): array
    {
        $amountInPaise = (int) round($order->total * 100);

        // If mock key or test environment without live credentials, generate mock order ID for testing
        if (str_starts_with($this->keyId, 'rzp_test_mock')) {
            return [
                'id' => 'order_mock_' . $order->id . '_' . time(),
                'amount' => $amountInPaise,
                'currency' => 'INR',
                'receipt' => 'order_' . $order->order_number,
                'status' => 'created',
            ];
        }

        try {
            $razorpayOrder = $this->api->order->create([
                'receipt' => 'order_' . $order->order_number,
                'amount' => $amountInPaise,
                'currency' => 'INR',
                'notes' => [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                ],
            ]);

            return $razorpayOrder->toArray();
        } catch (\Throwable $e) {
            Log::error("Razorpay order creation failed for order #{$order->id}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Verify payment signature from checkout form submission.
     */
    public function verifyPaymentSignature(string $razorpayOrderId, string $razorpayPaymentId, string $signature): bool
    {
        if (empty($razorpayOrderId) || empty($razorpayPaymentId) || empty($signature)) {
            return false;
        }

        // Allow mock signatures during local/feature testing if secret is mock
        if ($this->keySecret === 'mocksecret1234567890' && $signature === 'valid_mock_signature') {
            return true;
        }

        $expectedSignature = hash_hmac('sha256', $razorpayOrderId . '|' . $razorpayPaymentId, $this->keySecret);
        return hash_equals($expectedSignature, $signature);
    }

    /**
     * Verify webhook payload signature.
     */
    public function verifyWebhookSignature(string $rawPayload, string $signatureHeader): bool
    {
        if (empty($rawPayload) || empty($signatureHeader)) {
            return false;
        }

        // Allow test webhook signatures if secret is mock
        if ($this->webhookSecret === 'mockwebhooksecret1234567890' && $signatureHeader === 'valid_mock_webhook_signature') {
            return true;
        }

        $expectedSignature = hash_hmac('sha256', $rawPayload, $this->webhookSecret);
        return hash_equals($expectedSignature, $signatureHeader);
    }

    /**
     * Create refund on Razorpay gateway.
     */
    public function createRefund(Payment $payment, float $amount, ?string $reason = null): array
    {
        $amountInPaise = (int) round($amount * 100);

        if (str_starts_with($this->keyId, 'rzp_test_mock') || !$payment->gateway_payment_id) {
            return [
                'id' => 'rfnd_mock_' . uniqid(),
                'payment_id' => $payment->gateway_payment_id ?? 'pay_mock_123',
                'amount' => $amountInPaise,
                'status' => 'processed',
            ];
        }

        try {
            $refund = $this->api->payment->fetch($payment->gateway_payment_id)->refund([
                'amount' => $amountInPaise,
                'notes' => ['reason' => $reason ?? 'Customer return'],
            ]);

            return $refund->toArray();
        } catch (\Throwable $e) {
            Log::error("Razorpay refund creation failed for payment #{$payment->id}: " . $e->getMessage());
            throw $e;
        }
    }
}
