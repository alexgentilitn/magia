<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * Migration: Aggiunge stato 'pending' per workflow approvazione professionisti
 */
return new class extends Migration
{
    public function up(): void
    {
        // Modifica l'enum stato per includere 'pending'
        DB::statement("ALTER TABLE professionisti MODIFY COLUMN stato ENUM('pending', 'attivo', 'sospeso', 'inattivo') DEFAULT 'pending'");
    }

    public function down(): void
    {
        // Ripristina enum originale
        DB::statement("ALTER TABLE professionisti MODIFY COLUMN stato ENUM('attivo', 'sospeso', 'inattivo') DEFAULT 'attivo'");
    }
};
