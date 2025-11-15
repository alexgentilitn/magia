@extends('layouts.admin')

@section('title', 'Debug Report')

@section('content')
<div class="container mx-auto px-4 py-6">
    <h1 class="text-3xl font-bold mb-6">DEBUG REPORT</h1>

    <div class="bg-white rounded-lg shadow p-6 mb-4">
        <h2 class="text-xl font-bold mb-4">Periodo</h2>
        <p>Data Inizio: {{ $dataInizio }}</p>
        <p>Data Fine: {{ $dataFine }}</p>
    </div>

    <div class="bg-white rounded-lg shadow p-6 mb-4">
        <h2 class="text-xl font-bold mb-4">Statistiche Generali</h2>
        <pre>{{ json_encode($statistiche, JSON_PRETTY_PRINT) }}</pre>
    </div>

    <div class="bg-white rounded-lg shadow p-6 mb-4">
        <h2 class="text-xl font-bold mb-4">Statistiche Presenze</h2>
        <pre>{{ json_encode($statistichePresenze, JSON_PRETTY_PRINT) }}</pre>
    </div>

    <div class="bg-white rounded-lg shadow p-6 mb-4">
        <h2 class="text-xl font-bold mb-4">Lezioni Per Stato</h2>
        <pre>{{ json_encode($lezioniPerStato, JSON_PRETTY_PRINT) }}</pre>
    </div>

    <div class="bg-white rounded-lg shadow p-6 mb-4">
        <h2 class="text-xl font-bold mb-4">Top Professionisti</h2>
        <pre>{{ json_encode($topProfessionisti, JSON_PRETTY_PRINT) }}</pre>
    </div>

    <div class="bg-white rounded-lg shadow p-6 mb-4">
        <h2 class="text-xl font-bold mb-4">Top Clienti</h2>
        <pre>{{ json_encode($topClienti, JSON_PRETTY_PRINT) }}</pre>
    </div>

    <div class="bg-white rounded-lg shadow p-6 mb-4">
        <h2 class="text-xl font-bold mb-4">Trend Giornaliero</h2>
        <pre>{{ json_encode($trendGiornaliero, JSON_PRETTY_PRINT) }}</pre>
    </div>
</div>
@endsection
