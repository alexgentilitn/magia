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
        Schema::create('pagamenti', function (Blueprint $table) {
            $table->id();

            // Relazioni
            $table->foreignId('cliente_id')->constrained('utenti')->onDelete('cascade');
            $table->foreignId('programma_id')->nullable()->constrained('programmi')->nullOnDelete();
            $table->foreignId('lezione_id')->nullable()->constrained('lezioni')->nullOnDelete();

            // Importi
            $table->decimal('importo', 10, 2);
            $table->decimal('importo_pagato', 10, 2)->default(0);
            $table->decimal('importo_residuo', 10, 2)->default(0);

            // Dati pagamento
            $table->enum('tipo', ['programma', 'lezione', 'abbonamento', 'altro'])->default('programma');
            $table->enum('metodo', ['contanti', 'bonifico', 'carta', 'pos', 'paypal', 'satispay', 'altro'])->default('contanti');
            $table->enum('stato', ['in_attesa', 'parziale', 'completato', 'scaduto', 'rimborsato', 'cancellato'])->default('in_attesa');

            // Date
            $table->date('data_emissione');
            $table->date('data_scadenza')->nullable();
            $table->date('data_pagamento')->nullable();

            // Riferimenti
            $table->string('numero_fattura')->nullable()->unique();
            $table->string('riferimento_transazione')->nullable();
            $table->text('note')->nullable();

            // Flags
            $table->boolean('fatturato')->default(false);
            $table->boolean('inviato_promemoria')->default(false);

            $table->timestamps();
            $table->softDeletes();

            // Indici
            $table->index('stato');
            $table->index('data_emissione');
            $table->index('data_scadenza');
            $table->index('tipo');
            $table->index('metodo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pagamenti');
    }
};
