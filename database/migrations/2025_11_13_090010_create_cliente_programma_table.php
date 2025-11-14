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
        Schema::create('cliente_programma', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('utenti')->onDelete('cascade');
            $table->foreignId('programma_id')->constrained('programmi')->onDelete('cascade');

            // Dati iscrizione
            $table->date('data_iscrizione')->nullable();
            $table->date('data_inizio')->nullable();
            $table->date('data_fine')->nullable();
            $table->enum('stato', ['attivo', 'completato', 'cancellato', 'sospeso', 'in_attesa'])->default('attivo');

            // Dati pagamento
            $table->decimal('prezzo_pagato', 10, 2)->nullable();

            // Completamento
            $table->integer('completamento')->default(0); // percentuale 0-100

            $table->timestamps();

            // Indici
            $table->unique(['cliente_id', 'programma_id']);
            $table->index('stato');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cliente_programma');
    }
};
