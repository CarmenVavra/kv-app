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
        // Keine "users"-Tabelle: es gibt genau einen Admin-Account, dessen Zugangsdaten in der
        // .env liegen (siehe App\Auth\AdminUserProvider). "sessions" bleibt, weil SESSION_DRIVER=database.
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            // string, not foreignId(): the auth identifier is the admin's email (see AdminUser::getAuthIdentifier()),
            // not a numeric users.id - there is no users table.
            $table->string('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions');
    }
};
