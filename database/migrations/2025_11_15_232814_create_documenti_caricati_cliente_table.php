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
        // Crea prima tabella solo se non esiste
        if (!Schema::hasTable('documenti_caricati_cliente')) {
            Schema::create('documenti_caricati_cliente', function (Blueprint $table) {
                $table->id();
                $table->foreignId('cliente_id')->constrained('users')->onDelete('cascade');

                // Tipo documento
                $table->enum('tipo_documento', [
                    'certificato_medico',
                    'documento_identita',
                    'consenso_firmato',
                    'altro'
                ]);

                // File
                $table->string('file_path');
                $table->string('file_name');
                $table->string('mime_type')->nullable();
                $table->integer('file_size')->nullable(); // bytes

                // Metadata
                $table->text('note')->nullable();
                $table->boolean('verificato_admin')->default(false);
                $table->timestamp('data_verifica')->nullable();

                $table->timestamps();

                // Index
                $table->index(['cliente_id', 'tipo_documento']);
            });
        }

        // Crea seconda tabella solo se non esiste
        if (!Schema::hasTable('documenti_cliente')) {
            Schema::create('documenti_cliente', function (Blueprint $table) {
                $table->id();
                $table->foreignId('cliente_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('caricato_da')->nullable()->constrained('users')->onDelete('set null');

                $table->string('titolo');
                $table->text('descrizione')->nullable();
                $table->string('file_path');
                $table->string('file_name');

                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documenti_caricati_cliente');
        Schema::dropIfExists('documenti_cliente');
    }
};
