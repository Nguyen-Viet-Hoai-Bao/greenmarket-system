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
        Schema::table('wards', function (Blueprint $table) {
            $table->foreignId('district_id')->after('id')->constrained()->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('wards', function (Blueprint $table) {
            $table->dropForeign(['district_id']);
            $table->dropColumn('district_id');
        });
    }

};
