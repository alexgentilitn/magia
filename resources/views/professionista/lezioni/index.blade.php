@extends('layouts.professionista')

@section('titolo', 'Agenda Lezioni')
@section('sottotitolo', 'Visualizza e gestisci le tue lezioni')

@section('content')
<div class="space-y-6">

    <!-- Stats Rapide -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-calendar-day text-3xl text-blue-500"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Oggi</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['oggi'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-calendar-week text-3xl text-green-500"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Questa Settimana</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['settimana'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-calendar-alt text-3xl text-purple-500"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Questo Mese</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['mese'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-list text-3xl text-orange-500"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Totale</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['totale'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista Lezioni -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Le Tue Lezioni</h3>
        </div>
        <div class="overflow-x-auto">
            @if($lezioni->count() > 0)
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Data</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Orario</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Programma</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sede</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Partecipanti</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stato</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Azioni</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($lezioni as $lezione)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $lezione->data->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ \Carbon\Carbon::parse($lezione->ora_inizio)->format('H:i') }} - {{ \Carbon\Carbon::parse($lezione->ora_fine)->format('H:i') }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            {{ $lezione->programma->nome ?? 'N/D' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            {{ $lezione->sede->nome ?? 'N/D' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <span class="inline-flex items-center">
                                <i class="fas fa-users mr-2 text-gray-400"></i>
                                {{ $lezione->numero_partecipanti ?? 0 }} / {{ $lezione->posti_totali }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $lezione->badge_stato }}">
                                {{ ucfirst($lezione->stato) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <a href="{{ route('professionista.lezioni.show', $lezione->id) }}"
                               class="text-violet-600 hover:text-violet-900">
                                <i class="fas fa-eye"></i> Dettagli
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <div class="px-6 py-12 text-center">
                <i class="fas fa-calendar-times text-6xl text-gray-300 mb-4"></i>
                <p class="text-gray-500">Nessuna lezione trovata</p>
            </div>
            @endif
        </div>

        @if($lezioni->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $lezioni->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
