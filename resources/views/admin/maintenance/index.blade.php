@extends('layouts.admin')

@section('titolo', 'Manutenzione Database')

@section('contenuto')
<div class="p-6 max-w-7xl mx-auto">

    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">
            <i class="fas fa-tools text-fucsia-magia mr-3"></i>
            Manutenzione Database
        </h1>
        <p class="text-gray-600">Strumenti per correggere e verificare l'integrità del database</p>
    </div>

    <!-- Messaggi di feedback -->
    @if(session('success'))
    <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-r-lg">
        <div class="flex items-start">
            <i class="fas fa-check-circle text-green-500 text-xl mr-3 mt-0.5"></i>
            <div class="flex-1">
                <p class="text-green-800 font-medium whitespace-pre-line">{{ session('success') }}</p>
            </div>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg">
        <div class="flex items-start">
            <i class="fas fa-exclamation-circle text-red-500 text-xl mr-3 mt-0.5"></i>
            <div class="flex-1">
                <p class="text-red-800 font-medium">{{ session('error') }}</p>
            </div>
        </div>
    </div>
    @endif

    @if(session('info'))
    <div class="mb-6 bg-blue-50 border-l-4 border-blue-500 p-4 rounded-r-lg">
        <div class="flex items-start">
            <i class="fas fa-info-circle text-blue-500 text-xl mr-3 mt-0.5"></i>
            <div class="flex-1">
                <p class="text-blue-800 font-medium">{{ session('info') }}</p>
            </div>
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Card 1: Fix Visibilità Calendario -->
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="bg-gradient-to-r from-viola-magia to-fucsia-magia p-4">
                <h3 class="text-xl font-bold text-white flex items-center">
                    <i class="fas fa-calendar-check mr-3"></i>
                    Fix Visibilità Calendario
                </h3>
            </div>
            <div class="p-6">
                <p class="text-gray-700 mb-4">
                    Corregge tutte le lezioni che non sono visibili nel calendario impostando
                    il campo <code class="bg-gray-100 px-2 py-1 rounded text-sm">visibile_calendario = true</code>
                </p>

                @if($lezioniSenzaVisibilita > 0)
                <div class="mb-4 bg-yellow-50 border-l-4 border-yellow-500 p-3 rounded-r">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-triangle text-yellow-500 mr-2"></i>
                        <span class="text-yellow-800 font-medium">
                            Trovate <strong>{{ $lezioniSenzaVisibilita }}</strong> lezioni non visibili nel calendario
                        </span>
                    </div>
                </div>

                <form action="{{ route('admin.maintenance.fix-visibilita') }}" method="POST" id="fix-visibilita-form">
                    @csrf
                    <button type="button"
                            onclick="confermaAzione('fix-visibilita-form', 'Aggiornare {{ $lezioniSenzaVisibilita }} lezioni?', 'Questa operazione imposterà visibile_calendario=true per tutte le lezioni. L\'operazione potrebbe richiedere alcuni secondi.', 'Sì, correggi')"
                            class="w-full px-6 py-3 bg-gradient-to-r from-viola-magia to-fucsia-magia text-white rounded-lg hover:shadow-lg transition font-medium">
                        <i class="fas fa-magic mr-2"></i>
                        Correggi {{ $lezioniSenzaVisibilita }} lezioni
                    </button>
                </form>
                @else
                <div class="mb-4 bg-green-50 border-l-4 border-green-500 p-3 rounded-r">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-green-500 mr-2"></i>
                        <span class="text-green-800 font-medium">
                            Tutto OK! Nessuna lezione da correggere
                        </span>
                    </div>
                </div>

                <button type="button"
                        disabled
                        class="w-full px-6 py-3 bg-gray-300 text-gray-500 rounded-lg cursor-not-allowed font-medium">
                    <i class="fas fa-check mr-2"></i>
                    Nessuna correzione necessaria
                </button>
                @endif
            </div>
        </div>

        <!-- Card 2: Verifica Integrità -->
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="bg-gradient-to-r from-blue-500 to-purple-500 p-4">
                <h3 class="text-xl font-bold text-white flex items-center">
                    <i class="fas fa-check-double mr-3"></i>
                    Verifica Integrità
                </h3>
            </div>
            <div class="p-6">
                <p class="text-gray-700 mb-4">
                    Esegue una verifica completa dell'integrità del database e identifica eventuali problemi
                    o incoerenze nei dati.
                </p>

                <div class="mb-4 bg-blue-50 border-l-4 border-blue-500 p-3 rounded-r">
                    <p class="text-blue-800 text-sm">
                        <i class="fas fa-info-circle mr-2"></i>
                        Questa operazione <strong>non modifica</strong> i dati, ma solo li analizza
                    </p>
                </div>

                <a href="{{ route('admin.maintenance.verifica') }}"
                   class="block w-full px-6 py-3 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition font-medium text-center">
                    <i class="fas fa-search mr-2"></i>
                    Avvia Verifica
                </a>
            </div>
        </div>

    </div>

    <!-- Informazioni utili -->
    <div class="mt-8 bg-gray-50 rounded-lg p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-3 flex items-center">
            <i class="fas fa-info-circle text-fucsia-magia mr-2"></i>
            Informazioni
        </h3>
        <div class="space-y-2 text-sm text-gray-700">
            <p>
                <strong>• Fix Visibilità Calendario:</strong>
                Utilizzare quando le lezioni create (specialmente quelle ricorrenti) non appaiono nel calendario.
            </p>
            <p>
                <strong>• Verifica Integrità:</strong>
                Utilizzare periodicamente per identificare problemi nei dati (prenotazioni incoerenti, relazioni mancanti, ecc.).
            </p>
            <p class="text-yellow-700 bg-yellow-50 p-2 rounded border-l-4 border-yellow-400">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                <strong>Attenzione:</strong> Eseguire sempre un backup del database prima di operazioni di manutenzione massive.
            </p>
        </div>
    </div>

    <!-- Link ritorno -->
    <div class="mt-6">
        <a href="{{ route('admin.lezioni.index') }}"
           class="text-fucsia-magia hover:text-viola-magia inline-flex items-center">
            <i class="fas fa-arrow-left mr-2"></i>
            Torna alla gestione lezioni
        </a>
    </div>

</div>
@endsection
