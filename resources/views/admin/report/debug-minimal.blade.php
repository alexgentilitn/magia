@extends('layouts.admin-minimal')

@section('title', 'Debug Report Minimal')

@section('content')
<div class="container mx-auto px-4 py-6">
    <h1 class="text-3xl font-bold mb-6" style="color: red; background: yellow;">DEBUG REPORT - LAYOUT MINIMAL</h1>

    <div class="bg-white rounded-lg shadow p-6 mb-4" style="border: 3px solid red;">
        <h2 class="text-xl font-bold mb-4">Statistiche Generali</h2>
        <p>Totale Lezioni: {{ $statistiche['totale_lezioni'] ?? 'N/A' }}</p>
        <p>Lezioni Completate: {{ $statistiche['lezioni_completate'] ?? 'N/A' }}</p>
        <p>Totale Partecipanti: {{ $statistiche['totale_partecipanti'] ?? 'N/A' }}</p>
    </div>
</div>
@endsection
