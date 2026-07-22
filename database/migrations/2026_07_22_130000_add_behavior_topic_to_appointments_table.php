<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('appointments', function (Blueprint $table): void {
            // "Leistung" des Termins (BehaviorTopic-Enum, wie beim Klienten). Nullable, damit
            // bereits erfasste Termine ohne Leistung bestehen bleiben; im Formular ist das Feld
            // fuer neue/bearbeitete Termine Pflicht.
            $table->string('behavior_topic')->nullable()->after('type');
        });
    }

    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table): void {
            $table->dropColumn('behavior_topic');
        });
    }
};
