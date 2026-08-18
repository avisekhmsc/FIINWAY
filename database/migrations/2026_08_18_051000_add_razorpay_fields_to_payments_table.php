<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->string('method')->nullable()->change();
            $table->string('gateway')->default('razorpay')->after('user_id');
            $table->string('gateway_order_id')->nullable()->unique()->after('gateway');
            $table->string('gateway_payment_id')->nullable()->unique()->after('gateway_order_id');
            $table->string('currency', 10)->default('INR')->after('amount');
            $table->string('signature')->nullable()->after('gateway_payment_id');
            $table->json('metadata')->nullable()->after('gateway_response');
            $table->timestamp('paid_at')->nullable()->after('status');
            $table->timestamp('refunded_at')->nullable()->after('paid_at');
        });

        Schema::table('refunds', function (Blueprint $table) {
            $table->string('gateway_refund_id')->nullable()->unique()->after('payment_id');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn([
                'gateway', 'gateway_order_id', 'gateway_payment_id',
                'currency', 'signature', 'metadata', 'paid_at', 'refunded_at'
            ]);
        });

        Schema::table('refunds', function (Blueprint $table) {
            $table->dropColumn('gateway_refund_id');
        });
    }
};
