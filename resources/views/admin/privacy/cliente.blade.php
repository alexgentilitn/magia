@extends('layouts.admin')

@section('titolo', 'Consensi Privacy - ' . $cliente->nome_completo)

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
                    <span class="text-gray-500">{{ $cliente->nome_completo }}</span>
                </div>
            </li>
        </ol>
    </nav>

    <!-- Header Cliente -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="flex items-start justify-between">
            <div class="flex items-center">
                <div class="h-16 w-16 rounded-full bg-gradient-to-br from-viola-magia to-fucsia-magia flex items-center justify-center text-white font-bold text-2xl">
                    {{ strtoupper(substr($cliente->nome, 0, 1) . substr($cliente->cognome, 0, 1)) }}
                </div>
                <div class="ml-4">
                    <h2 class="text-2xl font-bold text-gray-800">{{ $cliente->nome_completo }}</h2>
                    <p class="text-gray-600">{{ $cliente->email }}</p>
                    <p class="text-sm text-gray-500 mt-1">
                        <i class="fas fa-phone mr-1"></i> {{ $cliente->telefono }}
                        @if($cliente->codice_cliente)
                            <span class="ml-3">
                                <i class="fas fa-barcode mr-1"></i> {{ $cliente->codice_cliente }}
                            </span>
                        @endif
                    </p>
                </div>
            </div>
            <div>
                <a href="{{ route('admin.clienti.show', $cliente->id) }}"
                   class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                    <i class="fas fa-user mr-2"></i>
                    Vedi Profilo Completo
                </a>
            </div>
        </div>
    </div>

    <!-- Riepilogo Stato Consensi -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
        @php
            $tipiConsenso = [
                'privacy_policy' => ['label' => 'Privacy Policy', 'icon' => 'file-contract', 'color' => 'green'],
                'marketing' => ['label' => 'Marketing', 'icon' => 'bullhorn', 'color' => 'blue'],
                'profilazione' => ['label' => 'Profilazione', 'icon' => 'chart-line', 'color' => 'purple'],
                'cookie' => ['label' => 'Cookie', 'icon' => 'cookie-bite', 'color' => 'yellow'],
                'terze_parti' => ['label' => 'Terze Parti', 'icon' => 'share-nodes', 'color' => 'red']
            ];
        @endphp

        @foreach($tipiConsenso as $tipo => $info)
            @php
                $consensoAttivo = $consensi[$tipo] ?? collect();
                $ultimoConsenso = $consensoAttivo->first();
                $haConsenso = $ultimoConsenso && $ultimoConsenso->consenso_dato && $ultimoConsenso->stato == 'attivo';
            @endphp
            <div class="bg-white rounded-lg shadow p-4 border-l-4 border-{{ $info['color'] }}-500">
                <div class="flex items-center justify-between mb-2">
                    <i class="fas fa-{{ $info['icon'] }} text-{{ $info['color'] }}-500 text-xl"></i>
                    @if($haConsenso)
                        <i class="fas fa-check-circle text-green-500"></i>
                    @else
                        <i class="fas fa-times-circle text-red-500"></i>
                    @endif
                </div>
                <p class="text-xs text-gray-600 font-medium">{{ $info['label'] }}</p>
                <p class="text-lg font-bold text-gray-800 mt-1">
                    @if($haConsenso)
                        <span class="text-green-600">Attivo</span>
                    @else
                        <span class="text-red-600">Non attivo</span>
                    @endif
                </p>
                @if($ultimoConsenso)
                    <p class="text-xs text-gray-500 mt-1">
                        {{ $ultimoConsenso->data_consenso->format('d/m/Y') }}
                    </p>
                @endif
            </div>
        @endforeach
    </div>

    <!-- Storico Consensi per Tipo -->
    @foreach($tipiConsenso as $tipo => $info)
        @php
            $consensiTipo = $consensi[$tipo] ?? collect();
        @endphp

        @if($consensiTipo->isNotEmpty())
        <div class="bg-white rounded-lg shadow mb-6 overflow-hidden">
            <div class="px-6 py-4 bg-{{ $info['color'] }}-50 border-b border-{{ $info['color'] }}-100">
                <h3 class="text-lg font-semibold text-gray-800">
                    <i class="fas fa-{{ $info['icon'] }} mr-2 text-{{ $info['color'] }}-600"></i>
                    {{ $info['label'] }}
                    <span class="ml-2 text-sm font-normal text-gray-600">({{ $consensiTipo->count() }} versioni)</span>
                </h3>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Consenso</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Data</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stato</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Versione Policy</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">IP</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">User Agent</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($consensiTipo as $consenso)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($consenso->consenso_dato)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <i class="fas fa-check mr-1"></i> Accordato
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        <i class="fas fa-times mr-1"></i> Negato
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    {{ $consenso->data_consenso ? $consenso->data_consenso->format('d/m/Y H:i') : '-' }}
                                </div>
                                @if($consenso->data_revoca)
                                    <div class="text-xs text-red-600">
                                        Revocato: {{ $consenso->data_revoca->format('d/m/Y H:i') }}
                                    </div>
                                @endif
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
                            <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate" title="{{ $consenso->user_agent }}">
                                {{ $consenso->user_agent ? Str::limit($consenso->user_agent, 50) : '-' }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    @endforeach

    <!-- Se non ci sono consensi -->
    @if($consensi->flatten()->isEmpty())
    <div class="bg-white rounded-lg shadow p-12 text-center">
        <i class="fas fa-inbox text-6xl text-gray-300 mb-4"></i>
        <h3 class="text-xl font-semibold text-gray-800 mb-2">Nessun Consenso Registrato</h3>
        <p class="text-gray-600">Questo cliente non ha ancora fornito alcun consenso privacy.</p>
    </div>
    @endif

</div>
@endsection
