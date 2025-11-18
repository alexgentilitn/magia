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
        Schema::create('pagamenti_professionisti', function (Blueprint $table) {
            $table->id();

            // Relazioni
            $table->unsignedBigInteger('professionista_id')->comment('FK a professionisti.id');
            $table->unsignedBigInteger('utente_id')->comment('FK a utenti.id (professionista)');
            $table->unsignedBigInteger('pagato_da')->nullable()->comment('FK a utenti.id (admin che ha registrato)');

            // Periodo di riferimento
            $table->date('periodo_da')->comment('Inizio periodo compenso');
            $table->date('periodo_a')->comment('Fine periodo compenso');

            // Importi
            $table->decimal('importo_maturato', 10, 2)->default(0)->comment('Compenso lordo maturato');
            $table->decimal('importo_pagato', 10, 2)->default(0)->comment('Importo effettivamente pagato');
            $table->decimal('ritenuta_fiscale', 10, 2)->default(0)->comment('Ritenuta d\'acconto 20%');
            $table->decimal('importo_netto', 10, 2)->default(0)->comment('Netto da pagare (dopo ritenuta)');

            // Dettagli pagamento
            $table->enum('metodo_pagamento', ['bonifico', 'contante', 'assegno'])->default('bonifico');
            $table->dateTime('data_pagamento')->nullable();
            $table->string('numero_bonifico', 100)->nullable();
            $table->string('iban', 34)->nullable();

            // Stato e documenti
            $table->enum('stato', ['completato', 'in_attesa', 'annullato'])->default('completato');
            $table->string('ricevuta_path')->nullable()->comment('Path ricevuta pagamento');
            $table->text('note')->nullable();

            $table->timestamps();

            // Indici per performance
            $table->index('utente_id');
            $table->index('professionista_id');
            $table->index('data_pagamento');
            $table->index('stato');
            $table->index(['periodo_da', 'periodo_a']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pagamenti_professionisti');
    }
};
