@extends('layouts.admin')

@section('titolo', 'Verifica Integrità Database')

@section('contenuto')
<div class="p-6 max-w-7xl mx-auto">

    <!-- Header -->
    <div class="mb-6">
        <a href="{{ route('admin.maintenance.index') }}"
           class="text-fucsia-magia hover:text-viola-magia mb-2 inline-block">
            <i class="fas fa-arrow-left mr-2"></i> Torna alla manutenzione
        </a>
        <h1 class="text-3xl font-bold text-gray-800 mb-2">
            <i class="fas fa-check-double text-blue-500 mr-3"></i>
            Verifica Integrità Database
        </h1>
        <p class="text-gray-600">Risultati dell'analisi dell'integrità dei dati</p>
    </div>

    @if(count($problemi) === 0)
    <!-- Tutto OK -->
    <div class="bg-green-50 border-l-4 border-green-500 rounded-r-lg p-8 text-center">
        <div class="inline-block p-6 bg-green-100 rounded-full mb-4">
            <i class="fas fa-check-circle text-green-500 text-6xl"></i>
        </div>
        <h2 class="text-2xl font-bold text-green-800 mb-2">Database Integro!</h2>
        <p class="text-green-700 text-lg">
            Nessun problema rilevato. Il database è in uno stato ottimale.
        </p>
    </div>
    @else
    <!-- Problemi trovati -->
    <div class="mb-6 bg-yellow-50 border-l-4 border-yellow-500 p-4 rounded-r-lg">
        <div class="flex items-center">
            <i class="fas fa-exclamation-triangle text-yellow-500 text-2xl mr-3"></i>
            <div>
                <h3 class="font-bold text-yellow-800 text-lg">
                    Trovati {{ count($problemi) }} problemi
                </h3>
                <p class="text-yellow-700 text-sm">
                    Alcuni problemi richiedono attenzione
                </p>
            </div>
        </div>
    </div>

    <!-- Lista problemi -->
    <div class="space-y-4">
        @foreach($problemi as $index => $problema)
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="flex items-start p-6">
                <!-- Icona -->
                <div class="flex-shrink-0 mr-4">
                    @if($problema['tipo'] === 'error')
                    <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-times-circle text-red-500 text-2xl"></i>
                    </div>
                    @elseif($problema['tipo'] === 'warning')
                    <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-exclamation-triangle text-yellow-500 text-2xl"></i>
                    </div>
                    @else
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-info-circle text-blue-500 text-2xl"></i>
                    </div>
                    @endif
                </div>

                <!-- Contenuto -->
                <div class="flex-1">
                    <div class="flex items-start justify-between">
                        <div>
                            <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold mb-2
                                @if($problema['tipo'] === 'error') bg-red-100 text-red-800
                                @elseif($problema['tipo'] === 'warning') bg-yellow-100 text-yellow-800
                                @else bg-blue-100 text-blue-800 @endif">
                                {{ $problema['categoria'] }}
                            </span>
                            <h3 class="text-lg font-bold text-gray-900 mb-2">
                                {{ $problema['descrizione'] }}
                            </h3>
                        </div>

                        <!-- Azione disponibile -->
                        @if($problema['azione'])
                        <div class="ml-4">
                            @if($problema['azione'] === 'fix-visibilita')
                            <a href="{{ route('admin.maintenance.index') }}"
                               class="px-4 py-2 bg-fucsia-magia text-white rounded-lg hover:bg-viola-magia transition text-sm font-medium">
                                <i class="fas fa-magic mr-1"></i> Correggi
                            </a>
                            @endif
                        </div>
                        @endif
                    </div>

                    @if(!$problema['azione'])
                    <p class="text-gray-600 text-sm mt-2">
                        <i class="fas fa-info-circle mr-1"></i>
                        Questo problema richiede correzione manuale
                    </p>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    <!-- Informazioni verifica -->
    <div class="mt-8 bg-gray-50 rounded-lg p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-3 flex items-center">
            <i class="fas fa-clipboard-check text-fucsia-magia mr-2"></i>
            Cosa viene verificato
        </h3>
        <ul class="space-y-2 text-sm text-gray-700">
            <li class="flex items-start">
                <i class="fas fa-check text-green-500 mr-2 mt-1"></i>
                <span><strong>Visibilità Calendario:</strong> Lezioni con campo visibile_calendario null o false</span>
            </li>
            <li class="flex items-start">
                <i class="fas fa-check text-green-500 mr-2 mt-1"></i>
                <span><strong>Prenotazioni Incoerenti:</strong> Lezioni con posti_occupati &gt; 0 ma senza clienti</span>
            </li>
            <li class="flex items-start">
                <i class="fas fa-check text-green-500 mr-2 mt-1"></i>
                <span><strong>Contatori Posti:</strong> Lezioni con clienti ma posti_occupati = 0</span>
            </li>
            <li class="flex items-start">
                <i class="fas fa-check text-green-500 mr-2 mt-1"></i>
                <span><strong>Ricorrenze Orfane:</strong> Lezioni figlie che puntano a un padre inesistente</span>
            </li>
        </ul>
    </div>

    <!-- Azioni -->
    <div class="mt-6 flex gap-4">
        <a href="{{ route('admin.maintenance.verifica') }}"
           class="px-6 py-3 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition font-medium">
            <i class="fas fa-sync-alt mr-2"></i>
            Riesegui Verifica
        </a>
        <a href="{{ route('admin.maintenance.index') }}"
           class="px-6 py-3 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition font-medium">
            <i class="fas fa-arrow-left mr-2"></i>
            Torna alla Manutenzione
        </a>
    </div>

</div>
@endsection
