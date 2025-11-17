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
use App\Http\Controllers\Admin\RicetteController;
use App\Http\Controllers\Admin\ReferralController;
use App\Http\Controllers\Admin\AnalyticsController;
use App\Http\Controllers\Admin\TemplateEmailController;
use App\Http\Controllers\Admin\SuperAdminController;

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
// LANDING PAGE - GIORNATA DI PROVA
// ============================================

Route::get('/giornata-di-prova', [\App\Http\Controllers\GiornataProvaController::class, 'index'])->name('giornata-prova.index');
Route::post('/giornata-di-prova', [\App\Http\Controllers\GiornataProvaController::class, 'richiedi'])->name('giornata-prova.richiedi');


// ============================================
// LE NOSTRE SEDI (mappa interattiva)
// ============================================

Route::get('/sedi', function() {
    return view('sedi.index');
})->name('sedi.index');


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
// AREA ADMIN (Protetta - Solo Amministratori)
// ============================================

Route::middleware(['auth', 'tipo_utente:amministratore'])->prefix('admin')->name('admin.')->group(function () {
    
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

        // GESTIONE DOCUMENTI CLIENTE
        Route::prefix('{cliente}/documenti')->name('documenti.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\DocumentiController::class, 'index'])->name('index');
            Route::get('/carica', [\App\Http\Controllers\Admin\DocumentiController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Admin\DocumentiController::class, 'store'])->name('store');
        });
    });

    // GESTIONE DOCUMENTI (azioni singolo documento)
    Route::prefix('documenti')->name('documenti.')->group(function () {
        Route::get('/{id}/visualizza', [\App\Http\Controllers\Admin\DocumentiController::class, 'show'])->name('show');
        Route::get('/{id}/download', [\App\Http\Controllers\Admin\DocumentiController::class, 'download'])->name('download');
        Route::get('/{id}/modifica', [\App\Http\Controllers\Admin\DocumentiController::class, 'edit'])->name('edit');
        Route::put('/{id}', [\App\Http\Controllers\Admin\DocumentiController::class, 'update'])->name('update');
        Route::delete('/{id}', [\App\Http\Controllers\Admin\DocumentiController::class, 'destroy'])->name('destroy');

        // Alert scadenze
        Route::get('/scadenza', [\App\Http\Controllers\Admin\DocumentiController::class, 'scadenza'])->name('scadenza');
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
        Route::post('/{id}/approva', [ProfessionistiController::class, 'approva'])->name('approva');
        Route::post('/{id}/rifiuta', [ProfessionistiController::class, 'rifiuta'])->name('rifiuta');

        // Gestione certificazioni
        Route::get('/{id}/certificazioni', [ProfessionistiController::class, 'certificazioni'])->name('certificazioni');
        Route::post('/{id}/certificazioni', [ProfessionistiController::class, 'salvaCertificazioni'])->name('salva-certificazioni');

        // Gestione disponibilità
        Route::get('/{id}/disponibilita', [ProfessionistiController::class, 'disponibilita'])->name('disponibilita');
        Route::post('/{id}/disponibilita', [ProfessionistiController::class, 'salvaDisponibilita'])->name('salva-disponibilita');

        // Reset password
        Route::post('/{id}/reset-password', [ProfessionistiController::class, 'resetPassword'])->name('reset-password');

        // Gestione foto profilo
        Route::post('/{id}/upload-foto', [ProfessionistiController::class, 'uploadFoto'])->name('upload-foto');
        Route::delete('/{id}/elimina-foto', [ProfessionistiController::class, 'eliminaFoto'])->name('elimina-foto');

        // Gestione Documenti
        Route::prefix('/{id}/documenti')->name('documenti.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\ProfessionistaDocumentiController::class, 'index'])->name('index');
            Route::post('/upload', [\App\Http\Controllers\Admin\ProfessionistaDocumentiController::class, 'upload'])->name('upload');
            Route::get('/{documento_id}/download', [\App\Http\Controllers\Admin\ProfessionistaDocumentiController::class, 'download'])->name('download');
            Route::delete('/{documento_id}', [\App\Http\Controllers\Admin\ProfessionistaDocumentiController::class, 'destroy'])->name('destroy');
            Route::post('/{documento_id}/verifica', [\App\Http\Controllers\Admin\ProfessionistaDocumentiController::class, 'verifica'])->name('verifica');
            Route::post('/{documento_id}/revoca-verifica', [\App\Http\Controllers\Admin\ProfessionistaDocumentiController::class, 'revocaVerifica'])->name('revoca-verifica');
            Route::put('/{documento_id}', [\App\Http\Controllers\Admin\ProfessionistaDocumentiController::class, 'update'])->name('update');
        });

        // Gestione Galleria Foto
        Route::prefix('/{id}/galleria')->name('galleria.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\ProfessionistaGalleriaController::class, 'index'])->name('index');
            Route::post('/upload', [\App\Http\Controllers\Admin\ProfessionistaGalleriaController::class, 'upload'])->name('upload');
            Route::delete('/{index}', [\App\Http\Controllers\Admin\ProfessionistaGalleriaController::class, 'destroy'])->name('destroy');
            Route::post('/{index}/imposta-copertina', [\App\Http\Controllers\Admin\ProfessionistaGalleriaController::class, 'impostaCopertina'])->name('imposta-copertina');
            Route::get('/download-all', [\App\Http\Controllers\Admin\ProfessionistaGalleriaController::class, 'downloadAll'])->name('download-all');
        });
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
    // GESTIONE RICETTE
    // ============================================
    Route::prefix('ricette')->name('ricette.')->group(function () {
        // Lista
        Route::get('/', [RicetteController::class, 'index'])->name('index');

        // Crea
        Route::get('/crea', [RicetteController::class, 'create'])->name('create');
        Route::post('/', [RicetteController::class, 'store'])->name('store');

        // Visualizza
        Route::get('/{id}', [RicetteController::class, 'show'])->name('show');

        // Modifica
        Route::get('/{id}/modifica', [RicetteController::class, 'edit'])->name('edit');
        Route::put('/{id}', [RicetteController::class, 'update'])->name('update');

        // Elimina
        Route::delete('/{id}', [RicetteController::class, 'destroy'])->name('destroy');

        // Pubblica/Schedula
        Route::post('/{id}/pubblica', [RicetteController::class, 'pubblica'])->name('pubblica');
        Route::post('/{id}/nascondi', [RicetteController::class, 'nascondi'])->name('nascondi');

        // Upload immagine
        Route::post('/upload-immagine', [RicetteController::class, 'uploadImmagine'])->name('upload-immagine');
    });

    // ============================================
    // GESTIONE TEMPLATE EMAIL
    // ============================================
    Route::prefix('template-email')->name('template-email.')->group(function () {
        // Lista
        Route::get('/', [TemplateEmailController::class, 'index'])->name('index');

        // Crea
        Route::get('/crea', [TemplateEmailController::class, 'create'])->name('create');
        Route::post('/', [TemplateEmailController::class, 'store'])->name('store');

        // Visualizza
        Route::get('/{id}', [TemplateEmailController::class, 'show'])->name('show');

        // Modifica
        Route::get('/{id}/modifica', [TemplateEmailController::class, 'edit'])->name('edit');
        Route::put('/{id}', [TemplateEmailController::class, 'update'])->name('update');

        // Elimina
        Route::delete('/{id}', [TemplateEmailController::class, 'destroy'])->name('destroy');

        // Preview
        Route::post('/{id}/preview', [TemplateEmailController::class, 'preview'])->name('preview');

        // Invia email di test
        Route::post('/{id}/test', [TemplateEmailController::class, 'inviaTest'])->name('test');

        // Toggle attivo/inattivo
        Route::patch('/{id}/toggle-attivo', [TemplateEmailController::class, 'toggleAttivo'])->name('toggle-attivo');
    });

    // ============================================
    // PROGRAMMA REFERRAL "PORTA UN AMICO"
    // ============================================
    Route::prefix('referral')->name('referral.')->group(function () {
        // Dashboard principale
        Route::get('/', [ReferralController::class, 'index'])->name('index');

        // Dettagli referral
        Route::get('/{id}', [ReferralController::class, 'show'])->name('show');

        // Cambia stato referral
        Route::post('/{id}/cambia-stato', [ReferralController::class, 'cambiaStato'])->name('cambia-stato');

        // Applica sconto
        Route::post('/{id}/applica-sconto', [ReferralController::class, 'applicaSconto'])->name('applica-sconto');

        // Configurazione programma
        Route::get('/config/impostazioni', [ReferralController::class, 'configurazione'])->name('configurazione');
        Route::post('/config/impostazioni', [ReferralController::class, 'salvaConfigurazione'])->name('salva-configurazione');

        // Report
        Route::get('/report/statistiche', [ReferralController::class, 'report'])->name('report');
        Route::get('/report/export-csv', [ReferralController::class, 'exportCsv'])->name('export-csv');

        // Crea referral manualmente
        Route::get('/crea', [ReferralController::class, 'create'])->name('create');
        Route::post('/', [ReferralController::class, 'store'])->name('store');
    });

    // ============================================
    // ANALYTICS COMPORTAMENTALI
    // ============================================
    Route::prefix('analytics')->name('analytics.')->group(function () {
        // Dashboard analytics
        Route::get('/', [AnalyticsController::class, 'index'])->name('index');

        // Eventi dettagliati
        Route::get('/eventi', [AnalyticsController::class, 'eventi'])->name('eventi');

        // Analytics per singolo utente
        Route::get('/utente/{utente_id}', [AnalyticsController::class, 'utente'])->name('utente');

        // Export CSV
        Route::get('/export', [AnalyticsController::class, 'export'])->name('export');

        // API per grafici (AJAX)
        Route::get('/api/grafico-visite', [AnalyticsController::class, 'graficoVisite'])->name('api.grafico-visite');
        Route::get('/api/grafico-dispositivi', [AnalyticsController::class, 'graficoDispositivi'])->name('api.grafico-dispositivi');
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

        // Export CSV (legacy)
        Route::get('/export-csv', [\App\Http\Controllers\Admin\ReportController::class, 'exportCsv'])->name('export-csv');

        // Export Excel
        Route::get('/export-excel-presenze', [\App\Http\Controllers\Admin\ReportController::class, 'exportExcelPresenze'])->name('export-excel-presenze');
        Route::get('/export-excel-calendario', [\App\Http\Controllers\Admin\ReportController::class, 'exportExcelCalendario'])->name('export-excel-calendario');

        // Export PDF
        Route::get('/export-pdf-calendario', [\App\Http\Controllers\Admin\ReportController::class, 'exportPdfCalendario'])->name('export-pdf-calendario');
        Route::get('/export-pdf-professionisti', [\App\Http\Controllers\Admin\ReportController::class, 'exportPdfProfessionisti'])->name('export-pdf-professionisti');
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

    // ============================================
    // SUPER ADMIN (Manutenzione Sistema)
    // ============================================
    Route::middleware(['tipo_utente:amministratore', 'super_admin'])->prefix('super-admin')->name('super-admin.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\SuperAdminController::class, 'index'])->name('index');
        Route::post('/', [\App\Http\Controllers\Admin\SuperAdminController::class, 'index'])->name('login'); // POST per login
        Route::post('/toggle-debug', [\App\Http\Controllers\Admin\SuperAdminController::class, 'toggleDebug'])->name('toggle-debug');
        Route::post('/clear-all-cache', [\App\Http\Controllers\Admin\SuperAdminController::class, 'clearAllCache'])->name('clear-all-cache');
        Route::post('/clear-config-cache', [\App\Http\Controllers\Admin\SuperAdminController::class, 'clearConfigCache'])->name('clear-config-cache');
        Route::post('/clear-logs', [\App\Http\Controllers\Admin\SuperAdminController::class, 'clearLogs'])->name('clear-logs');
        Route::get('/view-logs', [\App\Http\Controllers\Admin\SuperAdminController::class, 'viewLogs'])->name('view-logs');
        Route::get('/database-info', [\App\Http\Controllers\Admin\SuperAdminController::class, 'databaseInfo'])->name('database-info');
        Route::post('/optimize-database', [\App\Http\Controllers\Admin\SuperAdminController::class, 'optimizeDatabase'])->name('optimize-database');
        Route::post('/run-migrations', [\App\Http\Controllers\Admin\SuperAdminController::class, 'runMigrations'])->name('run-migrations');
        Route::get('/logout', [\App\Http\Controllers\Admin\SuperAdminController::class, 'logout'])->name('logout');
    });

});


// ============================================
// AREA PROFESSIONISTI (Protetta - Solo Collaboratori)
// ============================================

Route::middleware(['auth', 'tipo_utente:professionista'])->prefix('professionista')->name('professionista.')->group(function () {

    // Dashboard Professionista
    Route::get('/dashboard', [\App\Http\Controllers\Professionista\DashboardController::class, 'index'])->name('dashboard');

    // ============================================
    // PROFILO
    // ============================================
    Route::prefix('profilo')->name('profilo.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Professionista\ProfiloController::class, 'index'])->name('index');
        Route::post('/aggiorna', [\App\Http\Controllers\Professionista\ProfiloController::class, 'aggiornaProfilo'])->name('aggiorna');
        Route::get('/cambia-password', [\App\Http\Controllers\Professionista\ProfiloController::class, 'cambiaPassword'])->name('cambia-password');
        Route::post('/cambia-password', [\App\Http\Controllers\Professionista\ProfiloController::class, 'salvaNuovaPassword'])->name('salva-password');
    });

    // ============================================
    // AGENDA PERSONALE (Lezioni filtrate)
    // ============================================
    Route::prefix('lezioni')->name('lezioni.')->group(function () {
        // Lista lezioni (solo proprie)
        Route::get('/', [\App\Http\Controllers\Professionista\LezioniController::class, 'index'])->name('index');

        // Visualizza lezione
        Route::get('/{id}', [\App\Http\Controllers\Professionista\LezioniController::class, 'show'])->name('show');

        // Gestione prenotazioni e presenze
        Route::get('/{id}/presenze', [\App\Http\Controllers\Professionista\LezioniController::class, 'gestionePresenze'])->name('presenze');
        Route::post('/{lezioneId}/check-in/{clienteId}', [\App\Http\Controllers\Professionista\LezioniController::class, 'checkIn'])->name('check-in');
        Route::post('/{lezioneId}/check-out/{clienteId}', [\App\Http\Controllers\Professionista\LezioniController::class, 'checkOut'])->name('check-out');
        Route::post('/{lezioneId}/segna-assente/{clienteId}', [\App\Http\Controllers\Professionista\LezioniController::class, 'segnaAssente'])->name('segna-assente');
        Route::post('/{lezioneId}/annulla-assenza/{clienteId}', [\App\Http\Controllers\Professionista\LezioniController::class, 'annullaAssenza'])->name('annulla-assenza');
    });

    // ============================================
    // CALENDARIO PERSONALE (solo propri corsi)
    // ============================================
    Route::prefix('calendario')->name('calendario.')->group(function () {
        // Vista calendario
        Route::get('/', [\App\Http\Controllers\Professionista\CalendarioController::class, 'index'])->name('index');

        // API per eventi calendario (filtrati)
        Route::get('/events', [\App\Http\Controllers\Professionista\CalendarioController::class, 'getEvents'])->name('events');

        // Dettagli lezione
        Route::get('/{id}', [\App\Http\Controllers\Professionista\CalendarioController::class, 'show'])->name('show');

        // Presenze rapide dal calendario
        Route::post('/{lezione}/check-in/{cliente}', [\App\Http\Controllers\Professionista\CalendarioController::class, 'checkIn'])->name('check-in');
        Route::post('/{lezione}/check-out/{cliente}', [\App\Http\Controllers\Professionista\CalendarioController::class, 'checkOut'])->name('check-out');
        Route::post('/{lezione}/segna-assente/{cliente}', [\App\Http\Controllers\Professionista\CalendarioController::class, 'segnaAssente'])->name('segna-assente');
        Route::post('/{lezione}/annulla-assenza/{cliente}', [\App\Http\Controllers\Professionista\CalendarioController::class, 'annullaAssenza'])->name('annulla-assenza');
    });

    // ============================================
    // COMPENSI E PAGAMENTI
    // ============================================
    Route::prefix('compensi')->name('compensi.')->group(function () {
        // Dashboard compensi
        Route::get('/', [\App\Http\Controllers\Professionista\CompensiController::class, 'index'])->name('index');

        // Storico pagamenti
        Route::get('/storico', [\App\Http\Controllers\Professionista\CompensiController::class, 'storico'])->name('storico');

        // Dettaglio periodo
        Route::get('/periodo/{anno}/{mese}', [\App\Http\Controllers\Professionista\CompensiController::class, 'dettaglioPeriodo'])->name('periodo');
    });

    // ============================================
    // DISPONIBILITÀ
    // ============================================
    Route::prefix('disponibilita')->name('disponibilita.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Professionista\DisponibilitaController::class, 'index'])->name('index');
        Route::post('/salva', [\App\Http\Controllers\Professionista\DisponibilitaController::class, 'salva'])->name('salva');
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

