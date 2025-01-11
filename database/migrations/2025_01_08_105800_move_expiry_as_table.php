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
        // Schema::table('membership_user', function (Blueprint $table) {
        //     $table->dropColumn('expiry');
        // });
        // Schema::table('membership_user', function (Blueprint $table) {
        //     $table->dropColumn('membership_id');
        // });

        // Schema::create('member_membership', function (Blueprint $table) {
        //     $table->id();
        //     $table->unsignedBigInteger('member_id');
        //     $table->unsignedBigInteger('membership_id');
        //     $table->date('expiry')->nullable();
        //     $table->timestamps();
        
        //     $table->foreign('member_id')->references('id')->on('membership_user')->onDelete('cascade');
        //     $table->foreign('membership_id')->references('id')->on('membership')->onDelete('cascade');
        // });

        // Schema::create('member_membership', function (Blueprint $table) {
        //     $table->id();
        //     $table->foreignId('member_id')->constrained('membership_user')->onDelete('cascade');
        //     $table->foreignId('membership_id')->constrained('membership')->onDelete('cascade');
        //     $table->date('expiry')->nullable();
        //     $table->timestamps();
        // });
        
        Schema::create('member_membership', function (Blueprint $table) {
            $table->unsignedBigInteger('member_id');
            $table->unsignedBigInteger('membership_id');
            
            $table->foreign('member_id')->references('id')->on('membership_user')->onDelete('cascade');
            $table->foreign('membership_id')->references('id')->on('membership')->onDelete('cascade');
            
            $table->primary(['member_id', 'membership_id']); // Optional: to ensure uniqueness
        });

        Schema::create('member_expiry', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained('membership_user')->onDelete('cascade');
            $table->date('expiry')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('member_membership');
        // Schema::table('membership_user', function (Blueprint $table) {
        //     $table->date('expiry')->nullable();
        // });
    }
};
