@extends('layouts.admin')

@section('titolo', 'Dashboard')

@section('contenuto')
<div class="p-6">
    
    <!-- Statistiche Rapide -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        
        <!-- Card: Totale Clienti -->
        <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-viola-magia transform hover:scale-105 transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium uppercase">Totale Clienti</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ $totaleClienti }}</p>
                </div>
                <div class="bg-viola-magia bg-opacity-10 rounded-full p-4">
                    <i class="fas fa-users text-viola-magia text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Card: Clienti Attivi -->
        <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-green-500 transform hover:scale-105 transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium uppercase">Clienti Attivi</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ $clientiAttivi }}</p>
                </div>
                <div class="bg-green-500 bg-opacity-10 rounded-full p-4">
                    <i class="fas fa-check-circle text-green-500 text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Card: Nuovi Questo Mese -->
        <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-blue-500 transform hover:scale-105 transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium uppercase">Nuovi Questo Mese</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ $nuoviMese }}</p>
                </div>
                <div class="bg-blue-500 bg-opacity-10 rounded-full p-4">
                    <i class="fas fa-user-plus text-blue-500 text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Card: Certificati in Scadenza -->
        <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-red-500 transform hover:scale-105 transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium uppercase">Certificati Scadenza</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ $certificatiScadenza }}</p>
                </div>
                <div class="bg-red-500 bg-opacity-10 rounded-full p-4">
                    <i class="fas fa-exclamation-triangle text-red-500 text-2xl"></i>
                </div>
            </div>
        </div>

    </div>

    <!-- Grafici -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        
        <!-- Grafico: Iscrizioni ultimi 12 mesi -->
        <div class="bg-white rounded-xl shadow-md p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-chart-line text-fucsia-magia mr-2"></i>
                Iscrizioni Ultimi 12 Mesi
            </h3>
            <div class="h-64">
                <canvas id="graficoIscrizioni"></canvas>
            </div>
        </div>

        <!-- Grafico: Clienti per Programma -->
        <div class="bg-white rounded-xl shadow-md p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-chart-pie text-fucsia-magia mr-2"></i>
                Clienti per Programma
            </h3>
            <div class="h-64 flex items-center justify-center">
                <canvas id="graficoProgrammi"></canvas>
            </div>
        </div>

    </div>

    <!-- Alert Certificati in Scadenza -->
    @if($certificatiScadenza > 0)
    <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-r-lg">
        <div class="flex items-center">
            <i class="fas fa-exclamation-triangle text-red-500 text-xl mr-3"></i>
            <div>
                <p class="text-red-800 font-bold">Attenzione: {{ $certificatiScadenza }} certificati medici in scadenza!</p>
                <p class="text-red-700 text-sm mt-1">Verifica i certificati nella sezione clienti</p>
            </div>
        </div>
    </div>
    @endif

    <!-- Ultimi Clienti Registrati -->
    <div class="bg-white rounded-xl shadow-md p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-gray-800 flex items-center">
                <i class="fas fa-clock text-fucsia-magia mr-2"></i>
                Ultimi Clienti Registrati
            </h3>
            <a href="{{ route('admin.clienti.index') }}" class="text-fucsia-magia hover:text-viola-magia font-medium text-sm">
                Vedi tutti <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="border-b border-gray-200">
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">Nome</th>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">Email</th>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">Programma</th>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">Data Iscrizione</th>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">Stato</th>
                        <th class="text-right py-3 px-4 text-sm font-semibold text-gray-700">Azioni</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ultimiClienti as $cliente)
                    <tr class="border-b border-gray-100 hover:bg-gray-50 transition">
                        <td class="py-3 px-4">
                            <span class="font-medium text-gray-800">{{ $cliente->nome }} {{ $cliente->cognome }}</span>
                        </td>
                        <td class="py-3 px-4 text-sm text-gray-600">{{ $cliente->email }}</td>
                        <td class="py-3 px-4">
                            <span class="px-2 py-1 bg-purple-100 text-purple-800 rounded-full text-xs font-medium">
                                {{ $cliente->programma_attuale ?? 'Nessuno' }}
                            </span>
                        </td>
                        <td class="py-3 px-4 text-sm text-gray-600">
                            {{ $cliente->created_at->format('d/m/Y') }}
                        </td>
                        <td class="py-3 px-4">
                            @if($cliente->stato_cliente === 'attivo')
                                <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">
                                    <i class="fas fa-check-circle mr-1"></i>Attivo
                                </span>
                            @else
                                <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-xs font-medium">
                                    <i class="fas fa-clock mr-1"></i>{{ ucfirst($cliente->stato_cliente) }}
                                </span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-right">
                            <a href="{{ route('admin.clienti.show', $cliente->id) }}" 
                               class="text-fucsia-magia hover:text-viola-magia">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-8 text-center text-gray-500">
                            <i class="fas fa-inbox text-4xl mb-2"></i>
                            <p>Nessun cliente registrato</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Grafico Iscrizioni
const ctxIscrizioni = document.getElementById('graficoIscrizioni');
if (ctxIscrizioni) {
    new Chart(ctxIscrizioni, {
        type: 'line',
        data: {
            labels: {!! json_encode($iscrizioniMesi->pluck('mese')) !!},
            datasets: [{
                label: 'Nuove Iscrizioni',
                data: {!! json_encode($iscrizioniMesi->pluck('totale')) !!},
                borderColor: '#E91E8C',
                backgroundColor: 'rgba(233, 30, 140, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
}

// Grafico Programmi
const ctxProgrammi = document.getElementById('graficoProgrammi');
if (ctxProgrammi) {
    new Chart(ctxProgrammi, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($clientiProgramma->pluck('programma')) !!},
            datasets: [{
                data: {!! json_encode($clientiProgramma->pluck('totale')) !!},
                backgroundColor: [
                    '#7B2869',
                    '#E91E8C',
                    '#FF6B35',
                    '#4ECDC4',
                    '#95E1D3'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
}
</script>
@endpush
@endsection
