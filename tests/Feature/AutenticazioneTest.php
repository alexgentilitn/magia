<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Utente;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

/**
 * Test per il sistema di autenticazione
 *
 * Verifica:
 * - Login admin funziona
 * - Login cliente funziona
 * - Logout funziona
 * - Redirect corretti per ruoli
 * - Protezione middleware
 */
class AutenticazioneTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function visualizza_pagina_login_admin()
    {
        $response = $this->get('/admin/login');

        $response->assertStatus(200);
        $response->assertSee('Login');
    }

    /** @test */
    public function admin_puo_fare_login_con_credenziali_corrette()
    {
        // Arrange: Crea un amministratore
        $admin = Utente::factory()->amministratore()->create([
            'email' => 'admin@test.com',
            'password' => Hash::make('password123'),
        ]);

        // Act: Prova a fare login
        $response = $this->post('/admin/login', [
            'email' => 'admin@test.com',
            'password' => 'password123',
        ]);

        // Assert: Verifica redirect a dashboard e autenticazione
        $response->assertRedirect('/admin/dashboard');
        $this->assertAuthenticatedAs($admin);
    }

    /** @test */
    public function admin_non_puo_fare_login_con_password_sbagliata()
    {
        $admin = Utente::factory()->amministratore()->create([
            'email' => 'admin@test.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->post('/admin/login', [
            'email' => 'admin@test.com',
            'password' => 'password_sbagliata',
        ]);

        $response->assertSessionHasErrors();
        $this->assertGuest();
    }

    /** @test */
    public function cliente_puo_fare_login()
    {
        $cliente = Utente::factory()->cliente()->create([
            'email' => 'cliente@test.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->post('/cliente/accedi', [
            'email' => 'cliente@test.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/cliente/dashboard');
        $this->assertAuthenticatedAs($cliente);
    }

    /** @test */
    public function utente_autenticato_puo_fare_logout()
    {
        $admin = Utente::factory()->amministratore()->create();

        $this->actingAs($admin)
             ->post('/logout')
             ->assertRedirect('/');

        $this->assertGuest();
    }

    /** @test */
    public function cliente_non_puo_accedere_area_admin()
    {
        $cliente = Utente::factory()->cliente()->create();

        $response = $this->actingAs($cliente)
                         ->get('/admin/dashboard');

        // Deve essere reindirizzato o ricevere 403
        $this->assertTrue(
            $response->isRedirect() || $response->status() === 403,
            'Cliente non dovrebbe accedere all\'area admin'
        );
    }

    /** @test */
    public function admin_puo_accedere_area_admin()
    {
        $admin = Utente::factory()->amministratore()->create();

        $response = $this->actingAs($admin)
                         ->get('/admin/dashboard');

        $response->assertStatus(200);
    }

    /** @test */
    public function professionista_puo_accedere_area_admin()
    {
        $professionista = Utente::factory()->professionista()->create();

        $response = $this->actingAs($professionista)
                         ->get('/admin/dashboard');

        $response->assertStatus(200);
    }

    /** @test */
    public function guest_non_puo_accedere_dashboard_admin()
    {
        $response = $this->get('/admin/dashboard');

        $response->assertRedirect('/admin/login');
    }

    /** @test */
    public function guest_non_puo_accedere_dashboard_cliente()
    {
        $response = $this->get('/cliente/dashboard');

        $response->assertRedirect('/cliente/accedi');
    }
}
