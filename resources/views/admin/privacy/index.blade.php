@extends('layouts.admin')

@section('titolo', 'Gestione Privacy e Consensi GDPR')

@section('contenuto')
<div class="p-6">

    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-shield-alt mr-2 text-viola-magia"></i>
                Gestione Privacy e Consensi GDPR
            </h2>
            <p class="text-gray-600 mt-1">Dashboard conformità e monitoraggio consensi</p>
        </div>
        <div class="mt-4 md:mt-0 flex gap-2">
            <a href="{{ route('admin.privacy.conformita') }}"
               class="inline-flex items-center px-4 py-2 bg-yellow-500 text-white font-semibold rounded-lg hover:bg-yellow-600 transition">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                Verifica Conformità
            </a>
            <a href="{{ route('admin.privacy.export') }}"
               class="inline-flex items-center px-4 py-2 bg-green-500 text-white font-semibold rounded-lg hover:bg-green-600 transition">
                <i class="fas fa-file-csv mr-2"></i>
                Esporta CSV
            </a>
        </div>
    </div>

    <!-- Statistiche Principali -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

        <!-- Totale Clienti -->
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-viola-magia">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium">Totale Clienti</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ $statistiche['totale_clienti'] }}</p>
                </div>
                <div class="bg-viola-magia bg-opacity-10 rounded-full p-4">
                    <i class="fas fa-users text-2xl text-viola-magia"></i>
                </div>
            </div>
        </div>

        <!-- Con Privacy Policy -->
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium">Con Privacy Policy</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ $statistiche['con_privacy'] }}</p>
                    @if($statistiche['totale_clienti'] > 0)
                        <p class="text-xs text-gray-500 mt-1">
                            {{ round(($statistiche['con_privacy'] / $statistiche['totale_clienti']) * 100, 1) }}%
                        </p>
                    @endif
                </div>
                <div class="bg-green-500 bg-opacity-10 rounded-full p-4">
                    <i class="fas fa-check-circle text-2xl text-green-500"></i>
                </div>
            </div>
        </div>

        <!-- Con Marketing -->
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium">Con Marketing</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ $statistiche['con_marketing'] }}</p>
                    @if($statistiche['totale_clienti'] > 0)
                        <p class="text-xs text-gray-500 mt-1">
                            {{ round(($statistiche['con_marketing'] / $statistiche['totale_clienti']) * 100, 1) }}%
                        </p>
                    @endif
                </div>
                <div class="bg-blue-500 bg-opacity-10 rounded-full p-4">
                    <i class="fas fa-envelope text-2xl text-blue-500"></i>
                </div>
            </div>
        </div>

        <!-- Con Profilazione -->
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium">Con Profilazione</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ $statistiche['con_profilazione'] }}</p>
                    @if($statistiche['totale_clienti'] > 0)
                        <p class="text-xs text-gray-500 mt-1">
                            {{ round(($statistiche['con_profilazione'] / $statistiche['totale_clienti']) * 100, 1) }}%
                        </p>
                    @endif
                </div>
                <div class="bg-purple-500 bg-opacity-10 rounded-full p-4">
                    <i class="fas fa-chart-line text-2xl text-purple-500"></i>
                </div>
            </div>
        </div>

    </div>

    <!-- Statistiche Stato Consensi -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">

        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-green-500">
            <div class="flex items-center">
                <i class="fas fa-check text-green-500 text-xl mr-3"></i>
                <div>
                    <p class="text-sm text-gray-600">Consensi Attivi</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $statistiche['consensi_totali'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-red-500">
            <div class="flex items-center">
                <i class="fas fa-times text-red-500 text-xl mr-3"></i>
                <div>
                    <p class="text-sm text-gray-600">Consensi Revocati</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $statistiche['consensi_revocati'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-yellow-500">
            <div class="flex items-center">
                <i class="fas fa-exclamation-triangle text-yellow-500 text-xl mr-3"></i>
                <div>
                    <p class="text-sm text-gray-600">Consensi Scaduti</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $statistiche['consensi_scaduti'] }}</p>
                </div>
            </div>
        </div>

    </div>

    <!-- Filtri Rapidi -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">
            <i class="fas fa-filter mr-2"></i>
            Report per Tipologia
        </h3>
        <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
            <a href="{{ route('admin.privacy.report', ['tipo' => 'privacy_policy']) }}"
               class="px-4 py-3 bg-gradient-to-r from-green-500 to-green-600 text-white rounded-lg hover:shadow-lg transition text-center">
                <i class="fas fa-file-contract block text-2xl mb-1"></i>
                <span class="text-sm font-medium">Privacy Policy</span>
            </a>
            <a href="{{ route('admin.privacy.report', ['tipo' => 'marketing']) }}"
               class="px-4 py-3 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-lg hover:shadow-lg transition text-center">
                <i class="fas fa-bullhorn block text-2xl mb-1"></i>
                <span class="text-sm font-medium">Marketing</span>
            </a>
            <a href="{{ route('admin.privacy.report', ['tipo' => 'profilazione']) }}"
               class="px-4 py-3 bg-gradient-to-r from-purple-500 to-purple-600 text-white rounded-lg hover:shadow-lg transition text-center">
                <i class="fas fa-user-chart block text-2xl mb-1"></i>
                <span class="text-sm font-medium">Profilazione</span>
            </a>
            <a href="{{ route('admin.privacy.report', ['tipo' => 'cookie']) }}"
               class="px-4 py-3 bg-gradient-to-r from-yellow-500 to-yellow-600 text-white rounded-lg hover:shadow-lg transition text-center">
                <i class="fas fa-cookie-bite block text-2xl mb-1"></i>
                <span class="text-sm font-medium">Cookie</span>
            </a>
            <a href="{{ route('admin.privacy.report', ['tipo' => 'terze_parti']) }}"
               class="px-4 py-3 bg-gradient-to-r from-red-500 to-red-600 text-white rounded-lg hover:shadow-lg transition text-center">
                <i class="fas fa-share-nodes block text-2xl mb-1"></i>
                <span class="text-sm font-medium">Terze Parti</span>
            </a>
        </div>
    </div>

    <!-- Ultimi Consensi Registrati -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">
                <i class="fas fa-history mr-2"></i>
                Ultimi Consensi Registrati
            </h3>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200" id="consensi-table">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Cliente
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Tipo Consenso
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Consenso
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Data
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Stato
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
                    @forelse($consensi_recenti as $consenso)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-full bg-gradient-to-br from-viola-magia to-fucsia-magia flex items-center justify-center text-white font-bold">
                                        {{ strtoupper(substr($consenso->cliente->nome, 0, 1) . substr($consenso->cliente->cognome, 0, 1)) }}
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $consenso->cliente->nome_completo }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ $consenso->cliente->email }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-3 py-1 text-xs font-semibold rounded-full
                                @if($consenso->tipo_consenso == 'privacy_policy') bg-green-100 text-green-800
                                @elseif($consenso->tipo_consenso == 'marketing') bg-blue-100 text-blue-800
                                @elseif($consenso->tipo_consenso == 'profilazione') bg-purple-100 text-purple-800
                                @elseif($consenso->tipo_consenso == 'cookie') bg-yellow-100 text-yellow-800
                                @else bg-red-100 text-red-800
                                @endif">
                                {{ $consenso->tipo_consenso_label }}
                            </span>
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
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 font-mono">
                            {{ $consenso->ip_address }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="{{ route('admin.privacy.cliente', $consenso->cliente_id) }}"
                               class="text-viola-magia hover:text-fucsia-magia mr-3"
                               title="Vedi tutti i consensi">
                                <i class="fas fa-eye"></i>
                            </a>
                            @if($consenso->stato == 'attivo')
                                <button onclick="revocaConsenso({{ $consenso->id }})"
                                        class="text-red-600 hover:text-red-900"
                                        title="Revoca consenso">
                                    <i class="fas fa-ban"></i>
                                </button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-inbox text-4xl mb-3 text-gray-300"></i>
                            <p>Nessun consenso registrato</p>
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
    $('#consensi-table').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.8/i18n/it-IT.json'
        },
        order: [[3, 'desc']], // Ordina per data decrescente
        pageLength: 20,
        responsive: true
    });
});

function revocaConsenso(consensoId) {
    Swal.fire({
        title: 'Conferma Revoca',
        text: 'Sei sicuro di voler revocare questo consenso?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sì, revoca',
        cancelButtonText: 'Annulla'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/admin/privacy/${consensoId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Revocato!', data.message, 'success')
                        .then(() => location.reload());
                } else {
                    Swal.fire('Errore', data.message, 'error');
                }
            })
            .catch(error => {
                Swal.fire('Errore', 'Si è verificato un errore', 'error');
            });
        }
    });
}
</script>
@endpush

@endsection
