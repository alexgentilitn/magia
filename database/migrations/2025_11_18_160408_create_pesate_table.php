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
        Schema::create('pesate', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cliente_id')->comment('ID del cliente');
            $table->string('sede', 100)->nullable()->comment('Sede dove è stata effettuata la pesata');
            $table->decimal('peso', 5, 2)->comment('Peso in kg');
            $table->decimal('bmi', 5, 2)->nullable()->comment('Body Mass Index');
            $table->decimal('peso_corporeo_senza_grassi', 5, 2)->nullable()->comment('Massa magra in kg');
            $table->decimal('muscolo_scheletrico', 5, 2)->nullable()->comment('Percentuale muscolo scheletrico');
            $table->decimal('grasso_corporeo', 5, 2)->nullable()->comment('Percentuale grasso corporeo');
            $table->decimal('grasso_sottocutaneo', 5, 2)->nullable()->comment('Percentuale grasso sottocutaneo');
            $table->integer('grasso_viscerale')->nullable()->comment('Livello grasso viscerale');
            $table->decimal('acqua_corporea', 5, 2)->nullable()->comment('Percentuale acqua corporea');
            $table->decimal('massa_muscolare', 5, 2)->nullable()->comment('Massa muscolare in kg');
            $table->decimal('massa_ossea', 5, 2)->nullable()->comment('Massa ossea in kg');
            $table->decimal('proteine', 5, 2)->nullable()->comment('Percentuale proteine');
            $table->integer('bmr')->nullable()->comment('Metabolismo basale (calorie)');
            $table->integer('eta_metabolica')->nullable()->comment('Età metabolica in anni');
            $table->date('data_rilevazione')->comment('Data della pesata');
            $table->text('note')->nullable()->comment('Note aggiuntive');
            $table->timestamps();

            // Indexes
            $table->index('cliente_id', 'idx_cliente_id');
            $table->index('data_rilevazione', 'idx_data_rilevazione');
            $table->index('sede', 'idx_sede');

            // Foreign key
            $table->foreign('cliente_id', 'fk_pesate_cliente')
                  ->references('id')
                  ->on('clienti')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pesate');
    }
};
