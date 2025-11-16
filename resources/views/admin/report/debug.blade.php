@extends('layouts.admin')

@section('title', 'Debug Report')

@section('content')
<div class="container mx-auto px-4 py-6">
    <h1 class="text-3xl font-bold mb-6" style="color: red; background: yellow;">DEBUG REPORT - QUESTA RIGA DEVE ESSERE VISIBILE</h1>

    <div class="bg-white rounded-lg shadow p-6 mb-4" style="border: 3px solid red;">
        <h2 class="text-xl font-bold mb-4">TEST BASICO</h2>
        <p style="font-size: 20px; color: blue;">Se vedi questo testo, la vista si carica correttamente.</p>
        <p>Data Inizio: {{ $dataInizio ?? 'NON DEFINITO' }}</p>
        <p>Data Fine: {{ $dataFine ?? 'NON DEFINITO' }}</p>
    </div>

    <div class="bg-white rounded-lg shadow p-6 mb-4">
        <h2 class="text-xl font-bold mb-4">Statistiche Generali</h2>
        <p>Totale Lezioni: {{ $statistiche['totale_lezioni'] ?? 'N/A' }}</p>
        <p>Lezioni Completate: {{ $statistiche['lezioni_completate'] ?? 'N/A' }}</p>
        <p>Totale Partecipanti: {{ $statistiche['totale_partecipanti'] ?? 'N/A' }}</p>
        <p>Tasso Occupazione: {{ $statistiche['tasso_occupazione'] ?? 'N/A' }}%</p>
    </div>

    <div class="bg-white rounded-lg shadow p-6 mb-4">
        <h2 class="text-xl font-bold mb-4">Raw Data (JSON)</h2>
        <textarea style="width: 100%; height: 300px; font-family: monospace;">
{{ json_encode([
    'statistiche' => $statistiche,
    'statistichePresenze' => $statistichePresenze,
    'lezioniPerStato' => $lezioniPerStato,
    'topProfessionisti' => $topProfessionisti,
    'topClienti' => $topClienti,
    'trendGiornaliero' => $trendGiornaliero
], JSON_PRETTY_PRINT) }}
        </textarea>
    </div>
</div>
@endsection
