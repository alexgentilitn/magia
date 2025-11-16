@extends('layouts.admin')

@section('titolo', 'Dashboard')

@section('contenuto')
<div class="p-6">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Dashboard</h1>
        <p class="text-gray-600">Panoramica generale del sistema MA.GIA DONNA</p>
    </div>

    <!-- Statistiche Principali -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <!-- Clienti -->
        <div class="bg-gradient-to-br from-fucsia-magia to-viola-magia text-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90">Clienti Totali</p>
                    <p class="text-3xl font-bold mt-2">{{ $totaleClienti }}</p>
                    <p class="text-xs opacity-75 mt-1">{{ $clientiAttivi }} attivi</p>
                </div>
                <div class="text-4xl opacity-80">
                    <i class="fas fa-users"></i>
                </div>
            </div>
        </div>

        <!-- Lezioni -->
        <div class="bg-gradient-to-br from-blue-500 to-blue-700 text-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90">Lezioni Programmate</p>
                    <p class="text-3xl font-bold mt-2">{{ $lezioniFuture }}</p>
                    <p class="text-xs opacity-75 mt-1">{{ $lezioniOggi }} oggi</p>
                </div>
                <div class="text-4xl opacity-80">
                    <i class="fas fa-calendar-alt"></i>
                </div>
            </div>
        </div>

        <!-- Programmi -->
        <div class="bg-gradient-to-br from-purple-500 to-purple-700 text-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90">Programmi Attivi</p>
                    <p class="text-3xl font-bold mt-2">{{ $programmiAttivi }}</p>
                    <p class="text-xs opacity-75 mt-1">su {{ $totaleProgrammi }} totali</p>
                </div>
                <div class="text-4xl opacity-80">
                    <i class="fas fa-dumbbell"></i>
                </div>
            </div>
        </div>

        <!-- Incasso Mese -->
        <div class="bg-gradient-to-br from-green-500 to-green-700 text-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90">Incasso Mese</p>
                    <p class="text-3xl font-bold mt-2">€ {{ number_format($incassoMese, 0) }}</p>
                    <p class="text-xs opacity-75 mt-1">€ {{ number_format($daIncassare, 0) }} da incassare</p>
                </div>
                <div class="text-4xl opacity-80">
                    <i class="fas fa-euro-sign"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Riga con 2 colonne -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Prossime Lezioni -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-4 border-b">
                <h2 class="text-lg font-semibold flex items-center">
                    <i class="fas fa-calendar-check text-fucsia-magia mr-2"></i>
                    Prossime Lezioni (Oggi/Domani)
                </h2>
            </div>
            <div class="p-4">
                @if($prossimeLezioni->count() > 0)
                    <div class="space-y-3">
                        @foreach($prossimeLezioni as $lezione)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex-1">
                                <p class="font-medium text-gray-800">{{ $lezione->titolo }}</p>
                                <p class="text-sm text-gray-600">
                                    <i class="far fa-clock mr-1"></i> {{ $lezione->data->format('d/m') }} - {{ substr($lezione->ora_inizio, 0, 5) }}
                                    @if($lezione->sede)
                                    <span class="ml-2"><i class="fas fa-map-marker-alt mr-1"></i>{{ $lezione->sede->nome }}</span>
                                    @endif
                                </p>
                            </div>
                            <span class="px-2 py-1 text-xs rounded {{ $lezione->badge_stato }}">{{ ucfirst($lezione->stato) }}</span>
                        </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-center py-8">Nessuna lezione programmata</p>
                @endif
            </div>
        </div>

        <!-- Pagamenti in Scadenza -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-4 border-b">
                <h2 class="text-lg font-semibold flex items-center">
                    <i class="fas fa-exclamation-triangle text-yellow-500 mr-2"></i>
                    Pagamenti in Scadenza (7 giorni)
                </h2>
            </div>
            <div class="p-4">
                @if($pagamentiScadenza->count() > 0)
                    <div class="space-y-3">
                        @foreach($pagamentiScadenza as $pagamento)
                        <div class="flex items-center justify-between p-3 bg-yellow-50 rounded-lg">
                            <div class="flex-1">
                                <p class="font-medium text-gray-800">{{ $pagamento->cliente->nome }} {{ $pagamento->cliente->cognome }}</p>
                                <p class="text-sm text-gray-600">
                                    € {{ number_format($pagamento->importo_residuo, 2) }} - Scad: {{ $pagamento->data_scadenza->format('d/m/Y') }}
                                </p>
                            </div>
                            <a href="{{ route('admin.pagamenti.show', $pagamento->id) }}" class="text-fucsia-magia hover:text-viola-magia">
                                <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-center py-8">Nessun pagamento in scadenza</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-lg font-semibold mb-4 flex items-center">
            <i class="fas fa-bolt text-yellow-500 mr-2"></i>
            Azioni Rapide
        </h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <a href="{{ route('admin.lezioni.create') }}" class="flex flex-col items-center p-4 bg-gradient-to-br from-viola-magia to-fucsia-magia text-white rounded-lg hover:shadow-lg transition">
                <i class="fas fa-plus-circle text-3xl mb-2"></i>
                <span class="text-sm font-medium">Nuova Lezione</span>
            </a>
            <a href="{{ route('admin.clienti.create') }}" class="flex flex-col items-center p-4 bg-gradient-to-br from-blue-500 to-blue-600 text-white rounded-lg hover:shadow-lg transition">
                <i class="fas fa-user-plus text-3xl mb-2"></i>
                <span class="text-sm font-medium">Nuovo Cliente</span>
            </a>
            <a href="{{ route('admin.pagamenti.create') }}" class="flex flex-col items-center p-4 bg-gradient-to-br from-green-500 to-green-600 text-white rounded-lg hover:shadow-lg transition">
                <i class="fas fa-cash-register text-3xl mb-2"></i>
                <span class="text-sm font-medium">Registra Pagamento</span>
            </a>
            <a href="{{ route('admin.calendario.index') }}" class="flex flex-col items-center p-4 bg-gradient-to-br from-purple-500 to-purple-600 text-white rounded-lg hover:shadow-lg transition">
                <i class="fas fa-calendar-alt text-3xl mb-2"></i>
                <span class="text-sm font-medium">Calendario</span>
            </a>
        </div>
    </div>

    <!-- Notifiche Sistema -->
    @if($certificatiScadenza > 0 || $pagamentiScadenza->count() > 0)
    <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 mb-6 rounded-r-lg">
        <div class="flex items-start">
            <i class="fas fa-exclamation-triangle text-yellow-500 text-xl mr-3 mt-1"></i>
            <div class="flex-1">
                <h3 class="text-lg font-semibold text-yellow-800 mb-2">Notifiche Sistema</h3>
                <ul class="space-y-1 text-yellow-700">
                    @if($certificatiScadenza > 0)
                    <li class="flex items-center">
                        <i class="fas fa-file-medical text-sm mr-2"></i>
                        <span><strong>{{ $certificatiScadenza }}</strong> certificati medici in scadenza nei prossimi 30 giorni</span>
                    </li>
                    @endif
                    @if($pagamentiScadenza->count() > 0)
                    <li class="flex items-center">
                        <i class="fas fa-euro-sign text-sm mr-2"></i>
                        <span><strong>{{ $pagamentiScadenza->count() }}</strong> pagamenti in scadenza nei prossimi 7 giorni</span>
                    </li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
    @endif

    <!-- Grafici -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Grafico Incassi -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold mb-4 flex items-center">
                <i class="fas fa-euro-sign text-green-500 mr-2"></i>
                Incassi Ultimi 6 Mesi
            </h2>
            <div style="position: relative; height: 300px;">
                <canvas id="chartIncassi"></canvas>
            </div>
        </div>

        <!-- Grafico Nuovi Clienti -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold mb-4 flex items-center">
                <i class="fas fa-user-plus text-blue-500 mr-2"></i>
                Nuovi Clienti Ultimi 6 Mesi
            </h2>
            <div style="position: relative; height: 300px;">
                <canvas id="chartClienti"></canvas>
            </div>
        </div>
    </div>

    <!-- Grafici Riga 2 -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Grafico Presenze -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold mb-4 flex items-center">
                <i class="fas fa-user-check text-purple-500 mr-2"></i>
                Presenze Ultimi 6 Mesi
            </h2>
            <div style="position: relative; height: 300px;">
                <canvas id="chartPresenze"></canvas>
            </div>
        </div>

        <!-- Calendario Mini Prossimi Eventi -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold mb-4 flex items-center">
                <i class="fas fa-calendar-week text-fucsia-magia mr-2"></i>
                Prossimi Eventi (7 giorni)
            </h2>
            <div class="space-y-2">
                @php
                    $prossimiEventi = \App\Models\Lezione::with(['programma', 'sede'])
                        ->whereBetween('data', [now(), now()->addDays(7)])
                        ->orderBy('data')
                        ->orderBy('ora_inizio')
                        ->limit(5)
                        ->get();
                @endphp
                @if($prossimiEventi->count() > 0)
                    @foreach($prossimiEventi as $evento)
                    <div class="flex items-center p-2 bg-gray-50 rounded hover:bg-gray-100 transition">
                        <div class="flex-shrink-0 w-12 text-center">
                            <p class="text-xs text-gray-500">{{ $evento->data->format('d') }}</p>
                            <p class="text-xs font-bold text-fucsia-magia">{{ $evento->data->format('M') }}</p>
                        </div>
                        <div class="ml-3 flex-1">
                            <p class="text-sm font-medium text-gray-800">{{ $evento->titolo }}</p>
                            <p class="text-xs text-gray-600">
                                <i class="far fa-clock"></i> {{ substr($evento->ora_inizio, 0, 5) }}
                                @if($evento->sede)
                                <span class="ml-2"><i class="fas fa-map-marker-alt"></i> {{ $evento->sede->nome }}</span>
                                @endif
                            </p>
                        </div>
                    </div>
                    @endforeach
                @else
                    <p class="text-gray-500 text-center py-8">Nessun evento nei prossimi 7 giorni</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Ultimi Clienti Registrati -->
    <div class="bg-white rounded-lg shadow">
        <div class="p-4 border-b">
            <h2 class="text-lg font-semibold flex items-center">
                <i class="fas fa-user-plus text-fucsia-magia mr-2"></i>
                Ultimi Clienti Registrati
            </h2>
        </div>
        @if($ultimiClienti->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cliente</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stato</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Registrato</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Azioni</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($ultimiClienti as $cliente)
                    <tr>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">{{ $cliente->nome }} {{ $cliente->cognome }}</div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $cliente->email }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs rounded-full {{ $cliente->stato_cliente == 'attivo' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ ucfirst($cliente->stato_cliente ?? 'N/D') }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $cliente->created_at->format('d/m/Y') }}</td>
                        <td class="px-6 py-4 text-center">
                            <a href="{{ route('admin.clienti.show', $cliente->id) }}" class="text-blue-600 hover:text-blue-800">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <p class="text-gray-500 text-center py-8">Nessun cliente registrato</p>
        @endif
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Colori Brand MA.GIA DONNA
    const violaMagia = '#7B2869';
    const fucsiaMagia = '#E91E8C';

    // ========================================
    // GRAFICO INCASSI
    // ========================================
    const ctxIncassi = document.getElementById('chartIncassi').getContext('2d');
    new Chart(ctxIncassi, {
        type: 'bar',
        data: {
            labels: {!! json_encode($incassiMesi->pluck('mese')) !!},
            datasets: [{
                label: 'Incassi (€)',
                data: {!! json_encode($incassiMesi->pluck('totale')) !!},
                backgroundColor: createGradient(ctxIncassi, fucsiaMagia, violaMagia),
                borderColor: violaMagia,
                borderWidth: 1,
                borderRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return '€ ' + context.parsed.y.toLocaleString('it-IT');
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '€' + value.toLocaleString('it-IT');
                        }
                    }
                }
            }
        }
    });

    // ========================================
    // GRAFICO NUOVI CLIENTI
    // ========================================
    const ctxClienti = document.getElementById('chartClienti').getContext('2d');
    new Chart(ctxClienti, {
        type: 'line',
        data: {
            labels: {!! json_encode($clientiMesi->pluck('mese')) !!},
            datasets: [{
                label: 'Nuovi Clienti',
                data: {!! json_encode($clientiMesi->pluck('totale')) !!},
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                borderColor: '#3b82f6',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#3b82f6',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 5,
                pointHoverRadius: 7
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

    // ========================================
    // GRAFICO PRESENZE
    // ========================================
    const ctxPresenze = document.getElementById('chartPresenze').getContext('2d');
    new Chart(ctxPresenze, {
        type: 'bar',
        data: {
            labels: {!! json_encode($presenzeMesi->pluck('mese')) !!},
            datasets: [{
                label: 'Presenze',
                data: {!! json_encode($presenzeMesi->pluck('totale')) !!},
                backgroundColor: createGradient(ctxPresenze, '#a855f7', '#7e22ce'),
                borderColor: '#7e22ce',
                borderWidth: 1,
                borderRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.parsed.y + ' presenze';
                        }
                    }
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

    // ========================================
    // HELPER: Crea Gradiente
    // ========================================
    function createGradient(ctx, color1, color2) {
        const gradient = ctx.createLinearGradient(0, 0, 0, 300);
        gradient.addColorStop(0, color1);
        gradient.addColorStop(1, color2);
        return gradient;
    }
});
</script>
@endpush

@endsection
