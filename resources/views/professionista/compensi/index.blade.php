@extends('layouts.professionista')

@section('titolo', 'Compensi')
@section('sottotitolo', 'Visualizza i tuoi compensi e statistiche')

@section('content')
<div class="space-y-6">

    <!-- Stats Principali -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-gradient-to-br from-violet-500 to-purple-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90">Compenso Totale</p>
                    <p class="text-3xl font-bold mt-2">€ {{ number_format($compensoTotale, 2, ',', '.') }}</p>
                    <p class="text-xs opacity-75 mt-2">{{ $totaleLezioniCompletate }} lezioni completate</p>
                </div>
                <div class="text-5xl opacity-20">
                    <i class="fas fa-euro-sign"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-green-500 to-emerald-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90">Mese Corrente</p>
                    <p class="text-3xl font-bold mt-2">€ {{ number_format($compensoMeseCorrente, 2, ',', '.') }}</p>
                    <p class="text-xs opacity-75 mt-2">{{ $lezioniMeseCorrente }} lezioni</p>
                </div>
                <div class="text-5xl opacity-20">
                    <i class="fas fa-calendar-check"></i>
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
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Compenso</th>
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
                datasets: [{
                    label: 'Compensi (€)',
                    data: data.map(d => d.totale),
                    backgroundColor: 'rgba(123, 40, 105, 0.8)',
                    borderColor: 'rgba(123, 40, 105, 1)',
                    borderWidth: 1
                }]
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
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'Compenso: € ' + context.parsed.y.toLocaleString('it-IT', {minimumFractionDigits: 2});
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
