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
        // Verifica se la tabella prenotazioni esiste già
        if (!Schema::hasTable('prenotazioni')) {
            Schema::create('prenotazioni', function (Blueprint $table) {
                $table->id();
                $table->foreignId('cliente_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('lezione_id')->constrained('lezioni')->onDelete('cascade');

                $table->enum('stato', ['confermata', 'in_attesa', 'cancellata'])->default('confermata');
                $table->timestamp('data_prenotazione');
                $table->boolean('presenza_confermata')->default(false);
                $table->timestamp('presenza_confermata_at')->nullable();

                $table->text('note')->nullable();

                $table->timestamps();

                // Unique constraint: un cliente può prenotare una lezione solo una volta
                $table->unique(['cliente_id', 'lezione_id']);

                // Index
                $table->index(['stato', 'data_prenotazione']);
            });
        } else {
            // Se esiste, aggiungi solo i campi mancanti
            Schema::table('prenotazioni', function (Blueprint $table) {
                if (!Schema::hasColumn('prenotazioni', 'stato')) {
                    $table->enum('stato', ['confermata', 'in_attesa', 'cancellata'])->default('confermata');
                }
                if (!Schema::hasColumn('prenotazioni', 'data_prenotazione')) {
                    $table->timestamp('data_prenotazione')->nullable();
                }
                if (!Schema::hasColumn('prenotazioni', 'presenza_confermata')) {
                    $table->boolean('presenza_confermata')->default(false);
                }
                if (!Schema::hasColumn('prenotazioni', 'presenza_confermata_at')) {
                    $table->timestamp('presenza_confermata_at')->nullable();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('prenotazioni')) {
            Schema::table('prenotazioni', function (Blueprint $table) {
                if (Schema::hasColumn('prenotazioni', 'presenza_confermata_at')) {
                    $table->dropColumn('presenza_confermata_at');
                }
            });
        }
    }
};
