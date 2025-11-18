@extends('layouts.professionista')

@section('titolo', 'Gestione Presenze')
@section('sottotitolo', $lezione->programma->nome ?? 'Lezione')

@section('content')
<div class="space-y-6">

    <!-- Info Lezione -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-semibold text-gray-900">{{ $lezione->programma->nome ?? 'Lezione' }}</h3>
                <p class="text-sm text-gray-600 mt-1">
                    {{ $lezione->data->format('d/m/Y') }} -
                    {{ \Carbon\Carbon::parse($lezione->ora_inizio)->format('H:i') }} -
                    {{ \Carbon\Carbon::parse($lezione->ora_fine)->format('H:i') }}
                </p>
            </div>
            <div>
                <span class="px-3 py-1 rounded-full text-sm font-semibold {{ $lezione->badge_stato }}">
                    {{ ucfirst($lezione->stato) }}
                </span>
            </div>
        </div>
    </div>

    <!-- Lista Partecipanti -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Partecipanti ({{ $partecipanti->count() }})</h3>
        </div>
        <div class="p-6">
            @if($partecipanti->count() > 0)
            <div class="space-y-4">
                @foreach($partecipanti as $partecipante)
                <div class="border rounded-lg p-4 hover:bg-gray-50 transition">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <h4 class="font-semibold text-gray-900">
                                {{ $partecipante->nome }} {{ $partecipante->cognome }}
                            </h4>
                            <p class="text-sm text-gray-600">{{ $partecipante->email }}</p>
                        </div>
                        <div class="flex items-center space-x-4">
                            <!-- Stato -->
                            <div class="text-center">
                                @if($partecipante->stato === 'presente')
                                    <span class="inline-block px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-semibold">
                                        <i class="fas fa-check-circle mr-1"></i> Presente
                                    </span>
                                @elseif($partecipante->stato === 'assente')
                                    <span class="inline-block px-3 py-1 bg-red-100 text-red-800 rounded-full text-sm font-semibold">
                                        <i class="fas fa-times-circle mr-1"></i> Assente
                                    </span>
                                @else
                                    <span class="inline-block px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-semibold">
                                        <i class="fas fa-clock mr-1"></i> Prenotata
                                    </span>
                                @endif
                            </div>

                            <!-- Check-in/out times -->
                            @if($partecipante->check_in_time || $partecipante->check_out_time)
                            <div class="text-xs text-gray-500">
                                @if($partecipante->check_in_time)
                                    <div>In: {{ \Carbon\Carbon::parse($partecipante->check_in_time)->format('H:i') }}</div>
                                @endif
                                @if($partecipante->check_out_time)
                                    <div>Out: {{ \Carbon\Carbon::parse($partecipante->check_out_time)->format('H:i') }}</div>
                                @endif
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-12">
                <i class="fas fa-users-slash text-6xl text-gray-300 mb-4"></i>
                <p class="text-gray-500">Nessun partecipante iscritto</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Pulsante Indietro -->
    <div>
        <a href="{{ route('professionista.lezioni.index') }}"
           class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-lg transition">
            <i class="fas fa-arrow-left mr-2"></i>
            Torna all'Agenda
        </a>
    </div>
</div>
@endsection
