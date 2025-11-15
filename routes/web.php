<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\RegistrazioneController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ClientiController;
use App\Http\Controllers\Admin\LezioniController;
use App\Http\Controllers\Admin\ProgrammiController;
use App\Http\Controllers\Admin\PagamentiController;
use App\Http\Controllers\Admin\SedeController;
use App\Http\Controllers\Admin\ProfessionistiController;
use App\Http\Controllers\Admin\ProfiloController;
use App\Http\Controllers\Admin\ImpostazioniController;
use App\Http\Controllers\Admin\CalendarioController;

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

    // ============================================
    // PROFILO UTENTE
    // ============================================
    Route::prefix('profilo')->name('profilo.')->group(function () {
        Route::get('/', [ProfiloController::class, 'index'])->name('index');
        Route::post('/aggiorna', [ProfiloController::class, 'aggiornaProfilo'])->name('aggiorna');
        Route::get('/cambia-password', [ProfiloController::class, 'cambiaPassword'])->name('cambia-password');
        Route::post('/cambia-password', [ProfiloController::class, 'salvaNuovaPassword'])->name('salva-password');
    });

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

    // GESTIONE LEZIONI
    Route::prefix('lezioni')->name('lezioni.')->group(function () {
        // Lista
        Route::get('/', [LezioniController::class, 'index'])->name('index');

        // Crea
        Route::get('/crea', [LezioniController::class, 'create'])->name('create');
        Route::post('/', [LezioniController::class, 'store'])->name('store');

        // Modifica multipla (DEVE essere PRIMA delle route con {id})
        Route::get('/modifica-multipla', [LezioniController::class, 'editMultiple'])->name('edit-multiple');
        Route::post('/aggiorna-multiple', [LezioniController::class, 'updateMultiple'])->name('update-multiple');

        // Visualizza
        Route::get('/{id}', [LezioniController::class, 'show'])->name('show');

        // Modifica
        Route::get('/{id}/modifica', [LezioniController::class, 'edit'])->name('edit');
        Route::put('/{id}', [LezioniController::class, 'update'])->name('update');

        // Elimina
        Route::delete('/{id}', [LezioniController::class, 'destroy'])->name('destroy');

        // Cambia stato
        Route::patch('/{id}/cambia-stato', [LezioniController::class, 'cambiaStato'])->name('cambia-stato');

        // Gestione prenotazioni
        Route::get('/{id}/prenotazioni', [LezioniController::class, 'gestionePrenotazioni'])->name('prenotazioni');
        Route::post('/{lezioneId}/check-in/{clienteId}', [LezioniController::class, 'checkIn'])->name('check-in');
        Route::post('/{lezioneId}/check-out/{clienteId}', [LezioniController::class, 'checkOut'])->name('check-out');
        Route::post('/{lezioneId}/aggiungi-prenotazione', [LezioniController::class, 'aggiungiPrenotazione'])->name('aggiungi-prenotazione');
        Route::delete('/{lezioneId}/rimuovi-prenotazione/{clienteId}', [LezioniController::class, 'rimuoviPrenotazione'])->name('rimuovi-prenotazione');
        Route::post('/{lezioneId}/segna-assente/{clienteId}', [LezioniController::class, 'segnaAssente'])->name('segna-assente');
        Route::post('/{lezioneId}/annulla-assenza/{clienteId}', [LezioniController::class, 'annullaAssenza'])->name('annulla-assenza');
    });

    // GESTIONE PROGRAMMI
    Route::prefix('programmi')->name('programmi.')->group(function () {
        // Lista
        Route::get('/', [ProgrammiController::class, 'index'])->name('index');

        // Crea
        Route::get('/crea', [ProgrammiController::class, 'create'])->name('create');
        Route::post('/', [ProgrammiController::class, 'store'])->name('store');

        // Visualizza
        Route::get('/{id}', [ProgrammiController::class, 'show'])->name('show');

        // Modifica
        Route::get('/{id}/modifica', [ProgrammiController::class, 'edit'])->name('edit');
        Route::put('/{id}', [ProgrammiController::class, 'update'])->name('update');

        // Elimina
        Route::delete('/{id}', [ProgrammiController::class, 'destroy'])->name('destroy');

        // Cambia stato
        Route::patch('/{id}/cambia-stato', [ProgrammiController::class, 'cambiaStato'])->name('cambia-stato');

        // Duplica programma
        Route::post('/{id}/duplica', [ProgrammiController::class, 'duplica'])->name('duplica');
    });

    // ============================================
    // GESTIONE PAGAMENTI
    // ============================================
    Route::prefix('pagamenti')->name('pagamenti.')->group(function () {
        // Lista
        Route::get('/', [PagamentiController::class, 'index'])->name('index');

        // Crea
        Route::get('/crea', [PagamentiController::class, 'create'])->name('create');
        Route::post('/', [PagamentiController::class, 'store'])->name('store');

        // Visualizza
        Route::get('/{id}', [PagamentiController::class, 'show'])->name('show');

        // Modifica
        Route::get('/{id}/modifica', [PagamentiController::class, 'edit'])->name('edit');
        Route::put('/{id}', [PagamentiController::class, 'update'])->name('update');

        // Elimina
        Route::delete('/{id}', [PagamentiController::class, 'destroy'])->name('destroy');

        // Azioni speciali
        Route::post('/{id}/pagamento-parziale', [PagamentiController::class, 'registraPagamentoParziale'])->name('pagamento-parziale');
        Route::post('/{id}/marca-completato', [PagamentiController::class, 'marcaCompletato'])->name('marca-completato');
        Route::post('/{id}/rimborsa', [PagamentiController::class, 'rimborsa'])->name('rimborsa');
    });

    // ============================================
    // GESTIONE SEDI
    // ============================================
    Route::prefix('sedi')->name('sedi.')->group(function () {
        // Lista
        Route::get('/', [SedeController::class, 'index'])->name('index');

        // Crea
        Route::get('/crea', [SedeController::class, 'create'])->name('create');
        Route::post('/', [SedeController::class, 'store'])->name('store');

        // Visualizza
        Route::get('/{id}', [SedeController::class, 'show'])->name('show');

        // Modifica
        Route::get('/{id}/modifica', [SedeController::class, 'edit'])->name('edit');
        Route::put('/{id}', [SedeController::class, 'update'])->name('update');

        // Elimina
        Route::delete('/{id}', [SedeController::class, 'destroy'])->name('destroy');

        // Azioni speciali
        Route::patch('/{id}/toggle-attiva', [SedeController::class, 'toggleAttiva'])->name('toggle-attiva');
        Route::patch('/{id}/set-principale', [SedeController::class, 'setPrincipale'])->name('set-principale');

        // Gestione orari
        Route::get('/{id}/orari', [SedeController::class, 'orari'])->name('orari');
        Route::post('/{id}/orari', [SedeController::class, 'salvaOrari'])->name('salva-orari');
    });

    // ============================================
    // GESTIONE PROFESSIONISTI
    // ============================================
    Route::prefix('professionisti')->name('professionisti.')->group(function () {
        // Lista
        Route::get('/', [ProfessionistiController::class, 'index'])->name('index');

        // Crea
        Route::get('/crea', [ProfessionistiController::class, 'create'])->name('create');
        Route::post('/', [ProfessionistiController::class, 'store'])->name('store');

        // Visualizza
        Route::get('/{id}', [ProfessionistiController::class, 'show'])->name('show');

        // Modifica
        Route::get('/{id}/modifica', [ProfessionistiController::class, 'edit'])->name('edit');
        Route::put('/{id}', [ProfessionistiController::class, 'update'])->name('update');

        // Elimina
        Route::delete('/{id}', [ProfessionistiController::class, 'destroy'])->name('destroy');

        // Azioni speciali
        Route::patch('/{id}/cambia-stato', [ProfessionistiController::class, 'cambiaStato'])->name('cambia-stato');

        // Gestione certificazioni
        Route::get('/{id}/certificazioni', [ProfessionistiController::class, 'certificazioni'])->name('certificazioni');
        Route::post('/{id}/certificazioni', [ProfessionistiController::class, 'salvaCertificazioni'])->name('salva-certificazioni');

        // Gestione disponibilità
        Route::get('/{id}/disponibilita', [ProfessionistiController::class, 'disponibilita'])->name('disponibilita');
        Route::post('/{id}/disponibilita', [ProfessionistiController::class, 'salvaDisponibilita'])->name('salva-disponibilita');

        // Reset password
        Route::post('/{id}/reset-password', [ProfessionistiController::class, 'resetPassword'])->name('reset-password');
    });

    // ============================================
    // CALENDARIO VISUALE
    // ============================================
    Route::prefix('calendario')->name('calendario.')->group(function () {
        // Vista calendario
        Route::get('/', [CalendarioController::class, 'index'])->name('index');

        // API per eventi calendario
        Route::get('/events', [CalendarioController::class, 'getEvents'])->name('events');

        // Dettagli lezione
        Route::get('/{id}', [CalendarioController::class, 'show'])->name('show');

        // Sposta lezione (drag & drop)
        Route::post('/{id}/move', [CalendarioController::class, 'move'])->name('move');

        // Modifica durata lezione (resize)
        Route::post('/{id}/resize', [CalendarioController::class, 'resize'])->name('resize');

        // Elimina lezione
        Route::delete('/{id}', [CalendarioController::class, 'destroy'])->name('destroy');

        // Prenotazioni
        Route::post('/{id}/prenota', [CalendarioController::class, 'prenota'])->name('prenota');
        Route::delete('/{lezione}/prenotazioni/{cliente}', [CalendarioController::class, 'annullaPrenotazione'])->name('annulla-prenotazione');
        Route::post('/{id}/invia-reminder', [CalendarioController::class, 'inviaReminder'])->name('invia-reminder');

        // Presenze (Check-in/Check-out)
        Route::post('/{lezione}/check-in/{cliente}', [CalendarioController::class, 'checkIn'])->name('check-in');
        Route::post('/{lezione}/check-out/{cliente}', [CalendarioController::class, 'checkOut'])->name('check-out');
        Route::post('/{lezione}/segna-assente/{cliente}', [CalendarioController::class, 'segnaAssente'])->name('segna-assente');
        Route::post('/{lezione}/annulla-assenza/{cliente}', [CalendarioController::class, 'annullaAssenza'])->name('annulla-assenza');
    });

    // ============================================
    // REPORT E STATISTICHE
    // ============================================
    Route::prefix('report')->name('report.')->group(function () {
        // Dashboard report principale
        Route::get('/', [\App\Http\Controllers\Admin\ReportController::class, 'index'])->name('index');

        // Debug report (senza JavaScript)
        Route::get('/debug', [\App\Http\Controllers\Admin\ReportController::class, 'debug'])->name('debug');

        // Report presenze dettagliato
        Route::get('/presenze', [\App\Http\Controllers\Admin\ReportController::class, 'presenze'])->name('presenze');

        // Report performance professionisti
        Route::get('/professionisti', [\App\Http\Controllers\Admin\ReportController::class, 'professionisti'])->name('professionisti');

        // Export CSV
        Route::get('/export-csv', [\App\Http\Controllers\Admin\ReportController::class, 'exportCsv'])->name('export-csv');
    });

    // ============================================
    // MANUTENZIONE DATABASE (solo amministratori)
    // ============================================
    Route::middleware('tipo_utente:amministratore')->prefix('maintenance')->name('maintenance.')->group(function () {
        // Pagina principale manutenzione
        Route::get('/', [\App\Http\Controllers\Admin\MaintenanceController::class, 'index'])->name('index');

        // Fix visibilità calendario
        Route::post('/fix-visibilita', [\App\Http\Controllers\Admin\MaintenanceController::class, 'fixVisibilitaCalendario'])->name('fix-visibilita');

        // Verifica integrità
        Route::get('/verifica', [\App\Http\Controllers\Admin\MaintenanceController::class, 'verificaIntegrita'])->name('verifica');
    });

    // ============================================
    // IMPOSTAZIONI SISTEMA (solo amministratori)
    // ============================================
    Route::middleware('tipo_utente:amministratore')->prefix('impostazioni-sistema')->name('impostazioni-sistema.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\ImpostazioniSistemaController::class, 'index'])->name('index');
        Route::get('/crea', [\App\Http\Controllers\Admin\ImpostazioniSistemaController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\ImpostazioniSistemaController::class, 'store'])->name('store');
        Route::get('/{id}/modifica', [\App\Http\Controllers\Admin\ImpostazioniSistemaController::class, 'edit'])->name('edit');
        Route::put('/{id}', [\App\Http\Controllers\Admin\ImpostazioniSistemaController::class, 'update'])->name('update');
        Route::delete('/{id}', [\App\Http\Controllers\Admin\ImpostazioniSistemaController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/toggle', [\App\Http\Controllers\Admin\ImpostazioniSistemaController::class, 'toggleAttivo'])->name('toggle');
    });

    // ============================================
    // IMPOSTAZIONI (solo amministratori)
    // ============================================
    Route::middleware('tipo_utente:amministratore')->prefix('impostazioni')->name('impostazioni.')->group(function () {
        // Configurazione SMTP
        Route::get('/smtp', [ImpostazioniController::class, 'smtp'])->name('smtp');
        Route::post('/smtp', [ImpostazioniController::class, 'salvaSmtp'])->name('smtp.salva');
        Route::post('/smtp/test', [ImpostazioniController::class, 'testSmtp'])->name('smtp.test');
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

