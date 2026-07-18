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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();

            // Hund
            $table->string('dog_name');
            $table->string('breed')->nullable();
            $table->string('age_text')->nullable();
            $table->string('behavior_topic');
            $table->text('dog_notes')->nullable();
            $table->string('color', 7);

            // Halter/in
            $table->string('owner_name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('address_line')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('city')->nullable();

            $table->string('status')->default('active');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
