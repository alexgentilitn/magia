@extends('layouts.admin')

@section('title', 'Report e Statistiche')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 flex items-center">
                <i class="fas fa-chart-line text-fucsia-magia mr-3"></i>
                Report e Statistiche
            </h1>
            <p class="text-gray-600 mt-1">Analisi performance e utilizzo del centro</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.report.presenze', ['data_inizio' => $dataInizio, 'data_fine' => $dataFine]) }}"
               class="px-4 py-2 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition-colors text-sm font-medium">
                <i class="fas fa-users mr-2"></i>
                Report Presenze
            </a>
            <a href="{{ route('admin.report.professionisti', ['data_inizio' => $dataInizio, 'data_fine' => $dataFine]) }}"
               class="px-4 py-2 bg-purple-100 text-purple-700 rounded-lg hover:bg-purple-200 transition-colors text-sm font-medium">
                <i class="fas fa-user-tie mr-2"></i>
                Report Professionisti
            </a>
        </div>
    </div>

    <!-- Filtri Periodo -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
        <form method="GET" action="{{ route('admin.report.index') }}" class="flex flex-col sm:flex-row gap-4 items-end">
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-1">Data Inizio</label>
                <input type="date" name="data_inizio" value="{{ $dataInizio }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent">
            </div>
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-1">Data Fine</label>
                <input type="date" name="data_fine" value="{{ $dataFine }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent">
            </div>
            <button type="submit"
                    class="px-6 py-2 bg-gradient-to-r from-viola-magia to-fucsia-magia text-white rounded-lg hover:shadow-lg transition-all font-medium">
                <i class="fas fa-filter mr-2"></i>
                Applica Filtri
            </button>
            <a href="{{ route('admin.report.export-csv', ['tipo' => 'generale', 'data_inizio' => $dataInizio, 'data_fine' => $dataFine]) }}"
               class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-medium">
                <i class="fas fa-file-csv mr-2"></i>
                Export CSV
            </a>
        </form>
    </div>

    <!-- Statistiche Generali -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <!-- Totale Lezioni -->
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium">Totale Lezioni</p>
                    <p class="text-3xl font-bold mt-2">{{ $statistiche['totale_lezioni'] }}</p>
                    <p class="text-blue-100 text-xs mt-1">{{ $statistiche['lezioni_completate'] }} completate</p>
                </div>
                <div class="bg-blue-400 bg-opacity-30 rounded-full p-4">
                    <i class="fas fa-calendar-alt text-3xl"></i>
                </div>
            </div>
        </div>

        <!-- Totale Partecipanti -->
        <div class="bg-gradient-to-br from-fucsia-magia to-pink-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-pink-100 text-sm font-medium">Totale Partecipanti</p>
                    <p class="text-3xl font-bold mt-2">{{ $statistiche['totale_partecipanti'] }}</p>
                    <p class="text-pink-100 text-xs mt-1">Prenotazioni totali</p>
                </div>
                <div class="bg-pink-400 bg-opacity-30 rounded-full p-4">
                    <i class="fas fa-users text-3xl"></i>
                </div>
            </div>
        </div>

        <!-- Tasso Occupazione -->
        <div class="bg-gradient-to-br from-viola-magia to-purple-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm font-medium">Tasso Occupazione</p>
                    <p class="text-3xl font-bold mt-2">{{ $statistiche['tasso_occupazione'] }}%</p>
                    <p class="text-purple-100 text-xs mt-1">{{ $statistiche['posti_occupati'] }}/{{ $statistiche['posti_totali'] }} posti</p>
                </div>
                <div class="bg-purple-400 bg-opacity-30 rounded-full p-4">
                    <i class="fas fa-chart-pie text-3xl"></i>
                </div>
            </div>
        </div>

        <!-- Tasso Presenza -->
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm font-medium">Tasso Presenza</p>
                    <p class="text-3xl font-bold mt-2">{{ $statistichePresenze['tasso_presenza'] }}%</p>
                    <p class="text-green-100 text-xs mt-1">{{ $statistichePresenze['presenti'] }} presenti / {{ $statistichePresenze['assenti'] }} assenti</p>
                </div>
                <div class="bg-green-400 bg-opacity-30 rounded-full p-4">
                    <i class="fas fa-check-circle text-3xl"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Grafico Lezioni per Stato -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">
                <i class="fas fa-chart-pie text-fucsia-magia mr-2"></i>
                Lezioni per Stato
            </h3>
            <canvas id="chartStato" height="250"></canvas>
        </div>

        <!-- Grafico Presenze vs Assenze -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">
                <i class="fas fa-user-check text-fucsia-magia mr-2"></i>
                Presenze vs Assenze
            </h3>
            <canvas id="chartPresenze" height="250"></canvas>
        </div>
    </div>

    <!-- Trend Giornaliero -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4">
            <i class="fas fa-chart-line text-fucsia-magia mr-2"></i>
            Trend Partecipazioni nel Periodo
        </h3>
        <canvas id="chartTrend" height="80"></canvas>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Top Professionisti -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">
                <i class="fas fa-trophy text-fucsia-magia mr-2"></i>
                Top Professionisti
            </h3>
            <div class="space-y-3">
                @forelse($topProfessionisti as $index => $prof)
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div class="flex items-center">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-sm font-bold
                            @if($index === 0) bg-yellow-500
                            @elseif($index === 1) bg-gray-400
                            @elseif($index === 2) bg-orange-600
                            @else bg-gray-300
                            @endif">
                            {{ $index + 1 }}
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-semibold text-gray-900">
                                {{ $prof->professionista_nome }} {{ $prof->professionista_cognome }}
                            </p>
                            <p class="text-xs text-gray-500">{{ $prof->totale_lezioni }} lezioni</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-semibold text-fucsia-magia">{{ $prof->totale_partecipanti }} partecipanti</p>
                        <p class="text-xs text-gray-500">Media: {{ round($prof->media_partecipanti, 1) }}</p>
                    </div>
                </div>
                @empty
                <p class="text-center text-gray-500 py-4">Nessun dato disponibile</p>
                @endforelse
            </div>
        </div>

        <!-- Top Clienti Attivi -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">
                <i class="fas fa-star text-fucsia-magia mr-2"></i>
                Clienti Più Attivi
            </h3>
            <div class="space-y-2">
                @forelse($topClienti as $index => $cliente)
                <div class="flex items-center justify-between p-2 hover:bg-gray-50 rounded">
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-fucsia-magia rounded-full flex items-center justify-center text-white text-xs font-bold">
                            {{ substr($cliente->nome, 0, 1) }}{{ substr($cliente->cognome, 0, 1) }}
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-900">{{ $cliente->nome }} {{ $cliente->cognome }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-xs text-green-600 bg-green-50 px-2 py-1 rounded">
                            <i class="fas fa-check mr-1"></i>{{ $cliente->presenze }}
                        </span>
                        <span class="text-sm font-semibold text-fucsia-magia">{{ $cliente->totale_partecipazioni }} lezioni</span>
                    </div>
                </div>
                @empty
                <p class="text-center text-gray-500 py-4">Nessun dato disponibile</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Grafico Lezioni per Stato
    const ctxStato = document.getElementById('chartStato').getContext('2d');
    new Chart(ctxStato, {
        type: 'doughnut',
        data: {
            labels: [@foreach($lezioniPerStato as $stato => $count)'{{ ucfirst($stato) }}',@endforeach],
            datasets: [{
                data: [@foreach($lezioniPerStato as $stato => $count){{ $count }},@endforeach],
                backgroundColor: [
                    '#3b82f6', // programmata
                    '#10b981', // confermata
                    '#f59e0b', // in_corso
                    '#6b7280', // completata
                    '#ef4444', // cancellata
                    '#f97316', // rinviata
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // Grafico Presenze vs Assenze
    const ctxPresenze = document.getElementById('chartPresenze').getContext('2d');
    new Chart(ctxPresenze, {
        type: 'pie',
        data: {
            labels: ['Presenti', 'Assenti', 'Prenotati'],
            datasets: [{
                data: [
                    {{ $statistichePresenze['presenti'] }},
                    {{ $statistichePresenze['assenti'] }},
                    {{ $statistichePresenze['prenotati'] }}
                ],
                backgroundColor: ['#10b981', '#ef4444', '#3b82f6']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // Grafico Trend Giornaliero
    const ctxTrend = document.getElementById('chartTrend').getContext('2d');
    new Chart(ctxTrend, {
        type: 'line',
        data: {
            labels: [@foreach($trendGiornaliero as $trend)'{{ \Carbon\Carbon::parse($trend->data)->format("d/m") }}',@endforeach],
            datasets: [
                {
                    label: 'Lezioni',
                    data: [@foreach($trendGiornaliero as $trend){{ $trend->totale_lezioni }},@endforeach],
                    borderColor: '#9c27b0',
                    backgroundColor: 'rgba(156, 39, 176, 0.1)',
                    tension: 0.4
                },
                {
                    label: 'Partecipanti',
                    data: [@foreach($trendGiornaliero as $trend){{ $trend->totale_partecipanti }},@endforeach],
                    borderColor: '#e91e63',
                    backgroundColor: 'rgba(233, 30, 99, 0.1)',
                    tension: 0.4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            },
            plugins: {
                legend: {
                    position: 'top'
                }
            }
        }
    });
});
</script>
@endsection
