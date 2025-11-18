<?php

namespace Database\Factories;

use App\Models\Utente;
use App\Models\Ruolo;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Factory per il modello Utente
 * Crea utenti di test per PHPUnit
 */
class UtenteFactory extends Factory
{
    protected $model = Utente::class;

    /**
     * Definisce lo stato di default dell'utente
     */
    public function definition(): array
    {
        return [
            'nome' => fake()->firstName(),
            'cognome' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'password' => Hash::make('password'), // Password di default per test
            'telefono' => fake()->phoneNumber(),
            'tipo_utente' => 'cliente',
            'ruolo_id' => null,
            'attivo' => true,
            'deve_cambiare_password' => false,
            'password_temp_expires_at' => null,
            'email_verified_at' => now(),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Stato: Utente amministratore
     */
    public function amministratore(): static
    {
        return $this->state(fn (array $attributes) => [
            'tipo_utente' => 'amministratore',
        ]);
    }

    /**
     * Stato: Utente professionista
     */
    public function professionista(): static
    {
        return $this->state(fn (array $attributes) => [
            'tipo_utente' => 'professionista',
        ]);
    }

    /**
     * Stato: Utente cliente
     */
    public function cliente(): static
    {
        return $this->state(fn (array $attributes) => [
            'tipo_utente' => 'cliente',
        ]);
    }

    /**
     * Stato: Utente non attivo
     */
    public function inattivo(): static
    {
        return $this->state(fn (array $attributes) => [
            'attivo' => false,
        ]);
    }
}
