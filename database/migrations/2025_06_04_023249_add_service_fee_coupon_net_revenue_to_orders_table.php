<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->float('service_fee')->default(0)->after('total_amount');
            $table->string('coupon_code')->nullable()->after('service_fee');
            $table->float('net_revenue')->nullable()->after('coupon_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['service_fee', 'coupon_code', 'net_revenue']);
        });
    }
};
