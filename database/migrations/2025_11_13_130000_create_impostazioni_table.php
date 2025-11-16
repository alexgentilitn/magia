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
        // Crea tabella solo se non esiste
        if (!Schema::hasTable('impostazioni')) {
            Schema::create('impostazioni', function (Blueprint $table) {
                $table->id();
                $table->string('chiave', 100)->unique();
                $table->text('valore')->nullable();
                $table->string('gruppo', 50)->default('generale')->index(); // es: smtp, sistema, email, ecc
                $table->string('tipo', 20)->default('testo'); // testo, numero, booleano, password, email
                $table->text('descrizione')->nullable();
                $table->boolean('criptata')->default(false); // per password e dati sensibili
                $table->timestamps();
            });

            // Inserisci impostazioni SMTP di default solo se la tabella è stata appena creata
            DB::table('impostazioni')->insert([
            [
                'chiave' => 'smtp_host',
                'valore' => 'smtp.gmail.com',
                'gruppo' => 'smtp',
                'tipo' => 'testo',
                'descrizione' => 'Host del server SMTP',
                'criptata' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'chiave' => 'smtp_porta',
                'valore' => '587',
                'gruppo' => 'smtp',
                'tipo' => 'numero',
                'descrizione' => 'Porta del server SMTP',
                'criptata' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'chiave' => 'smtp_username',
                'valore' => '',
                'gruppo' => 'smtp',
                'tipo' => 'email',
                'descrizione' => 'Username/Email per autenticazione SMTP',
                'criptata' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'chiave' => 'smtp_password',
                'valore' => '',
                'gruppo' => 'smtp',
                'tipo' => 'password',
                'descrizione' => 'Password per autenticazione SMTP',
                'criptata' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'chiave' => 'smtp_encryption',
                'valore' => 'tls',
                'gruppo' => 'smtp',
                'tipo' => 'testo',
                'descrizione' => 'Tipo di encryption (tls, ssl, none)',
                'criptata' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'chiave' => 'mail_from_address',
                'valore' => 'noreply@magiadonna.it',
                'gruppo' => 'smtp',
                'tipo' => 'email',
                'descrizione' => 'Indirizzo email mittente',
                'criptata' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'chiave' => 'mail_from_name',
                'valore' => 'MA.GIA DONNA',
                'gruppo' => 'smtp',
                'tipo' => 'testo',
                'descrizione' => 'Nome mittente',
                'criptata' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('impostazioni');
    }
};
