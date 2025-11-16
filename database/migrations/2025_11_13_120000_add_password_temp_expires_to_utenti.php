<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('utenti', function (Blueprint $table) {
            // Aggiungi solo se non esistono già
            if (!Schema::hasColumn('utenti', 'password_temp_expires_at')) {
                $table->timestamp('password_temp_expires_at')->nullable()->after('password');
            }
            if (!Schema::hasColumn('utenti', 'deve_cambiare_password')) {
                $table->boolean('deve_cambiare_password')->default(false)->after('password_temp_expires_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('utenti', function (Blueprint $table) {
            $table->dropColumn(['password_temp_expires_at', 'deve_cambiare_password']);
        });
    }
};
