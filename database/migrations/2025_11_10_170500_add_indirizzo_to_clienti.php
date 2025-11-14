<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Aggiunge colonne indirizzo alla tabella clienti
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clienti', function (Blueprint $table) {
            // Aggiungi colonne indirizzo se non esistono già
            if (!Schema::hasColumn('clienti', 'indirizzo_via')) {
                $table->string('indirizzo_via', 200)->nullable()->after('telefono_fisso');
            }
            
            if (!Schema::hasColumn('clienti', 'indirizzo_citta')) {
                $table->string('indirizzo_citta', 100)->nullable()->after('indirizzo_via');
            }
            
            if (!Schema::hasColumn('clienti', 'indirizzo_provincia')) {
                $table->string('indirizzo_provincia', 2)->nullable()->after('indirizzo_citta');
            }
            
            if (!Schema::hasColumn('clienti', 'indirizzo_cap')) {
                $table->string('indirizzo_cap', 5)->nullable()->after('indirizzo_provincia');
            }
        });
    }

    public function down(): void
    {
        Schema::table('clienti', function (Blueprint $table) {
            $table->dropColumn([
                'indirizzo_via',
                'indirizzo_citta',
                'indirizzo_provincia',
                'indirizzo_cap'
            ]);
        });
    }
};