@extends('layouts.admin')

@section('titolo', 'Programma Referral')

@section('contenuto')
<div class="p-6">

    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Programma "Porta un Amico"</h2>
            <p class="text-gray-600 mt-1">Gestisci il programma referral e gli sconti</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.referral.configurazione') }}" class="px-4 py-2 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition">
                <i class="fas fa-cog mr-2"></i>Configurazione
            </a>
            <a href="{{ route('admin.referral.report') }}" class="px-4 py-2 bg-green-100 text-green-700 rounded-lg hover:bg-green-200 transition">
                <i class="fas fa-chart-bar mr-2"></i>Report
            </a>
        </div>
    </div>

    <!-- Statistiche -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-viola-magia">
            <p class="text-sm text-gray-600 font-medium">Totale Inviti</p>
            <p class="text-2xl font-bold text-gray-800">{{ $statistiche['totale_inviti'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-yellow-500">
            <p class="text-sm text-gray-600 font-medium">In Attesa</p>
            <p class="text-2xl font-bold text-gray-800">{{ $statistiche['in_attesa'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-blue-500">
            <p class="text-sm text-gray-600 font-medium">Iscritti</p>
            <p class="text-2xl font-bold text-gray-800">{{ $statistiche['iscritti'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-green-500">
            <p class="text-sm text-gray-600 font-medium">Attivi</p>
            <p class="text-2xl font-bold text-gray-800">{{ $statistiche['attivi'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-fucsia-magia">
            <p class="text-sm text-gray-600 font-medium">Sconti da Applicare</p>
            <p class="text-2xl font-bold text-gray-800">{{ $statistiche['sconti_da_applicare'] }}</p>
        </div>
    </div>

    <!-- Tabella Referral -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gradient-to-r from-viola-magia to-fucsia-magia text-white">
                    <tr>
                        <th class="px-6 py-4 text-left text-sm font-semibold">Cliente Invitante</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold">Codice</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold">Cliente Invitato</th>
                        <th class="px-6 py-4 text-center text-sm font-semibold">Stato</th>
                        <th class="px-6 py-4 text-center text-sm font-semibold">Sconto</th>
                        <th class="px-6 py-4 text-center text-sm font-semibold">Azioni</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($referrals as $referral)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <a href="{{ route('admin.clienti.show', $referral->cliente_id) }}" class="text-viola-magia hover:underline font-medium">
                                    {{ $referral->cliente->nome_completo ?? 'N/A' }}
                                </a>
                            </td>
                            <td class="px-6 py-4">
                                <code class="px-3 py-1 bg-gray-100 text-viola-magia rounded font-mono text-sm">
                                    {{ $referral->codice_referral }}
                                </code>
                            </td>
                            <td class="px-6 py-4">
                                @if($referral->cliente_invitato_id)
                                    <a href="{{ route('admin.clienti.show', $referral->cliente_invitato_id) }}" class="text-viola-magia hover:underline">
                                        {{ $referral->clienteInvitato->nome_completo }}
                                    </a>
                                @else
                                    <span class="text-gray-400 italic">In attesa registrazione</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                @php
                                    $stati_colors = [
                                        'in_attesa' => 'yellow',
                                        'iscritto' => 'blue',
                                        'attivo' => 'green',
                                        'sconto_applicato' => 'purple',
                                        'annullato' => 'red'
                                    ];
                                    $color = $stati_colors[$referral->stato] ?? 'gray';
                                @endphp
                                <span class="px-3 py-1 bg-{{ $color }}-100 text-{{ $color }}-800 text-xs font-medium rounded-full">
                                    {{ \App\Models\Referral::getStati()[$referral->stato] ?? $referral->stato }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($referral->sconto_applicato)
                                    <i class="fas fa-check-circle text-green-600" title="Sconto applicato"></i>
                                @elseif($referral->sconto_tipo)
                                    <span class="text-sm text-gray-600">
                                        {{ $referral->sconto_tipo === 'percentuale' ? $referral->sconto_valore . '%' : '€ ' . $referral->sconto_valore }}
                                    </span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('admin.referral.show', $referral->id) }}" class="text-blue-600 hover:text-blue-800" title="Dettagli">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                <i class="fas fa-users text-4xl mb-3 block text-gray-300"></i>
                                Nessun referral al momento
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($referrals->hasPages())
            <div class="px-6 py-4 border-t">
                {{ $referrals->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
