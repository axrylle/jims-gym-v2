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
        Schema::create('membership', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description');
            $table->integer('duration');  // Fixed column type
            $table->float('price');
            $table->timestamps();
        });

        Schema::create('membership_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('membership_id')->constrained('membership');  // Ensured proper foreign key setup
            $table->string('last_name')->nullable();
            $table->string('first_name')->nullable();
            $table->string('middle_initial')->nullable();
            $table->string('contact_number')->nullable();
            $table->string('email')->nullable();
            $table->string('address')->nullable();
            $table->boolean('status')->default(true);
            $table->date('expiry')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('membership_user');
        Schema::dropIfExists('membership');
    }
};
