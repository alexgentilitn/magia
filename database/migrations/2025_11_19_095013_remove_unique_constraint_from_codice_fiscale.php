<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Rimuove vincolo UNIQUE da codice_fiscale
 *
 * Problema: Il constraint UNIQUE impedisce l'inserimento di multipli valori NULL
 * Soluzione: Rimuoviamo l'indice UNIQUE e rendiamo il campo semplicemente nullable
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('clienti', function (Blueprint $table) {
            // Rimuovi indice unique da codice_fiscale
            $table->dropUnique(['codice_fiscale']);

            // Ricrea il campo senza unique
            $table->string('codice_fiscale', 16)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clienti', function (Blueprint $table) {
            // Ripristina unique constraint (fallirà se ci sono duplicati/null)
            $table->string('codice_fiscale', 16)->nullable()->unique()->change();
        });
    }
};
