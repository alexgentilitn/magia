@extends('layouts.admin')

@section('titolo', 'Referral - ' . $cliente->nome_completo)

@section('contenuto')
<div class="p-6">

    <!-- Breadcrumb -->
    <nav class="flex mb-6" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="{{ route('admin.referral.index') }}" class="text-gray-700 hover:text-viola-magia">
                    <i class="fas fa-user-friends mr-2"></i>
                    Referral
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
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="h-16 w-16 rounded-full bg-gradient-to-br from-viola-magia to-fucsia-magia flex items-center justify-center text-white font-bold text-2xl">
                    {{ strtoupper(substr($cliente->nome, 0, 1) . substr($cliente->cognome, 0, 1)) }}
                </div>
                <div class="ml-4">
                    <h2 class="text-2xl font-bold text-gray-800">{{ $cliente->nome_completo }}</h2>
                    <p class="text-gray-600">{{ $cliente->email }}</p>
                </div>
            </div>
            <a href="{{ route('admin.clienti.show', $cliente->id) }}"
               class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                <i class="fas fa-user mr-2"></i>
                Profilo Completo
            </a>
        </div>
    </div>

    <!-- Statistiche Referral Cliente -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-blue-500">
            <p class="text-xs text-gray-600">Totale Inviti</p>
            <p class="text-2xl font-bold text-gray-800">{{ $statistiche['totale_inviti'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-yellow-500">
            <p class="text-xs text-gray-600">In Attesa</p>
            <p class="text-2xl font-bold text-gray-800">{{ $statistiche['pending'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-purple-500">
            <p class="text-xs text-gray-600">Registrati</p>
            <p class="text-2xl font-bold text-gray-800">{{ $statistiche['registrati'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-green-500">
            <p class="text-xs text-gray-600">Convertiti</p>
            <p class="text-2xl font-bold text-gray-800">{{ $statistiche['convertiti'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-fucsia-magia">
            <p class="text-xs text-gray-600">Sconti Guadagnati</p>
            <p class="text-2xl font-bold text-gray-800">€{{ number_format($statistiche['totale_sconti'], 2) }}</p>
        </div>
    </div>

    <!-- Lista Referral -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 bg-gray-50 border-b">
            <h3 class="text-lg font-semibold text-gray-800">Amiche Invitate</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email Invitato</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Codice</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stato</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Data Invito</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Conversione</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($referrals as $ref)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">{{ $ref->email_invitato }}</div>
                            @if($ref->clienteInvitato)
                                <div class="text-xs text-green-600">
                                    <i class="fas fa-check-circle"></i> {{ $ref->clienteInvitato->nome_completo }}
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <span class="font-mono text-xs bg-gray-100 px-2 py-1 rounded">{{ $ref->codice_invito }}</span>
                        </td>
                        <td class="px-6 py-4">
                            @if($ref->stato == 'pending')
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">In Attesa</span>
                            @elseif($ref->stato == 'registrato')
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">Registrato</span>
                            @elseif($ref->stato == 'convertito')
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Convertito</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ $ref->data_invito->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            @if($ref->data_conversione)
                                {{ $ref->data_conversione->format('d/m/Y') }}
                                <div class="text-xs text-green-600">€{{ $ref->sconto_invitante }}</div>
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                            Nessun invito inviato
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
