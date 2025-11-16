@extends('layouts.cliente')

@section('titolo', 'Porta un\'Amica - Guadagna Sconti')

@section('contenuto')
<div class="max-w-6xl mx-auto p-6">

    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">
            <i class="fas fa-gift mr-3 text-fucsia-magia"></i>
            Porta un'Amica
        </h1>
        <p class="text-gray-600 mt-2">
            Invita le tue amiche e ricevi fino a 10€ di sconto per ogni conversione!
        </p>
    </div>

    <!-- Banner Promozionale -->
    <div class="bg-gradient-to-r from-viola-magia to-fucsia-magia rounded-lg shadow-lg p-8 mb-8 text-white">
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <h2 class="text-2xl font-bold mb-3">Come Funziona?</h2>
                <div class="space-y-2">
                    <p class="flex items-center">
                        <i class="fas fa-check-circle mr-2"></i>
                        <span>Invita un'amica con il tuo codice personale</span>
                    </p>
                    <p class="flex items-center">
                        <i class="fas fa-check-circle mr-2"></i>
                        <span>La tua amica riceve 15€ di sconto alla registrazione</span>
                    </p>
                    <p class="flex items-center">
                        <i class="fas fa-check-circle mr-2"></i>
                        <span>Tu ricevi 10€ di sconto quando lei completa il primo acquisto!</span>
                    </p>
                </div>
            </div>
            <div class="hidden md:block">
                <div class="bg-white bg-opacity-20 rounded-lg p-6 text-center">
                    <p class="text-sm mb-2">Il Tuo Codice</p>
                    <p class="text-3xl font-bold">{{ $codicePersonale }}</p>
                    <button onclick="copiaCodiceclipboard('{{ $codicePersonale }}')"
                            class="mt-3 px-4 py-2 bg-white text-viola-magia font-medium rounded hover:bg-gray-100 transition">
                        <i class="fas fa-copy mr-2"></i> Copia
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiche -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
            <p class="text-sm text-gray-600">Amiche Invitate</p>
            <p class="text-3xl font-bold text-gray-800">{{ $statistiche['totale_inviti'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-yellow-500">
            <p class="text-sm text-gray-600">In Attesa</p>
            <p class="text-3xl font-bold text-gray-800">{{ $statistiche['in_attesa'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
            <p class="text-sm text-gray-600">Convertite</p>
            <p class="text-3xl font-bold text-gray-800">{{ $statistiche['amiche_convertite'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-fucsia-magia">
            <p class="text-sm text-gray-600">Sconti Guadagnati</p>
            <p class="text-3xl font-bold text-gray-800">€{{ number_format($statistiche['sconti_guadagnati'], 2) }}</p>
        </div>
    </div>

    <!-- Form Invio Invito -->
    <div class="bg-white rounded-lg shadow-lg p-8 mb-8">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">
            <i class="fas fa-envelope mr-2"></i>
            Invia un Invito
        </h2>
        <form id="form-invito" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email Amica *</label>
                    <input type="email" name="email_amica" required
                           placeholder="amica@example.com"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nome Amica (opzionale)</label>
                    <input type="text" name="nome_amica"
                           placeholder="Maria"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Messaggio Personale (opzionale)</label>
                <textarea name="messaggio_personale" rows="3"
                          placeholder="Aggiungi un messaggio personale per la tua amica..."
                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent"></textarea>
            </div>
            <button type="submit"
                    class="w-full px-6 py-3 bg-gradient-to-r from-viola-magia to-fucsia-magia text-white font-semibold rounded-lg hover:shadow-lg transition transform hover:-translate-y-0.5">
                <i class="fas fa-paper-plane mr-2"></i>
                Invia Invito
            </button>
        </form>
    </div>

    <!-- Lista Inviti Inviati -->
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b">
            <h3 class="text-lg font-semibold text-gray-800">
                <i class="fas fa-list mr-2"></i>
                Le Tue Amiche Invitate
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Codice</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stato</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Data Invito</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sconto</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Azioni</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($referrals as $ref)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">{{ $ref->email_invitato }}</div>
                            @if($ref->clienteInvitato)
                                <div class="text-xs text-green-600">
                                    <i class="fas fa-check-circle"></i> Registrata!
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <span class="font-mono text-xs bg-gray-100 px-2 py-1 rounded">{{ $ref->codice_invito }}</span>
                        </td>
                        <td class="px-6 py-4">
                            @if($ref->stato == 'pending')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    <i class="fas fa-clock mr-1"></i> In Attesa
                                </span>
                            @elseif($ref->stato == 'registrato')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    <i class="fas fa-user-check mr-1"></i> Registrata
                                </span>
                            @elseif($ref->stato == 'convertito')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-check-circle mr-1"></i> Convertita
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ $ref->data_invito->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4">
                            @if($ref->stato == 'convertito')
                                <span class="text-green-600 font-bold">+€{{ $ref->sconto_invitante }}</span>
                            @else
                                <span class="text-gray-400">€{{ $ref->sconto_invitante }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm">
                            @if($ref->stato == 'pending')
                                <button onclick="inviaPromemoria({{ $ref->id }})"
                                        class="text-blue-600 hover:text-blue-900 mr-2"
                                        title="Invia promemoria">
                                    <i class="fas fa-bell"></i>
                                </button>
                                <button onclick="annullaInvito({{ $ref->id }})"
                                        class="text-red-600 hover:text-red-900"
                                        title="Annulla invito">
                                    <i class="fas fa-times"></i>
                                </button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-envelope-open-text text-4xl mb-3 text-gray-300"></i>
                            <p>Non hai ancora inviato inviti. Inizia ora!</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Sconti Disponibili -->
    @if($statistiche['sconti_disponibili'] > 0)
    <div class="mt-8 bg-green-50 border-l-4 border-green-500 p-6 rounded-lg">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <i class="fas fa-gift text-green-500 text-2xl"></i>
            </div>
            <div class="ml-4">
                <h3 class="text-lg font-semibold text-green-800">Hai €{{ number_format($statistiche['sconti_disponibili'], 2) }} di sconto disponibile!</h3>
                <p class="text-sm text-green-700 mt-1">Usa il tuo sconto al prossimo acquisto</p>
            </div>
        </div>
    </div>
    @endif

</div>

@push('scripts')
<script>
// Invia invito
document.getElementById('form-invito').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);

    fetch('{{ route('cliente.referral.invia-invito') }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': formData.get('_token'),
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Invito Inviato!',
                text: data.message,
                confirmButtonText: 'OK'
            }).then(() => location.reload());
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Errore',
                text: data.message,
                confirmButtonText: 'OK'
            });
        }
    });
});

function copiaCodiceclipboard(codice) {
    navigator.clipboard.writeText(codice).then(() => {
        Swal.fire({
            icon: 'success',
            title: 'Copiato!',
            text: 'Codice copiato negli appunti',
            timer: 2000,
            showConfirmButton: false
        });
    });
}

function inviaPromemoria(id) {
    Swal.fire({
        title: 'Invia Promemoria',
        text: 'Vuoi inviare un promemoria alla tua amica?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sì, invia',
        cancelButtonText: 'Annulla'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/cliente/referral/${id}/promemoria`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Inviato!', data.message, 'success');
                } else {
                    Swal.fire('Errore', data.message, 'error');
                }
            });
        }
    });
}

function annullaInvito(id) {
    Swal.fire({
        title: 'Annulla Invito',
        text: 'Sei sicura di voler annullare questo invito?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sì, annulla',
        cancelButtonText: 'No',
        confirmButtonColor: '#d33'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/cliente/referral/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Annullato!', data.message, 'success').then(() => location.reload());
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
