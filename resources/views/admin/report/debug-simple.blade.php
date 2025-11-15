@extends('layouts.admin')

@section('title', 'Debug Report - ERRORE')

@section('content')
<div class="container mx-auto px-4 py-6">
    <h1 class="text-3xl font-bold mb-6" style="color: white; background: red; padding: 20px;">ERRORE NEL CARICAMENTO DEBUG REPORT</h1>

    <div class="bg-red-100 border-3 border-red-500 rounded-lg p-6 mb-4">
        <h2 class="text-xl font-bold mb-4 text-red-800">Messaggio Errore:</h2>
        <pre class="bg-white p-4 rounded text-sm overflow-auto">{{ $error ?? 'Errore sconosciuto' }}</pre>
    </div>

    <div class="bg-gray-100 border border-gray-300 rounded-lg p-6">
        <h2 class="text-xl font-bold mb-4">Stack Trace:</h2>
        <pre class="bg-white p-4 rounded text-xs overflow-auto" style="max-height: 400px;">{{ $trace ?? 'Nessun trace disponibile' }}</pre>
    </div>
</div>
@endsection
