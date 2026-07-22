<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('appointments', function (Blueprint $table): void {
            // Nur relevant, wenn status = Abgesagt: true = Stornogebühr faellt an (kurzfristige
            // Absage < 24 h vor Trainingsbeginn), false = keine Gebuehr. Ausserhalb einer Absage
            // bleibt es null.
            $table->boolean('cancellation_fee')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table): void {
            $table->dropColumn('cancellation_fee');
        });
    }
};
