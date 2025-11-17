@extends('layouts.professionista')

@section('titolo', 'Compensi')
@section('sottotitolo', 'Visualizza i tuoi compensi e statistiche')

@section('content')
<div class="space-y-6">

    <!-- Stats Principali -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-gradient-to-br from-violet-500 to-purple-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90">Compenso Maturato</p>
                    <p class="text-3xl font-bold mt-2">€ {{ number_format($compensoTotale, 2, ',', '.') }}</p>
                    <p class="text-xs opacity-75 mt-2">{{ $totaleLezioniCompletate }} lezioni</p>
                </div>
                <div class="text-5xl opacity-20">
                    <i class="fas fa-calculator"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-green-500 to-emerald-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90">Totale Pagato</p>
                    <p class="text-3xl font-bold mt-2">€ {{ number_format($totalePagato, 2, ',', '.') }}</p>
                    <p class="text-xs opacity-75 mt-2">{{ $numeroPagamenti }} pagamenti</p>
                </div>
                <div class="text-5xl opacity-20">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-amber-500 to-orange-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90">Da Ricevere</p>
                    <p class="text-3xl font-bold mt-2">€ {{ number_format($compensoDaPagare, 2, ',', '.') }}</p>
                    <p class="text-xs opacity-75 mt-2">Differenza</p>
                </div>
                <div class="text-5xl opacity-20">
                    <i class="fas fa-hourglass-half"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-blue-500 to-cyan-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90">Tariffa Oraria</p>
                    <p class="text-3xl font-bold mt-2">€ {{ number_format($tariffaOraria, 2, ',', '.') }}</p>
                    <p class="text-xs opacity-75 mt-2">{{ number_format($totaleOreInsegnate, 1) }} ore totali</p>
                </div>
                <div class="text-5xl opacity-20">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Ultimi Pagamenti Ricevuti -->
    @if($ultimiPagamenti->count() > 0)
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-900">
                <i class="fas fa-money-bill-wave text-green-500 mr-2"></i>
                Ultimi Pagamenti Ricevuti
            </h3>
            <a href="{{ route('professionista.compensi.storico') }}" class="text-violet-600 hover:text-violet-800 text-sm">
                Vedi tutti <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
        <div class="space-y-3">
            @foreach($ultimiPagamenti as $pagamento)
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                <div class="flex items-center space-x-3">
                    <div class="flex-shrink-0 w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-check text-green-600"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900">
                            {{ $pagamento->data_pagamento->format('d/m/Y') }}
                        </p>
                        <p class="text-xs text-gray-500">
                            Periodo: {{ $pagamento->periodo_da->format('d/m') }} - {{ $pagamento->periodo_a->format('d/m/Y') }}
                        </p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-sm font-bold text-green-600">
                        € {{ number_format($pagamento->importo_netto, 2, ',', '.') }}
                    </p>
                    <p class="text-xs text-gray-500">
                        {{ ucfirst($pagamento->metodo_pagamento) }}
                    </p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Grafico Compensi Mensili -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Andamento Compensi (Ultimi 12 Mesi)</h3>
        <div>
            <canvas id="compensiChart" class="w-full" style="max-height: 300px;"></canvas>
        </div>
    </div>

    <!-- Tabella Dettaglio Mensile -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Dettaglio Mensile</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Mese</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Lezioni</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Maturato</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pagato</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Azioni</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($compensiPerMese as $mese)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $mese['mese'] }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $mese['lezioni'] }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            € {{ number_format($mese['totale'], 2, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            @if($mese['pagato'] > 0)
                                <span class="text-green-600 font-medium">
                                    € {{ number_format($mese['pagato'], 2, ',', '.') }}
                                </span>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <a href="{{ route('professionista.compensi.periodo', [$mese['anno'], $mese['mese_num']]) }}"
                               class="text-violet-600 hover:text-violet-900">
                                <i class="fas fa-eye"></i> Dettagli
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('compensiChart');
    if (ctx) {
        const data = @json($compensiPerMese);

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: data.map(d => d.mese),
                datasets: [
                    {
                        label: 'Compenso Maturato',
                        data: data.map(d => d.totale),
                        backgroundColor: 'rgba(123, 40, 105, 0.7)',
                        borderColor: 'rgba(123, 40, 105, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Compenso Pagato',
                        data: data.map(d => d.pagato || 0),
                        backgroundColor: 'rgba(16, 185, 129, 0.7)',
                        borderColor: 'rgba(16, 185, 129, 1)',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '€ ' + value.toLocaleString('it-IT');
                            }
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': € ' + context.parsed.y.toLocaleString('it-IT', {minimumFractionDigits: 2});
                            }
                        }
                    }
                }
            }
        });
    }
});
</script>
@endpush
