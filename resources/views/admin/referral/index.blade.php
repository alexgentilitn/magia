@extends('layouts.admin')

@section('titolo', 'Sistema Referral "Porta un\'Amica"')

@section('contenuto')
<div class="p-6">

    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-user-friends mr-2 text-viola-magia"></i>
                Sistema Referral "Porta un'Amica"
            </h2>
            <p class="text-gray-600 mt-1">Gestione inviti e statistiche conversioni</p>
        </div>
        <div class="mt-4 md:mt-0 flex gap-2">
            <a href="{{ route('admin.referral.statistiche') }}"
               class="inline-flex items-center px-4 py-2 bg-blue-500 text-white font-semibold rounded-lg hover:bg-blue-600 transition">
                <i class="fas fa-chart-line mr-2"></i>
                Statistiche Avanzate
            </a>
            <a href="{{ route('admin.referral.export') }}"
               class="inline-flex items-center px-4 py-2 bg-green-500 text-white font-semibold rounded-lg hover:bg-green-600 transition">
                <i class="fas fa-file-csv mr-2"></i>
                Esporta CSV
            </a>
        </div>
    </div>

    <!-- Statistiche -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium">Totale Inviti</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ $statistiche['totale_inviti'] }}</p>
                </div>
                <div class="bg-blue-500 bg-opacity-10 rounded-full p-4">
                    <i class="fas fa-envelope text-2xl text-blue-500"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-yellow-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium">In Attesa</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ $statistiche['inviti_pending'] }}</p>
                </div>
                <div class="bg-yellow-500 bg-opacity-10 rounded-full p-4">
                    <i class="fas fa-clock text-2xl text-yellow-500"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium">Convertiti</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ $statistiche['inviti_convertiti'] }}</p>
                </div>
                <div class="bg-green-500 bg-opacity-10 rounded-full p-4">
                    <i class="fas fa-check-circle text-2xl text-green-500"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium">Tasso Conversione</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ $statistiche['tasso_conversione'] }}%</p>
                </div>
                <div class="bg-purple-500 bg-opacity-10 rounded-full p-4">
                    <i class="fas fa-percentage text-2xl text-purple-500"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Invitanti -->
    @if($topInvitanti->isNotEmpty())
    <div class="bg-white rounded-lg shadow mb-6 p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">
            <i class="fas fa-trophy mr-2 text-yellow-500"></i>
            Top 10 Clienti Invitanti
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($topInvitanti as $top)
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                    <div class="flex items-center">
                        <div class="h-12 w-12 rounded-full bg-gradient-to-br from-viola-magia to-fucsia-magia flex items-center justify-center text-white font-bold">
                            {{ strtoupper(substr($top->clienteInvitante->nome, 0, 1) . substr($top->clienteInvitante->cognome, 0, 1)) }}
                        </div>
                        <div class="ml-3">
                            <p class="font-semibold text-gray-900">{{ $top->clienteInvitante->nome_completo }}</p>
                            <p class="text-sm text-gray-600">{{ $top->totale_inviti }} inviti • {{ $top->inviti_convertiti }} convertiti</p>
                        </div>
                    </div>
                    <a href="{{ route('admin.referral.cliente', $top->cliente_invitante_id) }}"
                       class="text-viola-magia hover:text-fucsia-magia">
                        <i class="fas fa-eye"></i>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Filtri -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Ricerca</label>
                <input type="text" name="ricerca" value="{{ request('ricerca') }}"
                       placeholder="Email, codice, nome..."
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Stato</label>
                <select name="stato" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia">
                    <option value="">Tutti</option>
                    <option value="pending" {{ request('stato') == 'pending' ? 'selected' : '' }}>In Attesa</option>
                    <option value="registrato" {{ request('stato') == 'registrato' ? 'selected' : '' }}>Registrati</option>
                    <option value="convertito" {{ request('stato') == 'convertito' ? 'selected' : '' }}>Convertiti</option>
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="px-6 py-2 bg-fucsia-magia text-white font-medium rounded-lg hover:bg-viola-magia transition">
                    <i class="fas fa-search mr-2"></i> Cerca
                </button>
            </div>
        </form>
    </div>

    <!-- Tabella Referral -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200" id="referral-table">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cliente Invitante</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email Invitato</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Codice</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stato</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Data Invito</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Azioni</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($referrals as $ref)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $ref->clienteInvitante->nome_completo }}</div>
                            <div class="text-sm text-gray-500">{{ $ref->clienteInvitante->email }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $ref->email_invitato }}
                            @if($ref->clienteInvitato)
                                <div class="text-xs text-green-600">✓ {{ $ref->clienteInvitato->nome_completo }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="font-mono text-sm bg-gray-100 px-2 py-1 rounded">{{ $ref->codice_invito }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($ref->stato == 'pending')
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">In Attesa</span>
                            @elseif($ref->stato == 'registrato')
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">Registrato</span>
                            @elseif($ref->stato == 'convertito')
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Convertito</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $ref->data_invito->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="{{ route('admin.referral.show', $ref->id) }}" class="text-viola-magia hover:text-fucsia-magia mr-3" title="Dettagli">
                                <i class="fas fa-eye"></i>
                            </a>
                            @if($ref->stato != 'convertito')
                                <button onclick="marcaConvertito({{ $ref->id }})" class="text-green-600 hover:text-green-900 mr-3" title="Marca convertito">
                                    <i class="fas fa-check-circle"></i>
                                </button>
                            @endif
                            <button onclick="eliminaReferral({{ $ref->id }})" class="text-red-600 hover:text-red-900" title="Elimina">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-inbox text-4xl mb-3 text-gray-300"></i>
                            <p>Nessun referral trovato</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 bg-gray-50">
            {{ $referrals->links() }}
        </div>
    </div>

</div>

@push('scripts')
<script>
$(document).ready(function() {
    $('#referral-table').DataTable({
        language: { url: '//cdn.datatables.net/plug-ins/1.13.8/i18n/it-IT.json' },
        order: [[4, 'desc']],
        pageLength: 50
    });
});

function marcaConvertito(id) {
    Swal.fire({
        title: 'Marca come Convertito',
        text: 'Confermi che questo referral ha completato la conversione?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sì, converti',
        cancelButtonText: 'Annulla'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/admin/referral/${id}/convertito`, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Convertito!', data.message, 'success').then(() => location.reload());
                } else {
                    Swal.fire('Errore', data.message, 'error');
                }
            });
        }
    });
}

function eliminaReferral(id) {
    Swal.fire({
        title: 'Conferma Eliminazione',
        text: 'Sei sicuro di voler eliminare questo referral?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sì, elimina',
        cancelButtonText: 'Annulla',
        confirmButtonColor: '#d33'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/admin/referral/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Eliminato!', data.message, 'success').then(() => location.reload());
                } else {
                    Swal.fire('Errore', data.message, 'error');
                }
            });
        }
    });
}
</script>
@endpush

@endsection
