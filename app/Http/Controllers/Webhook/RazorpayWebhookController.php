<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Referral;
use App\Models\ReferralReward;
use App\Models\Refund;
use App\Models\WebhookEvent;
use App\Services\RazorpayService;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RazorpayWebhookController extends Controller
{
    protected RazorpayService $razorpayService;

    public function __construct(RazorpayService $razorpayService)
    {
        $this->razorpayService = $razorpayService;
    }

    public function handleWebhook(Request $request)
    {
        $signature = $request->header('X-Razorpay-Signature');
        $rawPayload = $request->getContent();

        if (!$signature || !$this->razorpayService->verifyWebhookSignature($rawPayload, $signature)) {
            Log::warning('Razorpay webhook signature verification failed.');
            return response()->json(['error' => 'Invalid webhook signature'], 400);
        }

        $eventData = json_decode($rawPayload, true);
        $event = $eventData['event'] ?? null;
        $eventId = $eventData['event_id'] ?? ($eventData['account_id'] ?? 'acc') . '_' . ($eventData['created_at'] ?? time()) . '_' . ($event ?? 'evt');

        Log::info("Razorpay Webhook Received Event: {$event} (ID: {$eventId})");

        // Database-level Event Deduplication (Immutable Webhook Audit Log)
        try {
            WebhookEvent::create([
                'gateway' => 'razorpay',
                'event_id' => $eventId,
                'event_type' => $event ?? 'unknown',
                'payload' => $eventData,
                'processed_at' => now(),
            ]);
        } catch (QueryException $e) {
            // Integrity constraint violation (duplicate event_id) -> already processed!
            Log::info("Razorpay Webhook Duplicate Event Ignored: {$eventId}");
            return response()->json(['status' => 'already_processed'], 200);
        }

        switch ($event) {
            case 'order.paid':
            case 'payment.authorized':
            case 'payment.captured':
                return $this->processPaymentSuccess($eventData);

            case 'payment.failed':
                return $this->processPaymentFailure($eventData);

            case 'refund.processed':
            case 'refund.created':
                return $this->processRefundEvent($eventData);

            default:
                return response()->json(['status' => 'ignored_event'], 200);
        }
    }

    protected function processPaymentSuccess(array $eventData)
    {
        $paymentEntity = $eventData['payload']['payment']['entity'] ?? [];
        $orderEntity   = $eventData['payload']['order']['entity'] ?? [];

        $razorpayOrderId   = $paymentEntity['order_id'] ?? ($orderEntity['id'] ?? null);
        $razorpayPaymentId = $paymentEntity['id'] ?? null;

        if (!$razorpayOrderId) {
            return response()->json(['error' => 'Order ID missing in payload'], 400);
        }

        DB::beginTransaction();
        try {
            // Lock payment record for update to guarantee idempotency
            $payment = Payment::where('gateway_order_id', $razorpayOrderId)
                ->lockForUpdate()
                ->first();

            if (!$payment && $razorpayPaymentId) {
                $payment = Payment::where('gateway_payment_id', $razorpayPaymentId)->lockForUpdate()->first();
            }

            if (!$payment) {
                DB::rollBack();
                Log::warning("Razorpay Webhook: No local payment record found for gateway order #{$razorpayOrderId}");
                return response()->json(['error' => 'Payment record not found'], 404);
            }

            $order = Order::where('id', $payment->order_id)->lockForUpdate()->first();

            // Idempotency check: if already processed as paid/success
            if ($payment->status === 'success' || ($order && $order->payment_status === 'paid')) {
                DB::rollBack();
                return response()->json(['status' => 'already_processed'], 200);
            }

            // Update payment record
            $payment->update([
                'gateway_payment_id' => $razorpayPaymentId ?? $payment->gateway_payment_id,
                'transaction_id'     => $razorpayPaymentId ?? $payment->transaction_id,
                'status'             => 'success',
                'paid_at'            => now(),
                'gateway_response'   => $paymentEntity,
            ]);

            if ($order) {
                $order->update([
                    'payment_status' => 'paid',
                    'payment_method' => 'razorpay',
                    'transaction_id' => $razorpayPaymentId ?? $payment->gateway_order_id,
                    'paid_at'        => now(),
                    'status'         => 'confirmed',
                ]);

                $order->items()->update(['status' => 'confirmed']);
                $order->shipments()->update(['status' => 'confirmed']);

                // Clear buyer cart items
                $buyer = $order->buyer;
                if ($buyer && $buyer->cart) {
                    $buyer->cart->items()->delete();
                }

                // Check referral reward
                if ($buyer) {
                    $referral = Referral::where('referred_id', $buyer->id)
                        ->where('eligible_action_done', false)
                        ->first();

                    if ($referral) {
                        $referral->update(['eligible_action_done' => true, 'eligible_at' => now()]);
                        $rewardAmount = (float) \App\Models\AppSetting::get('referral_reward', 50);
                        ReferralReward::create([
                            'user_id'     => $referral->referrer_id,
                            'referral_id' => $referral->id,
                            'amount'      => $rewardAmount,
                            'status'      => 'credited',
                            'credited_at' => now(),
                        ]);
                        $referral->referrer->increment('wallet_balance', $rewardAmount);
                    }
                }

                Notification::create([
                    'user_id' => $order->user_id,
                    'title'   => 'Payment Confirmed 🎉',
                    'body'    => "Your payment for order #{$order->order_number} was successfully processed.",
                    'type'    => 'order',
                ]);
            }

            DB::commit();
            return response()->json(['status' => 'success'], 200);

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("Razorpay Webhook Error: " . $e->getMessage());
            return response()->json(['error' => 'Webhook processing failed'], 500);
        }
    }

    protected function processPaymentFailure(array $eventData)
    {
        $paymentEntity   = $eventData['payload']['payment']['entity'] ?? [];
        $razorpayOrderId = $paymentEntity['order_id'] ?? null;

        if ($razorpayOrderId) {
            DB::transaction(function () use ($razorpayOrderId, $paymentEntity) {
                $payment = Payment::where('gateway_order_id', $razorpayOrderId)->lockForUpdate()->first();
                if ($payment && $payment->status === 'pending') {
                    $payment->update([
                        'status'           => 'failed',
                        'gateway_response' => $paymentEntity,
                    ]);

                    if ($payment->order && $payment->order->payment_status === 'pending') {
                        $payment->order->update(['payment_status' => 'failed']);
                    }
                }
            });
        }

        return response()->json(['status' => 'failure_logged'], 200);
    }

    protected function processRefundEvent(array $eventData)
    {
        $refundEntity    = $eventData['payload']['refund']['entity'] ?? [];
        $razorpayRefundId = $refundEntity['id'] ?? null;
        $razorpayPaymentId = $refundEntity['payment_id'] ?? null;

        if (!$razorpayRefundId && !$razorpayPaymentId) {
            return response()->json(['error' => 'Refund/Payment ID missing'], 400);
        }

        DB::transaction(function () use ($razorpayRefundId, $razorpayPaymentId, $refundEntity) {
            $refund = null;
            if ($razorpayRefundId) {
                $refund = Refund::where('gateway_refund_id', $razorpayRefundId)->lockForUpdate()->first();
            }

            if (!$refund && $razorpayPaymentId) {
                $payment = Payment::where('gateway_payment_id', $razorpayPaymentId)->first();
                if ($payment) {
                    $refund = Refund::where('payment_id', $payment->id)->lockForUpdate()->first();
                }
            }

            if ($refund && $refund->status !== 'processed') {
                $refund->update([
                    'status'            => 'processed',
                    'gateway_refund_id' => $razorpayRefundId ?? $refund->gateway_refund_id,
                    'processed_at'      => now(),
                ]);

                if ($refund->order) {
                    $refund->order->update(['payment_status' => 'refunded']);
                }

                if ($refund->payment) {
                    $refund->payment->update(['refunded_at' => now()]);
                }
            }
        });

        return response()->json(['status' => 'refund_processed'], 200);
    }
}
