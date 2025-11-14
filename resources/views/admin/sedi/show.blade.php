@extends('layouts.admin')

@section('titolo', 'Dettaglio Sede')

@section('contenuto')
<div class="p-6">
    <div class="mb-6">
        <a href="{{ route('admin.sedi.index') }}" class="text-fucsia-magia hover:text-viola-magia mb-2 inline-block">
            <i class="fas fa-arrow-left mr-2"></i> Torna alle sedi
        </a>
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">{{ $sede->nome }}</h1>
                <p class="text-gray-600 mt-1">
                    <i class="fas fa-map-marker-alt mr-1"></i>
                    {{ $sede->indirizzo_completo }}
                </p>
            </div>
            <div class="flex gap-2">
                @if($sede->sede_principale)
                <span class="px-3 py-1 rounded-full bg-purple-100 text-purple-800">
                    <i class="fas fa-star"></i> Sede Principale
                </span>
                @endif
                <span class="px-3 py-1 rounded-full {{ $sede->attiva ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                    {{ $sede->attiva ? 'Attiva' : 'Non Attiva' }}
                </span>
                @if($sede->visibile_pubblico)
                <span class="px-3 py-1 rounded-full bg-blue-100 text-blue-800">
                    <i class="fas fa-eye"></i> Pubblica
                </span>
                @endif
            </div>
        </div>
    </div>

    <!-- Statistiche Sede -->
    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-xs text-gray-600">Programmi</p>
            <p class="text-2xl font-bold text-gray-800">{{ $statistiche['programmi_totali'] }}</p>
            <p class="text-xs text-green-600">{{ $statistiche['programmi_attivi'] }} attivi</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-xs text-gray-600">Lezioni</p>
            <p class="text-2xl font-bold text-gray-800">{{ $statistiche['lezioni_totali'] }}</p>
            <p class="text-xs text-blue-600">{{ $statistiche['lezioni_future'] }} future</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-xs text-gray-600">Professionisti</p>
            <p class="text-2xl font-bold text-gray-800">{{ $statistiche['professionisti_assegnati'] }}</p>
        </div>
        @if($sede->capienza_massima)
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-xs text-gray-600">Capienza</p>
            <p class="text-2xl font-bold text-gray-800">{{ $sede->capienza_massima }}</p>
            <p class="text-xs text-gray-600">persone max</p>
        </div>
        @endif
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Info Principali -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Informazioni Generali -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold mb-4">Informazioni Generali</h2>

                @if($sede->descrizione)
                <div class="mb-4">
                    <p class="text-sm text-gray-600 mb-1">Descrizione</p>
                    <p class="text-gray-800">{{ $sede->descrizione }}</p>
                </div>
                @endif

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-600">Indirizzo</p>
                        <p class="font-medium">{{ $sede->indirizzo_via }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Città</p>
                        <p class="font-medium">{{ $sede->indirizzo_citta }} ({{ $sede->indirizzo_provincia }})</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">CAP</p>
                        <p class="font-medium">{{ $sede->indirizzo_cap }}</p>
                    </div>
                    @if($sede->telefono)
                    <div>
                        <p class="text-sm text-gray-600">Telefono</p>
                        <p class="font-medium">{{ $sede->telefono }}</p>
                    </div>
                    @endif
                    @if($sede->email)
                    <div>
                        <p class="text-sm text-gray-600">Email</p>
                        <p class="font-medium">{{ $sede->email }}</p>
                    </div>
                    @endif
                </div>

                @if($sede->latitudine && $sede->longitudine)
                <div class="mt-4">
                    <a href="{{ $sede->link_maps }}" target="_blank" class="text-fucsia-magia hover:text-viola-magia">
                        <i class="fas fa-map-marked-alt mr-1"></i> Apri in Google Maps
                    </a>
                </div>
                @endif
            </div>

            <!-- Prossime Lezioni -->
            @if($prossimeLezioni->count() > 0)
            <div class="bg-white rounded-lg shadow">
                <div class="p-4 border-b">
                    <h2 class="text-lg font-semibold">Prossime Lezioni in questa Sede</h2>
                </div>
                <div class="p-4">
                    <div class="space-y-3">
                        @foreach($prossimeLezioni as $lezione)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex-1">
                                <p class="font-medium text-gray-800">{{ $lezione->titolo }}</p>
                                <p class="text-sm text-gray-600">
                                    <i class="far fa-clock mr-1"></i>
                                    {{ $lezione->data->format('d/m/Y') }} - {{ substr($lezione->ora_inizio, 0, 5) }}
                                    @if($lezione->programma)
                                    <span class="ml-2">
                                        <i class="fas fa-dumbbell mr-1"></i>{{ $lezione->programma->nome }}
                                    </span>
                                    @endif
                                </p>
                            </div>
                            <a href="{{ route('admin.lezioni.show', $lezione->id) }}" class="text-blue-600 hover:text-blue-800">
                                <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Azioni Rapide -->
        <div class="space-y-4">
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold mb-4">Azioni</h2>
                <div class="space-y-2">
                    <a href="{{ route('admin.sedi.edit', $sede->id) }}" class="block w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-center">
                        <i class="fas fa-edit mr-2"></i> Modifica
                    </a>

                    @if(!$sede->sede_principale)
                    <form method="POST" action="{{ route('admin.sedi.set-principale', $sede->id) }}" class="block">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="w-full px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
                            <i class="fas fa-star mr-2"></i> Imposta come Principale
                        </button>
                    </form>
                    @endif

                    <form method="POST" action="{{ route('admin.sedi.toggle-attiva', $sede->id) }}" class="block">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="w-full px-4 py-2 {{ $sede->attiva ? 'bg-yellow-600 hover:bg-yellow-700' : 'bg-green-600 hover:bg-green-700' }} text-white rounded-lg">
                            <i class="fas fa-power-off mr-2"></i> {{ $sede->attiva ? 'Disattiva' : 'Attiva' }}
                        </button>
                    </form>

                    <a href="{{ route('admin.sedi.orari', $sede->id) }}" class="block w-full px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-center">
                        <i class="fas fa-clock mr-2"></i> Gestisci Orari
                    </a>

                    <form method="POST" action="{{ route('admin.sedi.destroy', $sede->id) }}" class="block">
                        @csrf
                        @method('DELETE')
                        <button type="submit" onclick="return confirm('Eliminare questa sede?')" class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                            <i class="fas fa-trash mr-2"></i> Elimina
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
