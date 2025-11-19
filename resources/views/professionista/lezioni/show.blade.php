@extends('layouts.professionista')

@section('titolo', 'Dettagli Lezione')
@section('sottotitolo', $lezione->programma->nome ?? 'Lezione')

@section('content')
<div class="space-y-6">

    <!-- Informazioni Lezione -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Informazioni Lezione</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <p class="text-sm font-medium text-gray-600">Data</p>
                <p class="text-lg text-gray-900">{{ $lezione->data->format('d/m/Y') }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-600">Orario</p>
                <p class="text-lg text-gray-900">
                    {{ \Carbon\Carbon::parse($lezione->ora_inizio)->format('H:i') }} -
                    {{ \Carbon\Carbon::parse($lezione->ora_fine)->format('H:i') }}
                </p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-600">Programma</p>
                <p class="text-lg text-gray-900">{{ $lezione->programma->nome ?? 'N/D' }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-600">Sede</p>
                <p class="text-lg text-gray-900">{{ $lezione->sede->nome ?? 'N/D' }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-600">Durata</p>
                <p class="text-lg text-gray-900">{{ $lezione->durata_minuti }} minuti</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-600">Stato</p>
                <p>
                    <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $lezione->badge_stato }}">
                        {{ ucfirst($lezione->stato) }}
                    </span>
                </p>
            </div>
        </div>

        @if($lezione->note_pubbliche)
        <div class="mt-4">
            <p class="text-sm font-medium text-gray-600">Note</p>
            <p class="text-gray-900 mt-1">{{ $lezione->note_pubbliche }}</p>
        </div>
        @endif
    </div>

    <!-- Partecipanti -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">
                Partecipanti ({{ $partecipanti->count() }})
            </h3>
        </div>
        <div class="overflow-x-auto">
            @if($partecipanti->count() > 0)
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cliente</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Telefono</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stato</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Check-in</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Check-out</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($partecipanti as $partecipante)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $partecipante->nome }} {{ $partecipante->cognome }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $partecipante->email }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $partecipante->telefono ?? 'N/D' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($partecipante->stato === 'presente')
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                    Presente
                                </span>
                            @elseif($partecipante->stato === 'assente')
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                    Assente
                                </span>
                            @else
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                    Prenotata
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $partecipante->check_in_time ? \Carbon\Carbon::parse($partecipante->check_in_time)->format('H:i') : '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $partecipante->check_out_time ? \Carbon\Carbon::parse($partecipante->check_out_time)->format('H:i') : '-' }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <div class="px-6 py-12 text-center">
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
