@extends('layouts.professionista')

@section('titolo', 'Dashboard Professionista')
@section('sottotitolo', 'Benvenuto ' . auth()->user()->nome . '! Ecco il riepilogo della tua attività')

@section('content')

<!-- Statistiche Principali -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">

    <!-- Compenso Totale -->
    <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-green-100 text-sm font-medium mb-1">Compenso Totale</p>
                <p class="text-3xl font-bold">€ {{ number_format($compensoTotale, 2, ',', '.') }}</p>
                <p class="text-green-100 text-xs mt-2">
                    <i class="fas fa-info-circle mr-1"></i>
                    Guadagno maturato totale
                </p>
            </div>
            <div class="bg-white bg-opacity-20 rounded-full p-4">
                <i class="fas fa-euro-sign text-3xl"></i>
            </div>
        </div>
    </div>

    <!-- Compenso Mese Corrente -->
    <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-blue-100 text-sm font-medium mb-1">Compenso Mese</p>
                <p class="text-3xl font-bold">€ {{ number_format($compensoMese, 2, ',', '.') }}</p>
                <p class="text-blue-100 text-xs mt-2">
                    <i class="fas fa-calendar-alt mr-1"></i>
                    {{ now()->format('F Y') }}
                </p>
            </div>
            <div class="bg-white bg-opacity-20 rounded-full p-4">
                <i class="fas fa-money-bill-wave text-3xl"></i>
            </div>
        </div>
    </div>

    <!-- Numero Lezioni Mese -->
    <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-purple-100 text-sm font-medium mb-1">Lezioni Mese</p>
                <p class="text-3xl font-bold">{{ $lezioniMese }}</p>
                <p class="text-purple-100 text-xs mt-2">
                    <i class="fas fa-chart-line mr-1"></i>
                    Totale: {{ $totaleLezioni }}
                </p>
            </div>
            <div class="bg-white bg-opacity-20 rounded-full p-4">
                <i class="fas fa-chalkboard-teacher text-3xl"></i>
            </div>
        </div>
    </div>

    <!-- Sedi di Lavoro -->
    <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl shadow-lg p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-orange-100 text-sm font-medium mb-1">Sedi di Lavoro</p>
                <p class="text-3xl font-bold">{{ $totaleSedi }}</p>
                <p class="text-orange-100 text-xs mt-2">
                    <i class="fas fa-map-marker-alt mr-1"></i>
                    Locations attive
                </p>
            </div>
            <div class="bg-white bg-opacity-20 rounded-full p-4">
                <i class="fas fa-building text-3xl"></i>
            </div>
        </div>
    </div>

</div>

<!-- Statistiche Lezioni -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">

    <!-- Lezioni Oggi -->
    <div class="bg-white rounded-xl shadow-md p-6">
        <div class="flex items-center">
            <div class="bg-indigo-100 rounded-full p-3">
                <i class="fas fa-calendar-day text-indigo-600 text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-gray-600 text-sm">Lezioni Oggi</p>
                <p class="text-2xl font-bold text-gray-800">{{ $lezioniOggi }}</p>
            </div>
        </div>
    </div>

    <!-- Lezioni Settimana -->
    <div class="bg-white rounded-xl shadow-md p-6">
        <div class="flex items-center">
            <div class="bg-teal-100 rounded-full p-3">
                <i class="fas fa-calendar-week text-teal-600 text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-gray-600 text-sm">Lezioni Settimana</p>
                <p class="text-2xl font-bold text-gray-800">{{ $lezioniSettimana }}</p>
            </div>
        </div>
    </div>

    <!-- Media Partecipanti -->
    <div class="bg-white rounded-xl shadow-md p-6">
        <div class="flex items-center">
            <div class="bg-pink-100 rounded-full p-3">
                <i class="fas fa-users text-pink-600 text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-gray-600 text-sm">Media Partecipanti</p>
                <p class="text-2xl font-bold text-gray-800">{{ $mediaPartecipanti }}</p>
            </div>
        </div>
    </div>

</div>

<!-- Contenuti Principali -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    <!-- Colonna Sinistra (2/3) -->
    <div class="lg:col-span-2 space-y-6">

        <!-- Prossime Lezioni -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <div class="bg-gradient-to-r from-violet-600 to-purple-600 p-4 text-white">
                <h3 class="text-lg font-semibold flex items-center">
                    <i class="fas fa-calendar-alt mr-2"></i>
                    Prossime Lezioni (7 giorni)
                </h3>
            </div>
            <div class="p-6">
                @if($prossimeLezioni->count() > 0)
                <div class="space-y-4">
                    @foreach($prossimeLezioni as $lezione)
                    <div class="flex items-start border-l-4 @if($lezione->data->isToday()) border-green-500 bg-green-50 @else border-gray-300 @endif rounded-r-lg p-4 hover:shadow-md transition">
                        <div class="flex-shrink-0">
                            <div class="bg-violet-100 text-violet-700 rounded-lg p-3 text-center">
                                <div class="text-2xl font-bold">{{ $lezione->data->format('d') }}</div>
                                <div class="text-xs uppercase">{{ $lezione->data->format('M') }}</div>
                            </div>
                        </div>
                        <div class="ml-4 flex-1">
                            <div class="flex items-start justify-between">
                                <div>
                                    <h4 class="font-semibold text-gray-800">{{ $lezione->programma->nome ?? 'Programma' }}</h4>
                                    <p class="text-sm text-gray-600 mt-1">
                                        <i class="fas fa-clock mr-1"></i>
                                        {{ \Carbon\Carbon::parse($lezione->ora_inizio)->format('H:i') }} -
                                        {{ \Carbon\Carbon::parse($lezione->ora_fine)->format('H:i') }}
                                    </p>
                                    <p class="text-sm text-gray-600 mt-1">
                                        <i class="fas fa-map-marker-alt mr-1"></i>
                                        {{ $lezione->sede->nome ?? 'Sede non specificata' }}
                                    </p>
                                </div>
                                <div class="text-right">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        <i class="fas fa-users mr-1"></i>
                                        {{ $lezione->numero_partecipanti ?? 0 }} partecipanti
                                    </span>
                                </div>
                            </div>
                            <div class="mt-2">
                                <a href="{{ route('professionista.lezioni.show', $lezione->id) }}" class="text-violet-600 hover:text-violet-800 text-sm font-medium">
                                    Visualizza dettagli <i class="fas fa-arrow-right ml-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-calendar-times text-4xl mb-3"></i>
                    <p>Nessuna lezione programmata nei prossimi 7 giorni</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Grafici -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <!-- Grafico Lezioni -->
            <div class="bg-white rounded-xl shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-chart-bar mr-2 text-purple-600"></i>
                    Lezioni (ultimi 6 mesi)
                </h3>
                <canvas id="lezioniChart" height="200"></canvas>
            </div>

            <!-- Grafico Compensi -->
            <div class="bg-white rounded-xl shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-chart-line mr-2 text-green-600"></i>
                    Compensi (ultimi 6 mesi)
                </h3>
                <canvas id="compensiChart" height="200"></canvas>
            </div>

        </div>

    </div>

    <!-- Colonna Destra (1/3) -->
    <div class="space-y-6">

        <!-- Sedi di Lavoro -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <div class="bg-gradient-to-r from-orange-500 to-orange-600 p-4 text-white">
                <h3 class="text-lg font-semibold flex items-center">
                    <i class="fas fa-map-marker-alt mr-2"></i>
                    Sedi di Lavoro
                </h3>
            </div>
            <div class="p-4">
                @if($sedi->count() > 0)
                <div class="space-y-3">
                    @foreach($sedi as $sede)
                    <div class="flex items-start p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                        <div class="flex-shrink-0 bg-orange-100 text-orange-600 rounded-lg p-2">
                            <i class="fas fa-building"></i>
                        </div>
                        <div class="ml-3">
                            <p class="font-medium text-gray-800">{{ $sede->nome }}</p>
                            <p class="text-xs text-gray-600 mt-1">
                                <i class="fas fa-map-pin mr-1"></i>
                                {{ $sede->citta }}
                            </p>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-6 text-gray-500">
                    <i class="fas fa-building text-3xl mb-2"></i>
                    <p class="text-sm">Nessuna sede assegnata</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Lezioni Recenti -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <div class="bg-gradient-to-r from-gray-600 to-gray-700 p-4 text-white">
                <h3 class="text-lg font-semibold flex items-center">
                    <i class="fas fa-history mr-2"></i>
                    Ultime Lezioni
                </h3>
            </div>
            <div class="p-4">
                @if($lezioniRecenti->count() > 0)
                <div class="space-y-3">
                    @foreach($lezioniRecenti as $lezione)
                    <div class="flex items-start p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                        <div class="flex-shrink-0 bg-gray-200 text-gray-700 rounded-lg p-2 text-center min-w-12">
                            <div class="text-sm font-bold">{{ $lezione->data->format('d') }}</div>
                            <div class="text-xs">{{ $lezione->data->format('M') }}</div>
                        </div>
                        <div class="ml-3 flex-1">
                            <p class="font-medium text-gray-800 text-sm">{{ $lezione->programma->nome ?? 'Programma' }}</p>
                            <p class="text-xs text-gray-600 mt-1">
                                <i class="fas fa-users mr-1"></i>
                                {{ $lezione->numero_presenze ?? 0 }} presenze
                            </p>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-6 text-gray-500">
                    <i class="fas fa-history text-3xl mb-2"></i>
                    <p class="text-sm">Nessuna lezione recente</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Info Tariffa -->
        <div class="bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-xl shadow-md p-6 text-white">
            <div class="flex items-center mb-3">
                <i class="fas fa-money-check-alt text-2xl mr-3"></i>
                <h3 class="text-lg font-semibold">Tariffa Oraria</h3>
            </div>
            <p class="text-3xl font-bold">€ {{ number_format($tariffaOraria, 2, ',', '.') }}</p>
            <p class="text-indigo-100 text-sm mt-2">
                Il tuo compenso viene calcolato moltiplicando le ore di lezione per questa tariffa
            </p>
        </div>

    </div>

</div>

@endsection

@push('scripts')
<script>
// Grafico Lezioni
const lezioniCtx = document.getElementById('lezioniChart').getContext('2d');
const lezioniChart = new Chart(lezioniCtx, {
    type: 'bar',
    data: {
        labels: {!! json_encode($lezioniMesi->pluck('mese')) !!},
        datasets: [{
            label: 'Lezioni',
            data: {!! json_encode($lezioniMesi->pluck('totale')) !!},
            backgroundColor: 'rgba(139, 92, 246, 0.8)',
            borderColor: 'rgba(139, 92, 246, 1)',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    precision: 0
                }
            }
        }
    }
});

// Grafico Compensi
const compensiCtx = document.getElementById('compensiChart').getContext('2d');
const compensiChart = new Chart(compensiCtx, {
    type: 'line',
    data: {
        labels: {!! json_encode($compensiMesi->pluck('mese')) !!},
        datasets: [{
            label: 'Compensi (€)',
            data: {!! json_encode($compensiMesi->pluck('totale')) !!},
            backgroundColor: 'rgba(34, 197, 94, 0.1)',
            borderColor: 'rgba(34, 197, 94, 1)',
            borderWidth: 2,
            fill: true,
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return '€' + value.toFixed(0);
                    }
                }
            }
        }
    }
});
</script>
@endpush
