<?php

namespace Database\Factories;

use App\Models\Cliente;
use App\Models\Utente;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory per il modello Cliente
 */
class ClienteFactory extends Factory
{
    protected $model = Cliente::class;

    public function definition(): array
    {
        $dataNascita = fake()->dateTimeBetween('-50 years', '-18 years');
        $eta = now()->year - $dataNascita->format('Y');

        return [
            'utente_id' => Utente::factory()->cliente(),
            'nome' => fake()->firstName('female'),
            'cognome' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'telefono' => fake()->phoneNumber(),
            'data_nascita' => $dataNascita,
            'codice_fiscale' => strtoupper(fake()->bothify('??????##?##?###?')),
            'indirizzo' => fake()->streetAddress(),
            'citta' => fake()->city(),
            'provincia' => fake()->stateAbbr(),
            'cap' => fake()->postcode(),

            // Dati corporei
            'peso' => fake()->randomFloat(1, 45, 120),
            'altezza' => fake()->numberBetween(150, 190),
            'circonferenza_vita' => fake()->numberBetween(60, 120),
            'circonferenza_fianchi' => fake()->numberBetween(70, 130),

            // Dati medici
            'certificato_medico' => fake()->boolean(70), // 70% ha certificato
            'scadenza_certificato_medico' => fake()->dateTimeBetween('now', '+2 years'),
            'patologie' => fake()->boolean(30) ? fake()->sentence() : null,
            'farmaci' => fake()->boolean(20) ? fake()->sentence() : null,
            'allergie' => fake()->boolean(15) ? fake()->sentence() : null,

            // Preferenze
            'preferenze_alimentari' => fake()->randomElement(['onnivoro', 'vegetariano', 'vegano', null]),
            'obiettivi' => fake()->randomElement(['dimagrimento', 'tonificazione', 'benessere', 'altro']),

            // Consensi
            'consenso_privacy' => true,
            'consenso_marketing' => fake()->boolean(60),
            'consenso_foto' => fake()->boolean(50),

            // Referral
            'codice_referral' => strtoupper(fake()->bothify('????####')),
            'invitato_da_cliente_id' => null,

            // Programma corrente
            'programma_corrente' => fake()->randomElement(['balla_snella', 'alimentazione', 'pelle_benessere', null]),
            'stato_programma' => fake()->randomElement(['attivo', 'sospeso', 'completato', null]),
            'data_inizio_programma' => fake()->dateTimeBetween('-6 months', 'now'),

            'attivo' => true,
            'note' => fake()->boolean(30) ? fake()->paragraph() : null,
        ];
    }

    /**
     * Cliente con certificato medico scaduto
     */
    public function certificatoScaduto(): static
    {
        return $this->state(fn (array $attributes) => [
            'certificato_medico' => true,
            'scadenza_certificato_medico' => fake()->dateTimeBetween('-6 months', '-1 day'),
        ]);
    }

    /**
     * Cliente con programma attivo
     */
    public function conProgrammaAttivo(): static
    {
        return $this->state(fn (array $attributes) => [
            'programma_corrente' => 'balla_snella',
            'stato_programma' => 'attivo',
            'data_inizio_programma' => fake()->dateTimeBetween('-3 months', 'now'),
        ]);
    }
}
