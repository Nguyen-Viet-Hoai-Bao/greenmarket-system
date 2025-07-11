<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('product_news', function (Blueprint $table) {
            $table->decimal('cost_price', 15, 2)->nullable()->after('price');
        });
    }

    public function down()
    {
        Schema::table('product_news', function (Blueprint $table) {
            $table->dropColumn('cost_price');
        });
    }

};
