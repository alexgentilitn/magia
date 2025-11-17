@extends('layouts.admin')

@section('titolo', 'Storico Programmi - ' . $cliente->nome_completo)

@section('contenuto')
<div class="p-6">
    <!-- Header -->
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 mb-2">
                <i class="fas fa-history text-fucsia-magia mr-3"></i>
                Storico Programmi
            </h1>
            <p class="text-gray-600">
                Cliente: <strong>{{ $cliente->nome_completo }}</strong>
                @if($cliente->codice_cliente)
                    <span class="ml-2 text-sm text-gray-500">({{ $cliente->codice_cliente }})</span>
                @endif
            </p>
        </div>

        <a href="{{ route('admin.clienti.show', $cliente->id) }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
            <i class="fas fa-arrow-left mr-2"></i>
            Torna al Profilo
        </a>
    </div>

    <!-- Statistiche Riepilogative -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Programmi Completati</p>
                    <p class="text-3xl font-bold text-green-600">{{ $cliente->programmiCompletati()->count() }}</p>
                </div>
                <div class="bg-green-100 p-3 rounded-full">
                    <i class="fas fa-check-circle text-green-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Programma Attuale</p>
                    <p class="text-3xl font-bold text-blue-600">
                        {{ $cliente->programma ? '1' : '0' }}
                    </p>
                </div>
                <div class="bg-blue-100 p-3 rounded-full">
                    <i class="fas fa-dumbbell text-blue-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Giorni Totali</p>
                    <p class="text-3xl font-bold text-fucsia-magia">{{ $giorniTotali }}</p>
                </div>
                <div class="bg-pink-100 p-3 rounded-full">
                    <i class="fas fa-calendar text-fucsia-magia text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Ultimo Programma</p>
                    <p class="text-sm font-semibold text-gray-800">
                        {{ $ultimoProgramma ? $ultimoProgramma->fine_programma->format('d/m/Y') : 'N/D' }}
                    </p>
                </div>
                <div class="bg-gray-100 p-3 rounded-full">
                    <i class="fas fa-clock text-gray-600 text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Timeline Programmi -->
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-xl font-bold text-gray-800">
                <i class="fas fa-timeline mr-2"></i>
                Timeline Completa
            </h2>
        </div>

        <div class="p-6">
            @if($programmi->isEmpty())
                <div class="text-center py-12">
                    <i class="fas fa-inbox text-gray-300 text-6xl mb-4"></i>
                    <p class="text-gray-500 text-lg">Nessun programma registrato</p>
                </div>
            @else
                <div class="relative">
                    <!-- Linea verticale timeline -->
                    <div class="absolute left-8 top-0 bottom-0 w-0.5 bg-gray-200"></div>

                    @foreach($programmi as $item)
                        <div class="relative pl-20 pb-8 last:pb-0">
                            <!-- Punto timeline -->
                            <div class="absolute left-6 w-4 h-4 rounded-full {{ $item->attivo ? 'bg-green-500' : 'bg-gray-300' }} border-4 border-white"></div>

                            <!-- Card Programma -->
                            <div class="bg-gray-50 rounded-lg p-6 hover:shadow-md transition-shadow">
                                <div class="flex items-start justify-between mb-4">
                                    <div>
                                        <h3 class="text-lg font-bold text-gray-800">
                                            {{ $item->programma->nome ?? 'Programma Personalizzato' }}
                                        </h3>
                                        <p class="text-sm text-gray-600">
                                            <i class="fas fa-calendar mr-1"></i>
                                            Dal {{ $item->inizio_programma->format('d/m/Y') }}
                                            al {{ $item->fine_programma ? $item->fine_programma->format('d/m/Y') : 'In corso' }}
                                        </p>
                                    </div>

                                    <span class="px-3 py-1 text-xs font-semibold rounded-full
                                        {{ $item->attivo ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' }}">
                                        {{ $item->attivo ? 'In Corso' : 'Completato' }}
                                    </span>
                                </div>

                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                    <div>
                                        <p class="text-xs text-gray-500">Durata</p>
                                        <p class="font-semibold">
                                            {{ $item->inizio_programma->diffInDays($item->fine_programma ?? now()) }} giorni
                                        </p>
                                    </div>

                                    @if($item->peso_iniziale)
                                        <div>
                                            <p class="text-xs text-gray-500">Peso Iniziale</p>
                                            <p class="font-semibold">{{ $item->peso_iniziale }} kg</p>
                                        </div>
                                    @endif

                                    @if($item->peso_attuale)
                                        <div>
                                            <p class="text-xs text-gray-500">Peso Finale</p>
                                            <p class="font-semibold">{{ $item->peso_attuale }} kg</p>
                                        </div>
                                    @endif

                                    @if($item->peso_iniziale && $item->peso_attuale)
                                        <div>
                                            <p class="text-xs text-gray-500">Variazione</p>
                                            <p class="font-semibold {{ ($item->peso_attuale - $item->peso_iniziale) < 0 ? 'text-green-600' : 'text-red-600' }}">
                                                {{ $item->peso_attuale - $item->peso_iniziale > 0 ? '+' : '' }}
                                                {{ number_format($item->peso_attuale - $item->peso_iniziale, 1) }} kg
                                            </p>
                                        </div>
                                    @endif
                                </div>

                                @if($item->note_programma)
                                    <div class="mt-4 p-3 bg-white rounded border border-gray-200">
                                        <p class="text-xs text-gray-500 mb-1">Note:</p>
                                        <p class="text-sm text-gray-700">{{ $item->note_programma }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
