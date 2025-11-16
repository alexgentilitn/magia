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
        Schema::create('email_templates', function (Blueprint $table) {
            $table->id();
            $table->string('codice', 50)->unique()->comment('Codice identificativo template');
            $table->string('nome', 100)->comment('Nome descrittivo template');
            $table->string('oggetto')->comment('Oggetto email');
            $table->text('corpo_html')->comment('Corpo email in HTML');
            $table->text('corpo_testo')->nullable()->comment('Versione testo semplice');
            $table->enum('tipo', ['benvenuto', 'conferma', 'reminder', 'marketing', 'sistema', 'custom'])->default('custom');
            $table->text('variabili_disponibili')->nullable()->comment('JSON array variabili usabili');
            $table->boolean('attivo')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_templates');
    }
};
