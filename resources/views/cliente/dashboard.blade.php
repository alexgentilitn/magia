@extends('layouts.app')

@section('title', 'Dashboard Cliente - MA.GIA DONNA')

@section('content')
<div class="container mx-auto px-4 py-8">

    {{-- Header Dashboard --}}
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-purple-800">Benvenuta, {{ $cliente->nome }}!</h1>
        <p class="text-gray-600 mt-2">Il tuo percorso di benessere con MA.GIA DONNA</p>
    </div>

    {{-- Alert Success/Error --}}
    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
            <p class="font-bold">Successo!</p>
            <p>{{ session('success') }}</p>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
            <p class="font-bold">Attenzione</p>
            <p>{{ session('error') }}</p>
        </div>
    @endif

    {{-- KPI Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        {{-- Lezioni Prenotate --}}
        <div class="bg-white rounded-lg shadow-md p-6 border-t-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Lezioni Prenotate</p>
                    <p class="text-3xl font-bold text-purple-800 mt-2">{{ $stats['lezioni_prenotate'] }}</p>
                </div>
                <div class="bg-purple-100 rounded-full p-3">
                    <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Presenze Totali --}}
        <div class="bg-white rounded-lg shadow-md p-6 border-t-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Presenze Totali</p>
                    <p class="text-3xl font-bold text-green-800 mt-2">{{ $stats['presenze_totali'] }}</p>
                </div>
                <div class="bg-green-100 rounded-full p-3">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Programma Attivo --}}
        <div class="bg-white rounded-lg shadow-md p-6 border-t-4 {{ $stats['programma_attivo'] ? 'border-blue-500' : 'border-gray-300' }}">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Programma</p>
                    <p class="text-lg font-bold {{ $stats['programma_attivo'] ? 'text-blue-800' : 'text-gray-500' }} mt-2">
                        {{ $stats['programma_attivo'] ? 'Attivo' : 'Non Attivo' }}
                    </p>
                </div>
                <div class="bg-blue-100 rounded-full p-3">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Giorni Attività --}}
        <div class="bg-white rounded-lg shadow-md p-6 border-t-4 border-pink-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Giorni con Noi</p>
                    <p class="text-3xl font-bold text-pink-800 mt-2">{{ $stats['giorni_attivita'] }}</p>
                </div>
                <div class="bg-pink-100 rounded-full p-3">
                    <svg class="w-8 h-8 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Colonna Sinistra --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Prossime Lezioni --}}
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-2xl font-bold text-purple-800 mb-4 flex items-center">
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    Prossime Lezioni
                </h2>

                @if($prossimiLezioni->count() > 0)
                    <div class="space-y-4">
                        @foreach($prossimiLezioni as $prenotazione)
                            <div class="border-l-4 border-purple-500 pl-4 py-2 hover:bg-purple-50 transition">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h3 class="font-semibold text-gray-800">{{ $prenotazione->lezione->titolo }}</h3>
                                        <p class="text-sm text-gray-600 mt-1">
                                            <span class="inline-flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                </svg>
                                                {{ \Carbon\Carbon::parse($prenotazione->lezione->data)->format('d/m/Y H:i') }}
                                            </span>
                                        </p>
                                        <p class="text-sm text-gray-500">
                                            📍 {{ $prenotazione->lezione->sede->nome }} | 👤 {{ $prenotazione->lezione->professionista->nome }}
                                        </p>
                                    </div>
                                    <span class="px-3 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">
                                        Confermata
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-4 text-center">
                        <a href="{{ route('cliente.prenotazioni') }}" class="text-purple-600 hover:text-purple-800 font-medium">
                            Vedi tutte le prenotazioni →
                        </a>
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <p class="text-gray-500">Nessuna lezione prenotata</p>
                        <a href="{{ route('cliente.prenotazioni') }}" class="mt-4 inline-block bg-purple-600 text-white px-6 py-2 rounded-lg hover:bg-purple-700 transition">
                            Prenota una lezione
                        </a>
                    </div>
                @endif
            </div>

            {{-- Ultimi Pagamenti --}}
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-2xl font-bold text-purple-800 mb-4 flex items-center">
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                    </svg>
                    Ultimi Pagamenti
                </h2>

                @if($ultimiPagamenti->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Data</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Descrizione</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Importo</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($ultimiPagamenti as $pagamento)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 text-sm">{{ \Carbon\Carbon::parse($pagamento->data_pagamento)->format('d/m/Y') }}</td>
                                        <td class="px-4 py-3 text-sm">{{ $pagamento->descrizione ?? 'Pagamento trimestrale' }}</td>
                                        <td class="px-4 py-3 text-sm text-right font-semibold text-green-600">€ {{ number_format($pagamento->importo, 2, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4 text-center">
                        <a href="{{ route('cliente.pagamenti') }}" class="text-purple-600 hover:text-purple-800 font-medium">
                            Vedi storico completo →
                        </a>
                    </div>
                @else
                    <p class="text-center text-gray-500 py-4">Nessun pagamento registrato</p>
                @endif
            </div>

        </div>

        {{-- Colonna Destra --}}
        <div class="space-y-6">

            {{-- Programma Attivo --}}
            @if($programma)
                <div class="bg-gradient-to-br from-purple-600 to-pink-600 rounded-lg shadow-md p-6 text-white">
                    <h3 class="text-xl font-bold mb-3">Il Tuo Programma</h3>
                    <h4 class="text-2xl font-bold mb-2">{{ $programma->nome }}</h4>
                    <p class="text-purple-100 text-sm mb-4">{{ $programma->descrizione_breve }}</p>
                    <a href="{{ route('cliente.materiali') }}" class="inline-block bg-white text-purple-600 px-4 py-2 rounded-lg font-medium hover:bg-purple-50 transition">
                        Vai ai Materiali
                    </a>
                </div>
            @else
                <div class="bg-white rounded-lg shadow-md p-6 border-2 border-dashed border-gray-300">
                    <h3 class="text-lg font-bold text-gray-800 mb-2">Nessun Programma Attivo</h3>
                    <p class="text-sm text-gray-600 mb-4">Contatta l'amministratore per attivare il tuo programma personalizzato!</p>
                </div>
            @endif

            {{-- Quick Links --}}
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Accesso Rapido</h3>
                <div class="space-y-2">
                    <a href="{{ route('cliente.profilo') }}" class="block p-3 hover:bg-purple-50 rounded-lg transition flex items-center">
                        <svg class="w-5 h-5 mr-3 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <span class="text-gray-700">Il Mio Profilo</span>
                    </a>
                    <a href="{{ route('cliente.parametri') }}" class="block p-3 hover:bg-purple-50 rounded-lg transition flex items-center">
                        <svg class="w-5 h-5 mr-3 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        <span class="text-gray-700">Parametri Corporei</span>
                    </a>
                    <a href="{{ route('cliente.materiali') }}" class="block p-3 hover:bg-purple-50 rounded-lg transition flex items-center">
                        <svg class="w-5 h-5 mr-3 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                        <span class="text-gray-700">Materiali & Schede</span>
                    </a>
                    <a href="{{ route('cliente.documenti') }}" class="block p-3 hover:bg-purple-50 rounded-lg transition flex items-center">
                        <svg class="w-5 h-5 mr-3 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                        </svg>
                        <span class="text-gray-700">Carica Documenti</span>
                    </a>
                </div>
            </div>

        </div>
    </div>

</div>
@endsection
