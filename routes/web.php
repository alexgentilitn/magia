<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\RegistrazioneController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ClientiController;

/**
 * File: Routes Web
 * Funzione: Definisce tutti gli URL dell'applicazione
 */

// ============================================
// HOMEPAGE
// ============================================

Route::get('/', function () {
    return view('homepage');
})->name('home');


// ============================================
// AREA ADMIN - LOGIN
// ============================================

Route::get('/admin/login', [AuthController::class, 'mostraLoginAdmin'])->name('admin.login');
Route::post('/admin/login', [AuthController::class, 'loginAdmin'])->name('admin.login.post');


// ============================================
// AREA CLIENTE - LOGIN
// ============================================

Route::get('/cliente/accedi', [AuthController::class, 'mostraLoginCliente'])->name('cliente.login');
Route::post('/cliente/accedi', [AuthController::class, 'loginCliente'])->name('cliente.login.post');


// ============================================
// REGISTRAZIONE CLIENTI
// ============================================

Route::get('/registrazione', [RegistrazioneController::class, 'mostraRegistrazione'])->name('registrazione');
Route::post('/registrazione', [RegistrazioneController::class, 'registraCliente'])->name('registrazione.store');

// Verifica AJAX
Route::post('/verifica-email', [RegistrazioneController::class, 'verificaEmail'])->name('verifica.email');
Route::post('/verifica-codice-fiscale', [RegistrazioneController::class, 'verificaCodiceFiscale'])->name('verifica.cf');
Route::post('/verifica-codice-invito', [RegistrazioneController::class, 'verificaCodiceInvito'])->name('verifica.codice');


// ============================================
// LOGOUT GLOBALE
// ============================================

Route::post('/logout', [AuthController::class, 'effettuaLogout'])->name('logout');


// ============================================
// AREA ADMIN (Protetta)
// ============================================

Route::middleware(['auth', 'tipo_utente:amministratore,professionista'])->prefix('admin')->name('admin.')->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Statistiche AJAX
    Route::get('/statistiche-rapide', [DashboardController::class, 'statisticheRapide'])->name('statistiche.rapide');
    
    // GESTIONE CLIENTI
    Route::prefix('clienti')->name('clienti.')->group(function () {
        // Lista
        Route::get('/', [ClientiController::class, 'index'])->name('index');
        
        // Crea
        Route::get('/crea', [ClientiController::class, 'create'])->name('create');
        Route::post('/', [ClientiController::class, 'store'])->name('store');
        
        // Visualizza
        Route::get('/{id}', [ClientiController::class, 'show'])->name('show');
        
        // Modifica
        Route::get('/{id}/modifica', [ClientiController::class, 'edit'])->name('edit');
        Route::put('/{id}', [ClientiController::class, 'update'])->name('update');
        
        // Elimina
        Route::delete('/{id}', [ClientiController::class, 'destroy'])->name('destroy');
    });
});


// ============================================
// AREA CLIENTE (Protetta)
// ============================================

Route::middleware(['auth', 'tipo_utente:cliente'])->prefix('cliente')->name('cliente.')->group(function () {
    
    // Dashboard Cliente (con i 5 bottoni)
    Route::get('/dashboard', function() {
        return view('cliente.dashboard');
    })->name('dashboard');
    
    // Sezione 1: BALLA & SNELLA
    Route::get('/balla-snella', function() {
        return view('cliente.balla-snella');
    })->name('balla-snella');
    
    // Sezione 2: ALIMENTAZIONE SMART
    Route::get('/alimentazione', function() {
        return view('cliente.alimentazione');
    })->name('alimentazione');
    
    // Sezione 3: PELLE & BENESSERE
    Route::get('/pelle-benessere', function() {
        return view('cliente.pelle-benessere');
    })->name('pelle-benessere');
    
    // Sezione 4: COMMUNITY MA.GIA
    Route::get('/community', function() {
        return view('cliente.community');
    })->name('community');
    
    // Sezione 5: COACHING & OPPORTUNITÀ
    Route::get('/coaching', function() {
        return view('cliente.coaching');
    })->name('coaching');
    
    // Altre pagine area cliente
    Route::get('/programmi', function() {
        return view('cliente.programmi');
    })->name('programmi');
    
    Route::get('/calendario', function() {
        return view('cliente.calendario');
    })->name('calendario');
    
    Route::get('/profilo', function() {
        return view('cliente.profilo');
    })->name('profilo');
});

