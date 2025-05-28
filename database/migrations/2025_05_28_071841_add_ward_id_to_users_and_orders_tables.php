<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('ward_id')->nullable()->after('address');
            $table->foreign('ward_id')->references('id')->on('wards')->onDelete('set null');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedBigInteger('ward_id')->nullable()->after('address');
            $table->foreign('ward_id')->references('id')->on('wards')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('ward_id');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('ward_id');
        });
    }
};
