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
        Schema::create('product_units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_news_id')->constrained()->onDelete('cascade');
            $table->float('weight')->nullable(); // null nếu quản lý theo quantity
            $table->integer('batch_qty')->default(1); // >1 nếu quản lý theo quantity
            $table->float('cost_price');
            $table->float('sale_price')->nullable();
            $table->float('discount_price')->nullable();
            $table->date('expiry_date')->nullable();
            $table->integer('sold')->default(0);
            $table->boolean('is_sold_out')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_units');
    }
};
