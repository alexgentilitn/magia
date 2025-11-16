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
        Schema::create('referrals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_invitante_id')->constrained('clienti')->onDelete('cascade');
            $table->foreignId('cliente_invitato_id')->nullable()->constrained('clienti')->onDelete('set null');
            $table->string('email_invitato')->nullable();
            $table->string('codice_invito', 20)->unique();
            $table->enum('stato', ['pending', 'registrato', 'convertito', 'scaduto'])->default('pending');
            $table->timestamp('data_invito');
            $table->timestamp('data_registrazione')->nullable();
            $table->timestamp('data_conversione')->nullable();
            $table->decimal('sconto_invitante', 5, 2)->nullable()->comment('% sconto per chi invita');
            $table->decimal('sconto_invitato', 5, 2)->nullable()->comment('% sconto per chi è invitato');
            $table->boolean('sconto_applicato')->default(false);
            $table->text('note')->nullable();
            $table->timestamps();

            $table->index('codice_invito');
            $table->index(['cliente_invitante_id', 'stato']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('referrals');
    }
};
