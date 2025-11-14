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
        Schema::create('impostazioni_sistema', function (Blueprint $table) {
            $table->id();

            // Categoria dell'impostazione
            $table->string('categoria', 50)->comment('Es: tipologia_lezione, stato_lezione, frequenza_ricorrenza');

            // Chiave univoca all'interno della categoria
            $table->string('chiave', 50)->comment('Valore usato nel codice (es: individuale, gruppo)');

            // Label da mostrare all'utente
            $table->string('etichetta', 100)->comment('Testo da mostrare nelle select/UI');

            // Descrizione opzionale
            $table->text('descrizione')->nullable()->comment('Descrizione della voce');

            // Colore per badge/UI (hex)
            $table->string('colore', 7)->nullable()->comment('Colore hex es: #9c27b0');

            // Icona FontAwesome
            $table->string('icona', 50)->nullable()->comment('Classe icona FontAwesome');

            // Ordinamento
            $table->integer('ordinamento')->default(0)->comment('Ordine di visualizzazione');

            // Attivo/Disattivo
            $table->boolean('attivo')->default(true)->comment('Se disattivo non appare nelle select');

            // Sistema = non eliminabile dall'utente
            $table->boolean('di_sistema')->default(false)->comment('Voce di sistema non eliminabile');

            $table->timestamps();

            // Indici
            $table->index(['categoria', 'attivo']);
            $table->unique(['categoria', 'chiave']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('impostazioni_sistema');
    }
};
