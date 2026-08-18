<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add admin_note to returns
        Schema::table('returns', function (Blueprint $table) {
            $table->text('admin_note')->nullable()->after('status');
        });

        // Add status to referrals
        Schema::table('referrals', function (Blueprint $table) {
            $table->string('status')->default('pending')->after('eligible_at');
        });
    }

    public function down(): void
    {
        Schema::table('returns', function (Blueprint $table) {
            $table->dropColumn('admin_note');
        });
        Schema::table('referrals', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
