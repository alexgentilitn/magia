@extends('layouts.admin')

@section('titolo', 'Report Consensi Privacy')

@section('contenuto')
<div class="p-6">

    <!-- Breadcrumb -->
    <nav class="flex mb-6" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="{{ route('admin.privacy.index') }}" class="text-gray-700 hover:text-viola-magia">
                    <i class="fas fa-shield-alt mr-2"></i>
                    Privacy GDPR
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                    <span class="text-gray-500">Report Consensi</span>
                </div>
            </li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-chart-bar mr-2 text-viola-magia"></i>
                Report Consensi Privacy
            </h2>
            <p class="text-gray-600 mt-1">Analisi dettagliata per tipologia</p>
        </div>
        <div class="mt-4 md:mt-0">
            <a href="{{ route('admin.privacy.export', array_filter(['tipo' => request('tipo')])) }}"
               class="inline-flex items-center px-4 py-2 bg-green-500 text-white font-semibold rounded-lg hover:bg-green-600 transition">
                <i class="fas fa-file-csv mr-2"></i>
                Esporta CSV
            </a>
        </div>
    </div>

    <!-- Filtri -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <form method="GET" action="{{ route('admin.privacy.report') }}" class="space-y-4">

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                <!-- Tipo Consenso -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-filter mr-1"></i> Tipo Consenso
                    </label>
                    <select name="tipo" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent">
                        <option value="privacy_policy" {{ $tipo == 'privacy_policy' ? 'selected' : '' }}>Privacy Policy</option>
                        <option value="marketing" {{ $tipo == 'marketing' ? 'selected' : '' }}>Marketing</option>
                        <option value="profilazione" {{ $tipo == 'profilazione' ? 'selected' : '' }}>Profilazione</option>
                        <option value="cookie" {{ $tipo == 'cookie' ? 'selected' : '' }}>Cookie</option>
                        <option value="terze_parti" {{ $tipo == 'terze_parti' ? 'selected' : '' }}>Terze Parti</option>
                    </select>
                </div>

                <!-- Data Inizio -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-calendar mr-1"></i> Data Inizio
                    </label>
                    <input
                        type="date"
                        name="data_inizio"
                        value="{{ request('data_inizio', now()->subMonths(3)->format('Y-m-d')) }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent"
                    >
                </div>

                <!-- Data Fine -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-calendar mr-1"></i> Data Fine
                    </label>
                    <input
                        type="date"
                        name="data_fine"
                        value="{{ request('data_fine', now()->format('Y-m-d')) }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent"
                    >
                </div>

            </div>

            <!-- Bottoni Azione -->
            <div class="flex items-center gap-2">
                <button type="submit" class="px-6 py-2 bg-fucsia-magia text-white font-medium rounded-lg hover:bg-viola-magia transition">
                    <i class="fas fa-search mr-2"></i> Applica Filtri
                </button>
                <a href="{{ route('admin.privacy.report') }}" class="px-6 py-2 bg-gray-200 text-gray-700 font-medium rounded-lg hover:bg-gray-300 transition">
                    <i class="fas fa-redo mr-2"></i> Reset
                </a>
            </div>

        </form>
    </div>

    <!-- Statistiche Report -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">

        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium">Totale Consensi</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ $consensi->count() }}</p>
                </div>
                <div class="bg-blue-500 bg-opacity-10 rounded-full p-4">
                    <i class="fas fa-list text-2xl text-blue-500"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium">Accordati</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ $statistiche['accordati'] }}</p>
                    @if($consensi->count() > 0)
                        <p class="text-xs text-gray-500 mt-1">
                            {{ round(($statistiche['accordati'] / $consensi->count()) * 100, 1) }}%
                        </p>
                    @endif
                </div>
                <div class="bg-green-500 bg-opacity-10 rounded-full p-4">
                    <i class="fas fa-check text-2xl text-green-500"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-red-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium">Negati</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ $statistiche['negati'] }}</p>
                    @if($consensi->count() > 0)
                        <p class="text-xs text-gray-500 mt-1">
                            {{ round(($statistiche['negati'] / $consensi->count()) * 100, 1) }}%
                        </p>
                    @endif
                </div>
                <div class="bg-red-500 bg-opacity-10 rounded-full p-4">
                    <i class="fas fa-times text-2xl text-red-500"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-yellow-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium">Revocati</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ $statistiche['revocati'] }}</p>
                    @if($consensi->count() > 0)
                        <p class="text-xs text-gray-500 mt-1">
                            {{ round(($statistiche['revocati'] / $consensi->count()) * 100, 1) }}%
                        </p>
                    @endif
                </div>
                <div class="bg-yellow-500 bg-opacity-10 rounded-full p-4">
                    <i class="fas fa-ban text-2xl text-yellow-500"></i>
                </div>
            </div>
        </div>

    </div>

    <!-- Tabella Dettaglio -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">
                <i class="fas fa-table mr-2"></i>
                Dettaglio Consensi
                @php
                    $tipoLabel = [
                        'privacy_policy' => 'Privacy Policy',
                        'marketing' => 'Marketing',
                        'profilazione' => 'Profilazione',
                        'cookie' => 'Cookie',
                        'terze_parti' => 'Terze Parti'
                    ];
                @endphp
                <span class="text-fucsia-magia">{{ $tipoLabel[$tipo] ?? $tipo }}</span>
            </h3>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200" id="report-table">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Cliente
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Email
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Consenso
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Data Consenso
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Stato
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Versione
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            IP
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Azioni
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($consensi as $consenso)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">
                                {{ $consenso->cliente->nome_completo }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-500">
                                {{ $consenso->cliente->email }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($consenso->consenso_dato)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-check mr-1"></i> Accordato
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    <i class="fas fa-times mr-1"></i> Negato
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $consenso->data_consenso ? $consenso->data_consenso->format('d/m/Y H:i') : '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($consenso->stato == 'attivo')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-circle text-xs mr-1"></i> Attivo
                                </span>
                            @elseif($consenso->stato == 'revocato')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    <i class="fas fa-ban mr-1"></i> Revocato
                                </span>
                            @elseif($consenso->stato == 'scaduto')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    <i class="fas fa-clock mr-1"></i> Scaduto
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    {{ $consenso->stato }}
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $consenso->versione_policy ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 font-mono">
                            {{ $consenso->ip_address ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="{{ route('admin.privacy.cliente', $consenso->cliente_id) }}"
                               class="text-viola-magia hover:text-fucsia-magia"
                               title="Vedi tutti i consensi">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-inbox text-4xl mb-3 text-gray-300"></i>
                            <p>Nessun consenso trovato con i filtri selezionati</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

@push('scripts')
<script>
$(document).ready(function() {
    $('#report-table').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.8/i18n/it-IT.json'
        },
        order: [[3, 'desc']], // Ordina per data decrescente
        pageLength: 50,
        responsive: true,
        dom: 'Bfrtip',
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ]
    });
});
</script>
@endpush

@endsection
