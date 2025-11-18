@extends('layouts.professionista')

@section('titolo', 'Dettaglio Compensi')
@section('sottotitolo', $periodo)

@section('content')
<div class="space-y-6">

    <!-- Stats Periodo -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-gradient-to-br from-violet-500 to-purple-600 rounded-lg shadow-lg p-6 text-white">
            <p class="text-sm opacity-90">Compenso Totale</p>
            <p class="text-3xl font-bold mt-2">€ {{ number_format($compensoTotale, 2, ',', '.') }}</p>
        </div>

        <div class="bg-gradient-to-br from-blue-500 to-cyan-600 rounded-lg shadow-lg p-6 text-white">
            <p class="text-sm opacity-90">Lezioni Completate</p>
            <p class="text-3xl font-bold mt-2">{{ $totaleLezioni }}</p>
        </div>

        <div class="bg-gradient-to-br from-green-500 to-emerald-600 rounded-lg shadow-lg p-6 text-white">
            <p class="text-sm opacity-90">Ore Totali</p>
            <p class="text-3xl font-bold mt-2">{{ number_format($totaleOre, 1) }}</p>
        </div>
    </div>

    <!-- Tabella Dettaglio Lezioni -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Dettaglio Lezioni</h3>
        </div>
        <div class="overflow-x-auto">
            @if($lezioni->count() > 0)
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Data</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Programma</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sede</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Durata</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Presenze</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Compenso</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($lezioni as $lezione)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $lezione->data->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            {{ $lezione->programma->nome ?? 'N/D' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ $lezione->sede->nome ?? 'N/D' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $lezione->durata_minuti }} min
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $lezione->presenze ?? 0 }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                            € {{ number_format($lezione->compenso, 2, ',', '.') }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50">
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-right text-sm font-semibold text-gray-900">
                            Totale:
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">
                            € {{ number_format($compensoTotale, 2, ',', '.') }}
                        </td>
                    </tr>
                </tfoot>
            </table>
            @else
            <div class="px-6 py-12 text-center">
                <i class="fas fa-calendar-times text-6xl text-gray-300 mb-4"></i>
                <p class="text-gray-500">Nessuna lezione completata in questo periodo</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Pulsante Indietro -->
    <div>
        <a href="{{ route('professionista.compensi.index') }}"
           class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-lg transition">
            <i class="fas fa-arrow-left mr-2"></i>
            Torna ai Compensi
        </a>
    </div>
</div>
@endsection
