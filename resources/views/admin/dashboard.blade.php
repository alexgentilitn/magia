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

    <!-- Grafici -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Grafico Incassi -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold mb-4">Incassi Ultimi 6 Mesi</h2>
            <div class="h-64 flex items-end justify-between space-x-2">
                @foreach($incassiMesi as $mese)
                    @php
                        $maxIncasso = $incassiMesi->max('totale');
                        $altezza = $maxIncasso > 0 ? ($mese['totale'] / $maxIncasso) * 100 : 0;
                    @endphp
                    <div class="flex-1 flex flex-col items-center">
                        <div class="w-full bg-gradient-to-t from-fucsia-magia to-viola-magia rounded-t" style="height: {{ $altezza }}%;min-height: 20px;"></div>
                        <p class="text-xs text-gray-600 mt-2 transform -rotate-45 origin-top-left">{{ $mese['mese'] }}</p>
                        <p class="text-xs font-bold text-gray-800">€{{ number_format($mese['totale'], 0) }}</p>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Grafico Nuovi Clienti -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold mb-4">Nuovi Clienti Ultimi 6 Mesi</h2>
            <div class="h-64 flex items-end justify-between space-x-2">
                @foreach($clientiMesi as $mese)
                    @php
                        $maxClienti = $clientiMesi->max('totale');
                        $altezza = $maxClienti > 0 ? ($mese['totale'] / $maxClienti) * 100 : 0;
                    @endphp
                    <div class="flex-1 flex flex-col items-center">
                        <div class="w-full bg-gradient-to-t from-blue-500 to-blue-700 rounded-t" style="height: {{ $altezza }}%; min-height: 20px;"></div>
                        <p class="text-xs text-gray-600 mt-2 transform -rotate-45 origin-top-left">{{ $mese['mese'] }}</p>
                        <p class="text-xs font-bold text-gray-800">{{ $mese['totale'] }}</p>
                    </div>
                @endforeach
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
@endsection
