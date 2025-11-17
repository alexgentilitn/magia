<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Tabella utente_permesso
 *
 * Funzione: Crea la tabella pivot per i permessi individuali degli utenti
 * Permette di assegnare permessi specifici a singoli collaboratori
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('utente_permesso', function (Blueprint $table) {
            $table->id();
            $table->foreignId('utente_id')->constrained('utenti')->onDelete('cascade');
            $table->foreignId('permesso_id')->constrained('permessi')->onDelete('cascade');
            $table->timestamps();

            // Indice univoco per evitare duplicati
            $table->unique(['utente_id', 'permesso_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('utente_permesso');
    }
};
