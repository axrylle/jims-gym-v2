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
        Schema::table('membership_user', function (Blueprint $table) {
            // Drop the existing foreign key if necessary
            $table->dropForeign(['membership_id']);

            // Add the new foreign key with cascading delete
            $table->foreign('membership_id')
                  ->references('id')
                  ->on('membership')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('membership_user', function (Blueprint $table) {
            $table->dropForeign(['membership_id']);

            // Re-add the original foreign key (without cascade)
            $table->foreign('membership_id')
                  ->references('id')
                  ->on('membership');
        });
    }
};
