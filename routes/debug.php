<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

/**
 * ROUTES DEBUG E TESTING
 *
 * Questo file contiene routes temporanee per debug e testing.
 * Viene caricato SOLO in ambiente local/development.
 *
 * NON utilizzare in produzione!
 */

Route::middleware(['auth', 'tipo_utente:amministratore'])->prefix('admin')->name('admin.')->group(function () {

    // ============================================
    // TEST VIEWS SEMPLICI
    // ============================================

    Route::get('/test-simple', function () {
        return view('test-simple');
    })->name('test-simple');

    // ============================================
    // DEBUG REPORT MINIMAL
    // ============================================

    Route::get('/report/debug-minimal', function() {
        $controller = new \App\Http\Controllers\Admin\ReportController();
        $reflection = new \ReflectionClass($controller);

        $dataInizio = now()->subMonth()->format('Y-m-d');
        $dataFine = now()->format('Y-m-d');

        $method = $reflection->getMethod('getStatisticheGenerali');
        $method->setAccessible(true);
        $statistiche = $method->invoke($controller, $dataInizio, $dataFine);

        return view('admin.report.debug-minimal', compact('statistiche'));
    })->name('report.debug-minimal');

    // ============================================
    // DEBUG DATABASE
    // ============================================

    Route::get('/debug/database', function () {
        $totaleLezioni = \App\Models\Lezione::count();
        $dataMin = \App\Models\Lezione::min('data');
        $dataMax = \App\Models\Lezione::max('data');

        $dataInizio = now()->subMonth()->format('Y-m-d');
        $dataFine = now()->format('Y-m-d');
        $lezioniPeriodo = \App\Models\Lezione::whereBetween('data', [$dataInizio, $dataFine])->count();

        return response()->json([
            'totale_lezioni' => $totaleLezioni,
            'prima_lezione' => $dataMin,
            'ultima_lezione' => $dataMax,
            'periodo_report' => [
                'data_inizio' => $dataInizio,
                'data_fine' => $dataFine,
                'lezioni_nel_periodo' => $lezioniPeriodo
            ],
            'oggi' => now()->format('Y-m-d')
        ]);
    })->name('debug.database');

    // ============================================
    // TEST REPORT SENZA LAYOUT
    // ============================================

    Route::get('/test-report-noLayout', function () {
        $controller = new \App\Http\Controllers\Admin\ReportController();
        $reflection = new \ReflectionClass($controller);

        $dataInizio = now()->subMonth()->format('Y-m-d');
        $dataFine = now()->format('Y-m-d');

        $method = $reflection->getMethod('getStatisticheGenerali');
        $method->setAccessible(true);
        $stats = $method->invoke($controller, $dataInizio, $dataFine);

        return '<html><head><title>Test Report</title></head><body style="padding: 50px; font-family: Arial;">
        <h1 style="color: red;">TEST REPORT - NO LAYOUT</h1>
        <h2>Statistiche Generali</h2>
        <ul>
            <li>Totale Lezioni: ' . $stats['totale_lezioni'] . '</li>
            <li>Lezioni Completate: ' . $stats['lezioni_completate'] . '</li>
            <li>Totale Partecipanti: ' . $stats['totale_partecipanti'] . '</li>
            <li>Tasso Occupazione: ' . $stats['tasso_occupazione'] . '%</li>
        </ul>
        <pre>' . print_r($stats, true) . '</pre>
        </body></html>';
    })->name('test.report-no-layout');

    // ============================================
    // DEBUG REPORT DATA
    // ============================================

    Route::get('/debug/report-data', function () {
        $dataInizio = now()->subMonth()->format('Y-m-d');
        $dataFine = now()->format('Y-m-d');

        // Test statistiche generali
        $totaleLezioni = \App\Models\Lezione::whereBetween('data', [$dataInizio, $dataFine])->count();

        // Test statistiche presenze
        $presenti = DB::table('cliente_lezione')
            ->join('lezioni', 'cliente_lezione.lezione_id', '=', 'lezioni.id')
            ->whereBetween('lezioni.data', [$dataInizio, $dataFine])
            ->where('cliente_lezione.stato', 'presente')
            ->count();

        // Test lezioni per stato
        $lezioniPerStato = \App\Models\Lezione::whereBetween('data', [$dataInizio, $dataFine])
            ->select('stato', DB::raw('count(*) as totale'))
            ->groupBy('stato')
            ->get();

        // Test top professionisti
        $topProfessionisti = DB::table('lezioni')
            ->join('utenti', 'lezioni.professionista_id', '=', 'utenti.id')
            ->whereBetween('lezioni.data', [$dataInizio, $dataFine])
            ->select(
                'lezioni.professionista_id',
                'utenti.nome as professionista_nome',
                'utenti.cognome as professionista_cognome',
                DB::raw('count(*) as totale_lezioni')
            )
            ->groupBy('lezioni.professionista_id', 'utenti.nome', 'utenti.cognome')
            ->get();

        return response()->json([
            'periodo' => ['inizio' => $dataInizio, 'fine' => $dataFine],
            'totale_lezioni' => $totaleLezioni,
            'presenti' => $presenti,
            'lezioni_per_stato' => $lezioniPerStato,
            'top_professionisti' => $topProfessionisti,
        ]);
    })->name('debug.report-data');
});
