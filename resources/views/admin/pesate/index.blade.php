@extends('layouts.admin')

@section('titolo', 'Pesate Cliente')

@section('contenuto')
<div class="p-6">

    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <a href="{{ route('admin.clienti.show', $cliente->id) }}" class="text-fucsia-magia hover:text-viola-magia mb-2 inline-block">
                <i class="fas fa-arrow-left mr-2"></i> Torna al cliente
            </a>
            <h2 class="text-2xl font-bold text-gray-800">Pesate di {{ $cliente->nome }} {{ $cliente->cognome }}</h2>
        </div>
        <a href="{{ route('admin.clienti.pesate.create', $cliente->id) }}"
           class="px-6 py-3 bg-gradient-to-r from-viola-magia to-fucsia-magia text-white font-semibold rounded-lg hover:shadow-lg transition">
            <i class="fas fa-plus mr-2"></i> Nuova Pesata
        </a>
    </div>

    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6" role="alert">
        <div class="flex items-center">
            <i class="fas fa-check-circle mr-2"></i>
            <p class="font-medium">{{ session('success') }}</p>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6" role="alert">
        <div class="flex items-center">
            <i class="fas fa-exclamation-circle mr-2"></i>
            <p class="font-medium">{{ session('error') }}</p>
        </div>
    </div>
    @endif

    <!-- Statistiche Rapide -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-viola-magia">
            <p class="text-sm text-gray-600 font-medium">Totale Pesate</p>
            <p class="text-2xl font-bold text-gray-800">{{ $statistiche['totale_pesate'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-blue-500">
            <p class="text-sm text-gray-600 font-medium">Peso Iniziale</p>
            <p class="text-2xl font-bold text-gray-800">
                @if($statistiche['peso_iniziale'])
                    {{ number_format($statistiche['peso_iniziale'], 2, ',', '.') }} kg
                @else
                    N/D
                @endif
            </p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-green-500">
            <p class="text-sm text-gray-600 font-medium">Peso Attuale</p>
            <p class="text-2xl font-bold text-gray-800">
                @if($statistiche['peso_attuale'])
                    {{ number_format($statistiche['peso_attuale'], 2, ',', '.') }} kg
                @else
                    N/D
                @endif
            </p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-orange-500">
            <p class="text-sm text-gray-600 font-medium">Variazione</p>
            <p class="text-2xl font-bold {{ ($statistiche['variazione_peso'] ?? 0) < 0 ? 'text-green-600' : 'text-red-600' }}">
                @if($statistiche['variazione_peso'])
                    {{ $statistiche['variazione_peso'] > 0 ? '+' : '' }}{{ number_format($statistiche['variazione_peso'], 2, ',', '.') }} kg
                @else
                    N/D
                @endif
            </p>
        </div>
    </div>

    @if($pesate->isNotEmpty())
    <!-- Grafici -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">
                <i class="fas fa-weight text-fucsia-magia mr-2"></i> Andamento Peso
            </h3>
            <canvas id="graficoPeso" height="200"></canvas>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">
                <i class="fas fa-chart-line text-fucsia-magia mr-2"></i> BMI
            </h3>
            <canvas id="graficoBmi" height="200"></canvas>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">
                <i class="fas fa-fire text-fucsia-magia mr-2"></i> Grasso Corporeo
            </h3>
            <canvas id="graficoGrasso" height="200"></canvas>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">
                <i class="fas fa-tint text-fucsia-magia mr-2"></i> Acqua Corporea
            </h3>
            <canvas id="graficoAcqua" height="200"></canvas>
        </div>
    </div>
    @endif

    <!-- Tabella Pesate -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Storico Pesate</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Data</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sede</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Peso</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">BMI</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Grasso</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Muscolo</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Azioni</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($pesate as $pesata)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $pesata->data_rilevazione->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            {{ $pesata->sede ?? 'N/D' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ number_format($pesata->peso, 2, ',', '.') }} kg
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm {{ $pesata->getColoreBmi() }}">
                            {{ number_format($pesata->bmi, 1, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ number_format($pesata->grasso_corporeo ?? 0, 1, ',', '.') }}%
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ number_format($pesata->muscolo_scheletrico ?? 0, 1, ',', '.') }}%
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                            <a href="{{ route('admin.clienti.pesate.edit', [$cliente->id, $pesata->id]) }}"
                               class="text-fucsia-magia hover:text-viola-magia mr-3">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form method="POST"
                                  action="{{ route('admin.clienti.pesate.destroy', [$cliente->id, $pesata->id]) }}"
                                  class="inline"
                                  onsubmit="return confirm('Eliminare questa pesata?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                            <i class="fas fa-weight text-4xl mb-2 text-gray-300"></i>
                            <p>Nessuna pesata registrata</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

@if($pesate->isNotEmpty())
@push('scripts')
<script>
// Configurazione comune
const chartConfig = {
    responsive: true,
    maintainAspectRatio: true,
    plugins: {
        legend: { display: false }
    },
    scales: {
        y: { beginAtZero: false }
    }
};

// Grafico Peso
new Chart(document.getElementById('graficoPeso'), {
    type: 'line',
    data: {
        labels: @json($grafici['date']),
        datasets: [{
            label: 'Peso (kg)',
            data: @json($grafici['peso']),
            borderColor: 'rgb(219, 39, 119)',
            backgroundColor: 'rgba(219, 39, 119, 0.1)',
            tension: 0.3,
            fill: true
        }]
    },
    options: chartConfig
});

// Grafico BMI
new Chart(document.getElementById('graficoBmi'), {
    type: 'line',
    data: {
        labels: @json($grafici['date']),
        datasets: [{
            label: 'BMI',
            data: @json($grafici['bmi']),
            borderColor: 'rgb(59, 130, 246)',
            backgroundColor: 'rgba(59, 130, 246, 0.1)',
            tension: 0.3,
            fill: true
        }]
    },
    options: chartConfig
});

// Grafico Grasso
new Chart(document.getElementById('graficoGrasso'), {
    type: 'line',
    data: {
        labels: @json($grafici['date']),
        datasets: [{
            label: 'Grasso Corporeo (%)',
            data: @json($grafici['grasso_corporeo']),
            borderColor: 'rgb(239, 68, 68)',
            backgroundColor: 'rgba(239, 68, 68, 0.1)',
            tension: 0.3,
            fill: true
        }]
    },
    options: chartConfig
});

// Grafico Acqua
new Chart(document.getElementById('graficoAcqua'), {
    type: 'line',
    data: {
        labels: @json($grafici['date']),
        datasets: [{
            label: 'Acqua Corporea (%)',
            data: @json($grafici['acqua_corporea']),
            borderColor: 'rgb(14, 165, 233)',
            backgroundColor: 'rgba(14, 165, 233, 0.1)',
            tension: 0.3,
            fill: true
        }]
    },
    options: chartConfig
});
</script>
@endpush
@endif

@endsection
