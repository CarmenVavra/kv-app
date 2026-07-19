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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('praxisname')->nullable();
            $table->string('avatar_path')->nullable();
            $table->boolean('notify_new_inquiries')->default(true);
            $table->boolean('notify_appointment_reminders')->default(true);
            $table->boolean('notify_weekly_summary')->default(false);
            $table->unsignedSmallInteger('default_appointment_duration')->default(60);
            // Kein DB-Default: MySQL erlaubt keinen Default-Wert auf JSON-Spalten. Der Fallback
            // ('Fr','Sa','So', siehe Mockup) wird in Setting::current() beim ersten Anlegen gesetzt.
            $table->json('working_days');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
