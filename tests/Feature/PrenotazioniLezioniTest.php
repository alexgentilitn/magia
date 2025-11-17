<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Utente;
use App\Models\Cliente;
use App\Models\Lezione;
use App\Models\Sede;
use App\Models\Professionista;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Test per sistema prenotazioni lezioni
 *
 * Verifica:
 * - Prenotazione lezione
 * - Verifica capienza massima
 * - Check-in / Check-out
 * - Gestione assenze
 * - Edge cases (lezione piena, ecc.)
 */
class PrenotazioniLezioniTest extends TestCase
{
    use RefreshDatabase;

    private Utente $admin;
    private Sede $sede;
    private Professionista $professionista;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = Utente::factory()->amministratore()->create();
        $this->sede = Sede::factory()->create();

        $utenteProfessionista = Utente::factory()->professionista()->create();
        $this->professionista = Professionista::factory()->create([
            'utente_id' => $utenteProfessionista->id,
        ]);
    }

    /** @test */
    public function admin_puo_creare_lezione()
    {
        $datiLezione = [
            'titolo' => 'Pilates Base',
            'descrizione' => 'Lezione di pilates per principianti',
            'tipo' => 'pilates',
            'data_ora' => now()->addDays(7)->format('Y-m-d H:i'),
            'durata_minuti' => 60,
            'capienza' => 15,
            'professionista_id' => $this->professionista->id,
            'sede_id' => $this->sede->id,
        ];

        $response = $this->actingAs($this->admin)
                         ->post('/admin/lezioni', $datiLezione);

        $response->assertStatus(302);

        $this->assertDatabaseHas('lezioni', [
            'titolo' => 'Pilates Base',
            'capienza' => 15,
            'posti_prenotati' => 0,
        ]);
    }

    /** @test */
    public function cliente_puo_prenotare_lezione_disponibile()
    {
        $cliente = Cliente::factory()->create();

        $lezione = Lezione::factory()->create([
            'sede_id' => $this->sede->id,
            'professionista_id' => $this->professionista->id,
            'capienza' => 15,
            'posti_prenotati' => 5,
        ]);

        $response = $this->actingAs($this->admin)
                         ->post("/admin/lezioni/{$lezione->id}/aggiungi-prenotazione", [
                             'cliente_id' => $cliente->id,
                         ]);

        $response->assertStatus(200);

        // Verifica che la prenotazione sia stata creata
        $this->assertDatabaseHas('cliente_lezione', [
            'cliente_id' => $cliente->id,
            'lezione_id' => $lezione->id,
        ]);

        // Verifica che i posti prenotati siano aumentati
        $lezione->refresh();
        $this->assertEquals(6, $lezione->posti_prenotati);
    }

    /** @test */
    public function non_puo_prenotare_lezione_piena()
    {
        $cliente = Cliente::factory()->create();

        // Lezione completamente piena
        $lezione = Lezione::factory()->completa()->create([
            'sede_id' => $this->sede->id,
            'professionista_id' => $this->professionista->id,
            'capienza' => 10,
            'posti_prenotati' => 10, // Pieno!
        ]);

        $response = $this->actingAs($this->admin)
                         ->post("/admin/lezioni/{$lezione->id}/aggiungi-prenotazione", [
                             'cliente_id' => $cliente->id,
                         ]);

        // Deve fallire (422 o 403 o redirect con errore)
        $this->assertTrue(
            $response->status() >= 400 || $response->isRedirect(),
            'Non dovrebbe poter prenotare lezione piena'
        );

        // Verifica che la prenotazione NON sia stata creata
        $this->assertDatabaseMissing('cliente_lezione', [
            'cliente_id' => $cliente->id,
            'lezione_id' => $lezione->id,
        ]);
    }

    /** @test */
    public function admin_puo_fare_check_in_cliente()
    {
        $cliente = Cliente::factory()->create();
        $lezione = Lezione::factory()->create([
            'sede_id' => $this->sede->id,
            'professionista_id' => $this->professionista->id,
        ]);

        // Prima prenota
        $this->actingAs($this->admin)
             ->post("/admin/lezioni/{$lezione->id}/aggiungi-prenotazione", [
                 'cliente_id' => $cliente->id,
             ]);

        // Poi fa check-in
        $response = $this->actingAs($this->admin)
                         ->post("/admin/lezioni/{$lezione->id}/check-in/{$cliente->id}");

        $response->assertStatus(200);

        // Verifica che lo stato sia aggiornato
        $this->assertDatabaseHas('cliente_lezione', [
            'cliente_id' => $cliente->id,
            'lezione_id' => $lezione->id,
            'presente' => true,
        ]);
    }

    /** @test */
    public function admin_puo_fare_check_out_cliente()
    {
        $cliente = Cliente::factory()->create();
        $lezione = Lezione::factory()->create([
            'sede_id' => $this->sede->id,
            'professionista_id' => $this->professionista->id,
        ]);

        // Prenota e fa check-in
        $this->actingAs($this->admin)
             ->post("/admin/lezioni/{$lezione->id}/aggiungi-prenotazione", [
                 'cliente_id' => $cliente->id,
             ]);

        $this->actingAs($this->admin)
             ->post("/admin/lezioni/{$lezione->id}/check-in/{$cliente->id}");

        // Fa check-out
        $response = $this->actingAs($this->admin)
                         ->post("/admin/lezioni/{$lezione->id}/check-out/{$cliente->id}");

        $response->assertStatus(200);

        // Verifica che completato sia true
        $this->assertDatabaseHas('cliente_lezione', [
            'cliente_id' => $cliente->id,
            'lezione_id' => $lezione->id,
            'completato' => true,
        ]);
    }

    /** @test */
    public function admin_puo_segnare_cliente_assente()
    {
        $cliente = Cliente::factory()->create();
        $lezione = Lezione::factory()->create([
            'sede_id' => $this->sede->id,
            'professionista_id' => $this->professionista->id,
        ]);

        // Prenota
        $this->actingAs($this->admin)
             ->post("/admin/lezioni/{$lezione->id}/aggiungi-prenotazione", [
                 'cliente_id' => $cliente->id,
             ]);

        // Segna assente
        $response = $this->actingAs($this->admin)
                         ->post("/admin/lezioni/{$lezione->id}/segna-assente/{$cliente->id}");

        $response->assertStatus(200);

        $this->assertDatabaseHas('cliente_lezione', [
            'cliente_id' => $cliente->id,
            'lezione_id' => $lezione->id,
            'assente' => true,
        ]);
    }

    /** @test */
    public function admin_puo_rimuovere_prenotazione()
    {
        $cliente = Cliente::factory()->create();
        $lezione = Lezione::factory()->create([
            'sede_id' => $this->sede->id,
            'professionista_id' => $this->professionista->id,
            'posti_prenotati' => 0,
        ]);

        // Prenota
        $this->actingAs($this->admin)
             ->post("/admin/lezioni/{$lezione->id}/aggiungi-prenotazione", [
                 'cliente_id' => $cliente->id,
             ]);

        $lezione->refresh();
        $postiPrimaDiRimuovere = $lezione->posti_prenotati;

        // Rimuovi prenotazione
        $response = $this->actingAs($this->admin)
                         ->delete("/admin/lezioni/{$lezione->id}/rimuovi-prenotazione/{$cliente->id}");

        $response->assertStatus(302);

        // Verifica che la prenotazione sia stata rimossa
        $this->assertDatabaseMissing('cliente_lezione', [
            'cliente_id' => $cliente->id,
            'lezione_id' => $lezione->id,
        ]);

        // Verifica che i posti siano diminuiti
        $lezione->refresh();
        $this->assertEquals($postiPrimaDiRimuovere - 1, $lezione->posti_prenotati);
    }

    /** @test */
    public function lezione_mostra_capienza_corretta()
    {
        $lezione = Lezione::factory()->create([
            'sede_id' => $this->sede->id,
            'professionista_id' => $this->professionista->id,
            'capienza' => 20,
            'posti_prenotati' => 0,
        ]);

        // Prenota 5 clienti
        for ($i = 0; $i < 5; $i++) {
            $cliente = Cliente::factory()->create();
            $this->actingAs($this->admin)
                 ->post("/admin/lezioni/{$lezione->id}/aggiungi-prenotazione", [
                     'cliente_id' => $cliente->id,
                 ]);
        }

        $lezione->refresh();
        $this->assertEquals(5, $lezione->posti_prenotati);
        $this->assertEquals(15, $lezione->capienza - $lezione->posti_prenotati); // Posti disponibili
    }

    /** @test */
    public function non_puo_prenotare_stesso_cliente_due_volte()
    {
        $cliente = Cliente::factory()->create();
        $lezione = Lezione::factory()->create([
            'sede_id' => $this->sede->id,
            'professionista_id' => $this->professionista->id,
        ]);

        // Prima prenotazione
        $this->actingAs($this->admin)
             ->post("/admin/lezioni/{$lezione->id}/aggiungi-prenotazione", [
                 'cliente_id' => $cliente->id,
             ]);

        // Seconda prenotazione (dovrebbe fallire)
        $response = $this->actingAs($this->admin)
                         ->post("/admin/lezioni/{$lezione->id}/aggiungi-prenotazione", [
                             'cliente_id' => $cliente->id,
                         ]);

        // Deve fallire o redirect con errore
        $this->assertTrue(
            $response->status() >= 400 || $response->isRedirect(),
            'Non dovrebbe poter prenotare lo stesso cliente due volte'
        );
    }
}
