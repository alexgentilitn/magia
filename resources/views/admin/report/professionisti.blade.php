@extends('layouts.admin')

@section('titolo', 'Report Professionisti')

@section('contenuto')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 flex items-center">
                <i class="fas fa-user-tie text-fucsia-magia mr-3"></i>
                Report Performance Professionisti
            </h1>
            <p class="text-gray-600 mt-1">Analisi attività e performance istruttori</p>
        </div>
        <a href="{{ route('admin.report.index', ['data_inizio' => $dataInizio, 'data_fine' => $dataFine]) }}"
           class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>
            Torna ai Report
        </a>
    </div>

    <!-- Export Actions -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6 flex justify-between items-center">
        <div class="text-gray-600">
            Periodo: <strong>{{ \Carbon\Carbon::parse($dataInizio)->format('d/m/Y') }}</strong> -
            <strong>{{ \Carbon\Carbon::parse($dataFine)->format('d/m/Y') }}</strong>
        </div>
        <a href="{{ route('admin.report.export-csv', ['tipo' => 'professionisti', 'data_inizio' => $dataInizio, 'data_fine' => $dataFine]) }}"
           class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-medium">
            <i class="fas fa-file-csv mr-2"></i>
            Export CSV
        </a>
    </div>

    <!-- Tabella Performance -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Professionista
                    </th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Totale Lezioni
                    </th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Completate
                    </th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Cancellate
                    </th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Posti Occupati
                    </th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Tasso Occupazione
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($performance as $perf)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-r from-viola-magia to-fucsia-magia flex items-center justify-center text-white font-bold">
                                {{ substr($perf->professionista_nome ?? 'N', 0, 1) }}{{ substr($perf->professionista_cognome ?? 'D', 0, 1) }}
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $perf->professionista_nome }} {{ $perf->professionista_cognome }}
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center">
                        <span class="text-lg font-bold text-fucsia-magia">{{ $perf->totale_lezioni }}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center">
                        <span class="text-sm text-green-600 font-medium">{{ $perf->lezioni_completate }}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center">
                        <span class="text-sm text-red-600 font-medium">{{ $perf->lezioni_cancellate }}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center">
                        <div class="text-sm text-gray-900">
                            <span class="font-semibold">{{ $perf->posti_occupati }}</span> / {{ $perf->posti_totali }}
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center">
                        <div class="flex items-center justify-center">
                            <div class="w-24 bg-gray-200 rounded-full h-2 mr-2">
                                <div class="bg-gradient-to-r from-viola-magia to-fucsia-magia h-2 rounded-full"
                                     style="width: {{ $perf->tasso_occupazione }}%"></div>
                            </div>
                            <span class="text-sm font-semibold text-gray-700">{{ $perf->tasso_occupazione }}%</span>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                        <i class="fas fa-inbox text-4xl mb-2 opacity-50"></i>
                        <p>Nessun dato disponibile nel periodo selezionato</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Statistiche Riepilogative -->
    @if($performance->count() > 0)
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
        <div class="bg-blue-50 rounded-lg p-6 border-l-4 border-blue-500">
            <div class="flex items-center">
                <i class="fas fa-calendar-alt text-blue-500 text-2xl mr-4"></i>
                <div>
                    <p class="text-sm text-blue-700 font-medium">Totale Lezioni</p>
                    <p class="text-2xl font-bold text-blue-900">{{ $performance->sum('totale_lezioni') }}</p>
                </div>
            </div>
        </div>

        <div class="bg-green-50 rounded-lg p-6 border-l-4 border-green-500">
            <div class="flex items-center">
                <i class="fas fa-check-circle text-green-500 text-2xl mr-4"></i>
                <div>
                    <p class="text-sm text-green-700 font-medium">Lezioni Completate</p>
                    <p class="text-2xl font-bold text-green-900">{{ $performance->sum('lezioni_completate') }}</p>
                </div>
            </div>
        </div>

        <div class="bg-purple-50 rounded-lg p-6 border-l-4 border-purple-500">
            <div class="flex items-center">
                <i class="fas fa-users text-purple-500 text-2xl mr-4"></i>
                <div>
                    <p class="text-sm text-purple-700 font-medium">Totale Partecipanti</p>
                    <p class="text-2xl font-bold text-purple-900">{{ $performance->sum('posti_occupati') }}</p>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
