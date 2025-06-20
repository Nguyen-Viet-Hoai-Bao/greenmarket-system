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
            $table->string('description', 1000)->nullable()->default('Đang cập nhật');
            $table->string('product_info', 1000)->nullable()->default('Đang cập nhật');
            $table->string('note', 1000)->nullable()->default('Đang cập nhật');
            $table->string('origin', 1000)->nullable()->default('Đang cập nhật');
            $table->string('preservation', 1000)->nullable()->default('Đang cập nhật');
            $table->string('weight', 1000)->nullable()->default('Đang cập nhật');
            $table->string('usage_instructions', 1000)->nullable()->default('Đang cập nhật'); // Cột thêm mới
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
