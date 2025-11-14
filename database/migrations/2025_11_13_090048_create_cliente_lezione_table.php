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
        Schema::create('cliente_lezione', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('utenti')->onDelete('cascade');
            $table->foreignId('lezione_id')->constrained('lezioni')->onDelete('cascade');

            // Stato prenotazione
            $table->enum('stato', ['prenotato', 'confermato', 'presente', 'assente', 'cancellato', 'lista_attesa'])->default('prenotato');
            $table->date('data_prenotazione')->nullable();

            // Check-in/out
            $table->timestamp('check_in')->nullable();
            $table->timestamp('check_out')->nullable();

            // Feedback
            $table->integer('valutazione')->nullable(); // 1-5
            $table->text('feedback')->nullable();

            $table->timestamps();

            // Indici
            $table->unique(['cliente_id', 'lezione_id']);
            $table->index('stato');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cliente_lezione');
    }
};
