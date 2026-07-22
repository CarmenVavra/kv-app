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
        Schema::table('settings', function (Blueprint $table) {
            $table->decimal('kilometergeld_pro_km', 6, 2)->default(0.50)->after('working_days');
            $table->decimal('kurzparkschein_tarif_pro_halbe_stunde', 6, 2)->default(1.20)->after('kilometergeld_pro_km');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn(['kilometergeld_pro_km', 'kurzparkschein_tarif_pro_halbe_stunde']);
        });
    }
};
