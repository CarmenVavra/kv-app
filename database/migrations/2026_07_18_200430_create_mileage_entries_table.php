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
        Schema::create('mileage_entries', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->decimal('km', 8, 1);
            $table->decimal('rate_per_km', 4, 2);
            $table->decimal('amount', 10, 2);
            $table->foreignId('expense_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mileage_entries');
    }
};
