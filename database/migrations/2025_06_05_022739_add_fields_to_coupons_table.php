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
        Schema::table('coupons', function (Blueprint $table) {
            $table->string('image_path')->nullable()->after('coupon_desc');
            $table->unsignedInteger('quantity')->default(0)->after('image_path');
            $table->unsignedBigInteger('max_discount_amount')->default(0)->after('discount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('coupons', function (Blueprint $table) {
            $table->dropColumn('image_path');
            $table->dropColumn('quantity');
            $table->dropColumn('max_discount_amount');
        });
    }
};
