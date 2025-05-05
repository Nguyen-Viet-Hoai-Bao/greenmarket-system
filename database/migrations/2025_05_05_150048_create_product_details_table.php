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
        Schema::create('product_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_template_id');
            $table->text('description')->nullable()->default('Đang cập nhật');
            $table->text('product_info')->nullable()->default('Đang cập nhật');
            $table->text('note')->nullable()->default('Đang cập nhật');
            $table->string('origin')->nullable()->default('Đang cập nhật');
            $table->text('preservation')->nullable()->default('Đang cập nhật');
            $table->string('weight')->nullable()->default('Đang cập nhật');
            $table->text('usage_instructions')->nullable()->default('Đang cập nhật'); // Cột thêm mới
            $table->timestamps();

            $table->foreign('product_template_id')
                ->references('id')->on('product_templates')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_details');
    }
};
