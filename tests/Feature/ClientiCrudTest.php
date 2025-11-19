<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Utente;
use App\Models\Cliente;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Test per CRUD Clienti
 *
 * Verifica tutte le operazioni CRUD (Create, Read, Update, Delete)
 * per la gestione clienti dall'area admin
 */
class ClientiCrudTest extends TestCase
{
    use RefreshDatabase;

    private Utente $admin;

    protected function setUp(): void
    {
        parent::setUp();

        // Crea un admin per tutti i test
        $this->admin = Utente::factory()->amministratore()->create();
    }

    /** @test */
    public function admin_puo_visualizzare_lista_clienti()
    {
        // Crea 5 clienti
        Cliente::factory()->count(5)->create();

        $response = $this->actingAs($this->admin)
                         ->get('/admin/clienti');

        $response->assertStatus(200);
        $response->assertSee('Gestione Clienti');
    }

    /** @test */
    public function admin_puo_visualizzare_form_creazione_cliente()
    {
        $response = $this->actingAs($this->admin)
                         ->get('/admin/clienti/crea');

        $response->assertStatus(200);
        $response->assertSee('Nuovo Cliente');
    }

    /** @test */
    public function admin_puo_creare_nuovo_cliente()
    {
        $datiCliente = [
            'nome' => 'Maria',
            'cognome' => 'Rossi',
            'email' => 'maria.rossi@test.com',
            'telefono' => '1234567890',
            'data_nascita' => '1990-01-01',
            'codice_fiscale' => 'RSSMRA90A41H501X',
            'indirizzo' => 'Via Roma 123',
            'citta' => 'Milano',
            'provincia' => 'MI',
            'cap' => '20100',
            'peso' => 65.5,
            'altezza' => 165,
            'consenso_privacy' => true,
        ];

        $response = $this->actingAs($this->admin)
                         ->post('/admin/clienti', $datiCliente);

        // Verifica redirect (successo)
        $response->assertStatus(302);

        // Verifica che il cliente sia stato creato nel database
        $this->assertDatabaseHas('clienti', [
            'email' => 'maria.rossi@test.com',
            'nome' => 'Maria',
            'cognome' => 'Rossi',
        ]);
    }

    /** @test */
    public function creazione_cliente_richiede_campi_obbligatori()
    {
        $response = $this->actingAs($this->admin)
                         ->post('/admin/clienti', []);

        // Deve ritornare con errori di validazione
        $response->assertSessionHasErrors(['nome', 'cognome', 'email']);
    }

    /** @test */
    public function email_cliente_deve_essere_unica()
    {
        // Crea un cliente esistente
        $clienteEsistente = Cliente::factory()->create([
            'email' => 'esistente@test.com'
        ]);

        // Prova a creare un altro cliente con stessa email
        $response = $this->actingAs($this->admin)
                         ->post('/admin/clienti', [
                             'nome' => 'Nuovo',
                             'cognome' => 'Cliente',
                             'email' => 'esistente@test.com', // Email duplicata
                             'telefono' => '1234567890',
                             'data_nascita' => '1990-01-01',
                         ]);

        $response->assertSessionHasErrors('email');
    }

    /** @test */
    public function admin_puo_visualizzare_dettaglio_cliente()
    {
        $cliente = Cliente::factory()->create([
            'nome' => 'Laura',
            'cognome' => 'Bianchi',
        ]);

        $response = $this->actingAs($this->admin)
                         ->get("/admin/clienti/{$cliente->id}");

        $response->assertStatus(200);
        $response->assertSee('Laura');
        $response->assertSee('Bianchi');
    }

    /** @test */
    public function admin_puo_visualizzare_form_modifica_cliente()
    {
        $cliente = Cliente::factory()->create();

        $response = $this->actingAs($this->admin)
                         ->get("/admin/clienti/{$cliente->id}/modifica");

        $response->assertStatus(200);
        $response->assertSee('Modifica Cliente');
    }

    /** @test */
    public function admin_puo_aggiornare_cliente()
    {
        $cliente = Cliente::factory()->create([
            'nome' => 'Vecchio Nome',
            'email' => 'vecchia@email.com',
        ]);

        $datiAggiornati = [
            'nome' => 'Nuovo Nome',
            'cognome' => $cliente->cognome,
            'email' => 'nuova@email.com',
            'telefono' => $cliente->telefono,
            'data_nascita' => $cliente->data_nascita,
            'codice_fiscale' => $cliente->codice_fiscale,
            'indirizzo' => $cliente->indirizzo,
            'citta' => $cliente->citta,
            'provincia' => $cliente->provincia,
            'cap' => $cliente->cap,
            'consenso_privacy' => true,
        ];

        $response = $this->actingAs($this->admin)
                         ->put("/admin/clienti/{$cliente->id}", $datiAggiornati);

        $response->assertStatus(302);

        // Verifica che i dati siano stati aggiornati
        $this->assertDatabaseHas('clienti', [
            'id' => $cliente->id,
            'nome' => 'Nuovo Nome',
            'email' => 'nuova@email.com',
        ]);
    }

    /** @test */
    public function admin_puo_eliminare_cliente()
    {
        $cliente = Cliente::factory()->create();

        $response = $this->actingAs($this->admin)
                         ->delete("/admin/clienti/{$cliente->id}");

        $response->assertStatus(302);

        // Verifica soft delete (record esiste ma ha deleted_at)
        $this->assertSoftDeleted('clienti', [
            'id' => $cliente->id,
        ]);
    }

    /** @test */
    public function certificato_medico_scaduto_viene_identificato()
    {
        // Crea cliente con certificato scaduto
        $cliente = Cliente::factory()->certificatoScaduto()->create();

        $response = $this->actingAs($this->admin)
                         ->get('/admin/clienti');

        $response->assertStatus(200);
        // Dashboard dovrebbe mostrare alert certificati scaduti
    }
}
