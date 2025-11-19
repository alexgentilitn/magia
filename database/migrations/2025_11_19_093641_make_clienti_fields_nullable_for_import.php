<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Rende nullable i campi non essenziali per permettere import con dati minimi
 *
 * Funzione: Permette la creazione di clienti durante l'importazione pesate
 * fornendo solo nome, cognome e peso, senza richiedere email, telefono, ecc.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('clienti', function (Blueprint $table) {
            // Rendi nullable il codice fiscale (può essere assente)
            if (Schema::hasColumn('clienti', 'codice_fiscale')) {
                $table->string('codice_fiscale', 16)->nullable()->unique()->change();
            }

            // Rendi nullable email (può essere assente durante import)
            if (Schema::hasColumn('clienti', 'email')) {
                $table->string('email')->nullable()->change();
            }

            // Rendi nullable telefono mobile
            if (Schema::hasColumn('clienti', 'telefono_mobile')) {
                $table->string('telefono_mobile', 20)->nullable()->change();
            }

            // Rendi nullable data nascita
            if (Schema::hasColumn('clienti', 'data_nascita')) {
                $table->date('data_nascita')->nullable()->change();
            }

            // Rendi nullable campi indirizzo (vecchi nomi)
            if (Schema::hasColumn('clienti', 'indirizzo')) {
                $table->string('indirizzo', 200)->nullable()->change();
            }

            if (Schema::hasColumn('clienti', 'citta')) {
                $table->string('citta', 100)->nullable()->change();
            }

            if (Schema::hasColumn('clienti', 'provincia')) {
                $table->string('provincia', 2)->nullable()->change();
            }

            if (Schema::hasColumn('clienti', 'cap')) {
                $table->string('cap', 5)->nullable()->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clienti', function (Blueprint $table) {
            // Ripristina i campi come NOT NULL (attenzione: fallirà se ci sono valori NULL)
            if (Schema::hasColumn('clienti', 'codice_fiscale')) {
                $table->string('codice_fiscale', 16)->nullable(false)->unique()->change();
            }

            if (Schema::hasColumn('clienti', 'email')) {
                $table->string('email')->nullable(false)->change();
            }

            if (Schema::hasColumn('clienti', 'telefono_mobile')) {
                $table->string('telefono_mobile', 20)->nullable(false)->change();
            }

            if (Schema::hasColumn('clienti', 'data_nascita')) {
                $table->date('data_nascita')->nullable(false)->change();
            }

            if (Schema::hasColumn('clienti', 'indirizzo')) {
                $table->string('indirizzo', 200)->nullable(false)->change();
            }

            if (Schema::hasColumn('clienti', 'citta')) {
                $table->string('citta', 100)->nullable(false)->change();
            }

            if (Schema::hasColumn('clienti', 'provincia')) {
                $table->string('provincia', 2)->nullable(false)->change();
            }

            if (Schema::hasColumn('clienti', 'cap')) {
                $table->string('cap', 5)->nullable(false)->change();
            }
        });
    }
};
