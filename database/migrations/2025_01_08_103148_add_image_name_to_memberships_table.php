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
        Schema::table('membership', function (Blueprint $table) {
            $table->string('image_name')->nullable()->after('price');
        });
    }

    public function down()
    {
        Schema::table('membership', function (Blueprint $table) {
            $table->dropColumn('image_name');
        });
    }
};
