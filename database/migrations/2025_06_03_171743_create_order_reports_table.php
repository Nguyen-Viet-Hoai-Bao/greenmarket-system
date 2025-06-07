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
        Schema::create('order_reports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('client_id');
            $table->unsignedBigInteger('order_id');
            $table->text('content');
            $table->enum('issue_type', [
                            'delivery',       // Vấn đề giao hàng
                            'product_quality',// Chất lượng sản phẩm
                            'payment',        // Thanh toán
                            'customer_service', // Dịch vụ khách hàng
                            'other'           // Khác
                        ])->default('other');
            $table->enum('status', ['pending', 'resolved', 'rejected'])->default('pending');
            $table->timestamps();

            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_reports');
    }
};
