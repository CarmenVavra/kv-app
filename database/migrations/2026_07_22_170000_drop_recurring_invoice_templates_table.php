<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Die "Wiederkehrend"-Funktion (RecurringInvoiceTemplate) wurde auf Kundenwunsch entfernt.
     * Die urspruengliche create-Migration bleibt aus Historien-Gruenden bestehen (append-only),
     * diese Migration raeumt die Tabelle danach wieder ab.
     */
    public function up(): void
    {
        Schema::dropIfExists('recurring_invoice_templates');
    }

    public function down(): void
    {
        Schema::create('recurring_invoice_templates', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->string('description');
            $table->decimal('amount', 10, 2);
            $table->string('frequency');
            $table->date('next_run_date');
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }
};
