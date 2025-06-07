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
        Schema::create('admin_wallets', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['income', 'expense'])->comment('Loại giao dịch: thu hoặc chi');
            $table->decimal('amount', 15, 2);
            $table->string('description')->nullable();

            // Chỉ cần cập nhật ở bản ghi mới nhất (nếu cần)
            $table->decimal('total_income', 15, 2)->default(0);
            $table->decimal('total_expense', 15, 2)->default(0);
            $table->decimal('balance', 15, 2)->default(0);

            $table->timestamps();
        });
    }   

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_wallets');
    }
};
