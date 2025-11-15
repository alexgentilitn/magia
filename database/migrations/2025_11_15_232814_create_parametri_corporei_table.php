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
        Schema::create('parametri_corporei', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('users')->onDelete('cascade');
            $table->date('data_rilevazione');

            // Parametri base
            $table->decimal('peso', 5, 2)->nullable();
            $table->decimal('altezza', 5, 2)->nullable();

            // Circonferenze
            $table->decimal('circonferenza_vita', 5, 2)->nullable();
            $table->decimal('circonferenza_fianchi', 5, 2)->nullable();
            $table->decimal('circonferenza_braccia', 5, 2)->nullable();
            $table->decimal('circonferenza_cosce', 5, 2)->nullable();

            // Impedenziometria
            $table->decimal('massa_grassa', 5, 2)->nullable();
            $table->decimal('massa_magra', 5, 2)->nullable();
            $table->decimal('acqua_corporea', 5, 2)->nullable();
            $table->decimal('massa_ossea', 5, 2)->nullable();
            $table->decimal('metabolismo_basale', 6, 2)->nullable();

            // Note
            $table->text('note')->nullable();

            $table->timestamps();

            // Index per query veloci
            $table->index(['cliente_id', 'data_rilevazione']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parametri_corporei');
    }
};
