@extends('layouts.admin')

@section('titolo', 'Dettaglio Scheda')

@section('contenuto')
<div class="p-6">
    
    <!-- Header con Azioni -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">{{ $scheda->nome_scheda }}</h2>
            <p class="text-gray-600 mt-1">{{ $scheda->cliente->cognome }} {{ $scheda->cliente->nome }}</p>
        </div>
        <div class="mt-4 md:mt-0 flex gap-2">
            <a href="{{ route('admin.schede.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                <i class="fas fa-arrow-left mr-2"></i> Indietro
            </a>
            <a href="{{ route('admin.schede.edit', $scheda) }}" class="px-4 py-2 bg-fucsia-magia text-white rounded-lg hover:bg-opacity-90">
                <i class="fas fa-edit mr-2"></i> Modifica
            </a>
            <form action="{{ route('admin.schede.duplica', $scheda) }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                    <i class="fas fa-copy mr-2"></i> Duplica
                </button>
            </form>
        </div>
    </div>

    <!-- Statistiche Rapide -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-green-500">
            <p class="text-xs text-gray-600 font-medium">Stato</p>
            @php
                $badges = [
                    'bozza' => 'bg-gray-100 text-gray-800',
                    'attiva' => 'bg-green-100 text-green-800',
                    'completata' => 'bg-blue-100 text-blue-800',
                    'archiviata' => 'bg-gray-100 text-gray-600'
                ];
            @endphp
            <p class="text-lg font-bold">
                <span class="px-2 py-1 text-xs rounded-full {{ $badges[$scheda->stato] ?? 'bg-gray-100' }}">
                    {{ ucfirst($scheda->stato) }}
                </span>
            </p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-blue-500">
            <p class="text-xs text-gray-600 font-medium">Esercizi Totali</p>
            <p class="text-2xl font-bold text-gray-800">{{ $scheda->numeroEserciziTotali() }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-purple-500">
            <p class="text-xs text-gray-600 font-medium">Giorni Allenamento</p>
            <p class="text-2xl font-bold text-gray-800">{{ $scheda->numeroGiorniAllenamento() }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-yellow-500">
            <p class="text-xs text-gray-600 font-medium">Durata</p>
            <p class="text-2xl font-bold text-gray-800">
                {{ $scheda->durata_settimane ?? '-' }} 
                @if($scheda->durata_settimane)
                    <span class="text-sm font-normal">sett.</span>
                @endif
            </p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-red-500">
            <p class="text-xs text-gray-600 font-medium">Email Inviata</p>
            <p class="text-lg font-bold text-gray-800">
                @if($scheda->inviata_email)
                    <i class="fas fa-check-circle text-green-500"></i> Sì
                @else
                    <i class="fas fa-times-circle text-red-500"></i> No
                @endif
            </p>
        </div>
    </div>

    <!-- Informazioni Scheda -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4 pb-2 border-b">
            <i class="fas fa-info-circle text-fucsia-magia mr-2"></i> Informazioni Scheda
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <p class="text-sm text-gray-600">Cliente</p>
                <p class="font-semibold">{{ $scheda->cliente->cognome }} {{ $scheda->cliente->nome }}</p>
                <p class="text-sm text-gray-500">{{ $scheda->cliente->email }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Professionista</p>
                <p class="font-semibold">
                    @if($scheda->professionista)
                        {{ $scheda->professionista->nome }} {{ $scheda->professionista->cognome }}
                    @else
                        Non assegnato
                    @endif
                </p>
            </div>
            @if($scheda->data_inizio || $scheda->data_fine)
                <div>
                    <p class="text-sm text-gray-600">Periodo</p>
                    <p class="font-semibold">
                        {{ $scheda->data_inizio ? $scheda->data_inizio->format('d/m/Y') : '-' }}
                        @if($scheda->data_fine)
                            → {{ $scheda->data_fine->format('d/m/Y') }}
                        @endif
                    </p>
                </div>
            @endif
            @if($scheda->descrizione)
                <div class="md:col-span-2">
                    <p class="text-sm text-gray-600">Descrizione</p>
                    <p>{{ $scheda->descrizione }}</p>
                </div>
            @endif
            @if($scheda->obiettivi)
                <div class="md:col-span-2">
                    <p class="text-sm text-gray-600">Obiettivi</p>
                    <p>{{ $scheda->obiettivi }}</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Programma Settimanale -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4 pb-2 border-b">
            <i class="fas fa-calendar-week text-fucsia-magia mr-2"></i> Programma Settimanale
        </h3>

        @if($esercizi_per_giorno->count() > 0)
            @foreach(['Lunedì', 'Martedì', 'Mercoledì', 'Giovedì', 'Venerdì', 'Sabato', 'Domenica'] as $giorno)
                @if($esercizi_per_giorno->has($giorno))
                    <div class="mb-6">
                        <h4 class="font-bold text-gray-700 bg-gray-100 px-4 py-2 rounded-t">
                            <i class="fas fa-dumbbell mr-2 text-fucsia-magia"></i> {{ $giorno }}
                            <span class="text-sm font-normal text-gray-600">({{ $esercizi_per_giorno[$giorno]->count() }} esercizi)</span>
                        </h4>
                        <div class="border border-t-0 rounded-b p-4 space-y-3">
                            @foreach($esercizi_per_giorno[$giorno] as $esercizio)
                                <div class="bg-gray-50 p-4 rounded border-l-4 border-fucsia-magia">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <h5 class="font-bold text-gray-800">
                                                {{ $esercizio->ordine }}. {{ $esercizio->nome_esercizio }}
                                                <span class="text-xs px-2 py-1 bg-blue-100 text-blue-800 rounded ml-2">{{ $esercizio->categoria }}</span>
                                            </h5>
                                            @if($esercizio->descrizione)
                                                <p class="text-sm text-gray-600 mt-1">{{ $esercizio->descrizione }}</p>
                                            @endif
                                            <div class="flex flex-wrap gap-4 mt-2 text-sm">
                                                @if($esercizio->serie || $esercizio->ripetizioni)
                                                    <div>
                                                        <span class="text-gray-600">Serie x Rip:</span>
                                                        <span class="font-semibold">{{ $esercizio->getSerieRipetizioniFormattato() }}</span>
                                                    </div>
                                                @endif
                                                @if($esercizio->recupero_secondi)
                                                    <div>
                                                        <span class="text-gray-600">Recupero:</span>
                                                        <span class="font-semibold">{{ $esercizio->getRecuperoFormattato() }}</span>
                                                    </div>
                                                @endif
                                                @if($esercizio->peso_suggerito)
                                                    <div>
                                                        <span class="text-gray-600">Peso:</span>
                                                        <span class="font-semibold">{{ $esercizio->peso_suggerito }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                            @if($esercizio->note_esecuzione)
                                                <div class="mt-2 p-2 bg-yellow-50 border border-yellow-200 rounded text-sm">
                                                    <i class="fas fa-lightbulb text-yellow-600 mr-1"></i>
                                                    {{ $esercizio->note_esecuzione }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endforeach
        @else
            <div class="text-center py-8 text-gray-500">
                <i class="fas fa-dumbbell text-4xl mb-2"></i>
                <p>Nessun esercizio inserito</p>
            </div>
        @endif
    </div>

    <!-- Note -->
    @if($scheda->note_generali || $scheda->note_alimentazione || $scheda->consigli_professionista)
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4 pb-2 border-b">
                <i class="fas fa-sticky-note text-fucsia-magia mr-2"></i> Note e Consigli
            </h3>
            <div class="space-y-4">
                @if($scheda->note_generali)
                    <div>
                        <p class="text-sm font-semibold text-gray-700 mb-1">Note Generali</p>
                        <p class="text-gray-600">{{ $scheda->note_generali }}</p>
                    </div>
                @endif
                @if($scheda->note_alimentazione)
                    <div>
                        <p class="text-sm font-semibold text-gray-700 mb-1">Note Alimentazione</p>
                        <p class="text-gray-600">{{ $scheda->note_alimentazione }}</p>
                    </div>
                @endif
                @if($scheda->consigli_professionista)
                    <div>
                        <p class="text-sm font-semibold text-gray-700 mb-1">Consigli Professionista</p>
                        <p class="text-gray-600">{{ $scheda->consigli_professionista }}</p>
                    </div>
                @endif
            </div>
        </div>
    @endif

    <!-- Azioni -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4 pb-2 border-b">
            <i class="fas fa-cog text-fucsia-magia mr-2"></i> Azioni
        </h3>
        <div class="flex flex-wrap gap-3">
            <!-- Cambia Stato -->
            <form action="{{ route('admin.schede.cambia-stato', $scheda) }}" method="POST" class="inline">
                @csrf
                @method('PATCH')
                <select name="nuovo_stato" onchange="this.form.submit()" class="px-4 py-2 border rounded-lg">
                    <option value="">Cambia stato...</option>
                    <option value="bozza">Bozza</option>
                    <option value="attiva">Attiva</option>
                    <option value="completata">Completata</option>
                    <option value="archiviata">Archiviata</option>
                </select>
            </form>

            <!-- Genera PDF -->
            <form action="{{ route('admin.schede.genera-pdf', $scheda) }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                    <i class="fas fa-file-pdf mr-2"></i> Genera PDF
                </button>
            </form>

            <!-- Invia Email -->
            <form action="{{ route('admin.schede.invia-email', $scheda) }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600">
                    <i class="fas fa-envelope mr-2"></i> Invia Email
                </button>
            </form>

            <!-- Elimina -->
            <form action="{{ route('admin.schede.destroy', $scheda) }}" method="POST" class="inline" 
                  onsubmit="return confirm('Sicuro di eliminare questa scheda?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600">
                    <i class="fas fa-trash mr-2"></i> Elimina
                </button>
            </form>
        </div>
    </div>

</div>
@endsection
