@extends('layouts.cliente')

@section('titolo', 'Le Mie Preferenze Privacy')

@section('contenuto')
<div class="max-w-6xl mx-auto p-6">

    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">
            <i class="fas fa-shield-alt mr-3 text-viola-magia"></i>
            Privacy e Consensi
        </h1>
        <p class="text-gray-600 mt-2">
            Gestisci i tuoi consensi e le preferenze privacy in conformità al GDPR
        </p>
    </div>

    <!-- Alert Informativo -->
    <div class="bg-blue-50 border-l-4 border-blue-500 p-6 mb-8 rounded-lg">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <i class="fas fa-info-circle text-blue-500 text-xl"></i>
            </div>
            <div class="ml-4">
                <h3 class="text-sm font-semibold text-blue-900">I Tuoi Diritti GDPR</h3>
                <p class="text-sm text-blue-700 mt-2">
                    In conformità al Regolamento UE 2016/679 (GDPR), hai diritto a:
                </p>
                <ul class="list-disc list-inside text-sm text-blue-700 mt-2 space-y-1">
                    <li>Modificare i tuoi consensi in qualsiasi momento</li>
                    <li>Esportare tutti i tuoi dati personali</li>
                    <li>Richiedere la cancellazione dei tuoi dati (diritto all'oblio)</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Stato Consensi Attuali -->
    <div class="bg-white rounded-lg shadow-lg p-8 mb-8">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">
            <i class="fas fa-clipboard-check mr-2"></i>
            I Tuoi Consensi Attuali
        </h2>

        <form id="form-consensi" class="space-y-6">
            @csrf

            <!-- Privacy Policy (Obbligatorio) -->
            <div class="p-6 bg-gray-50 rounded-lg border-2 border-gray-200">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <i class="fas fa-file-contract text-3xl text-green-600"></i>
                    </div>
                    <div class="ml-4 flex-1">
                        <h3 class="text-lg font-semibold text-gray-900">Privacy Policy</h3>
                        <p class="text-sm text-gray-600 mt-1">
                            Consenso obbligatorio per utilizzare i nostri servizi
                        </p>
                        <div class="mt-4">
                            @if($stato_consensi['privacy_policy'])
                                <span class="inline-flex items-center px-4 py-2 bg-green-100 text-green-800 rounded-full font-medium">
                                    <i class="fas fa-check-circle mr-2"></i>
                                    Consenso Attivo
                                </span>
                            @else
                                <span class="inline-flex items-center px-4 py-2 bg-red-100 text-red-800 rounded-full font-medium">
                                    <i class="fas fa-times-circle mr-2"></i>
                                    Consenso Non Attivo
                                </span>
                            @endif
                        </div>
                        <a href="#" class="text-sm text-viola-magia hover:text-fucsia-magia mt-2 inline-block">
                            <i class="fas fa-external-link-alt mr-1"></i>
                            Leggi la Privacy Policy completa
                        </a>
                    </div>
                </div>
            </div>

            <!-- Marketing (Opzionale) -->
            <div class="p-6 bg-gray-50 rounded-lg border-2 border-gray-200 hover:border-fucsia-magia transition">
                <div class="flex items-start justify-between">
                    <div class="flex items-start flex-1">
                        <div class="flex-shrink-0">
                            <i class="fas fa-bullhorn text-3xl text-blue-600"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-900">Comunicazioni Marketing</h3>
                            <p class="text-sm text-gray-600 mt-1">
                                Ricevi offerte, promozioni e novità sui nostri servizi via email
                            </p>
                            <p class="text-xs text-gray-500 mt-2">
                                Puoi revocare questo consenso in qualsiasi momento
                            </p>
                        </div>
                    </div>
                    <div class="ml-4">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="marketing" value="1"
                                   {{ $stato_consensi['marketing'] ? 'checked' : '' }}
                                   class="sr-only peer">
                            <div class="w-14 h-7 bg-gray-300 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-fucsia-magia/30 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-gradient-to-r peer-checked:from-viola-magia peer-checked:to-fucsia-magia"></div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Profilazione (Opzionale) -->
            <div class="p-6 bg-gray-50 rounded-lg border-2 border-gray-200 hover:border-fucsia-magia transition">
                <div class="flex items-start justify-between">
                    <div class="flex items-start flex-1">
                        <div class="flex-shrink-0">
                            <i class="fas fa-chart-line text-3xl text-purple-600"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-900">Profilazione</h3>
                            <p class="text-sm text-gray-600 mt-1">
                                Analisi delle tue preferenze per offrirti un'esperienza personalizzata
                            </p>
                            <p class="text-xs text-gray-500 mt-2">
                                Consente di ricevere contenuti e offerte su misura per te
                            </p>
                        </div>
                    </div>
                    <div class="ml-4">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="profilazione" value="1"
                                   {{ $stato_consensi['profilazione'] ? 'checked' : '' }}
                                   class="sr-only peer">
                            <div class="w-14 h-7 bg-gray-300 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-fucsia-magia/30 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-gradient-to-r peer-checked:from-viola-magia peer-checked:to-fucsia-magia"></div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Cookie Analytics (Opzionale) -->
            <div class="p-6 bg-gray-50 rounded-lg border-2 border-gray-200 hover:border-fucsia-magia transition">
                <div class="flex items-start justify-between">
                    <div class="flex items-start flex-1">
                        <div class="flex-shrink-0">
                            <i class="fas fa-chart-pie text-3xl text-yellow-600"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-900">Cookie Analytics</h3>
                            <p class="text-sm text-gray-600 mt-1">
                                Cookie per analizzare l'utilizzo del sito e migliorare l'esperienza utente
                            </p>
                            <p class="text-xs text-gray-500 mt-2">
                                Aiutaci a capire come migliorare i nostri servizi
                            </p>
                        </div>
                    </div>
                    <div class="ml-4">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="cookie_analytics" value="1"
                                   {{ $stato_consensi['cookie_analytics'] ? 'checked' : '' }}
                                   class="sr-only peer">
                            <div class="w-14 h-7 bg-gray-300 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-fucsia-magia/30 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-gradient-to-r peer-checked:from-viola-magia peer-checked:to-fucsia-magia"></div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Cookie Marketing (Opzionale) -->
            <div class="p-6 bg-gray-50 rounded-lg border-2 border-gray-200 hover:border-fucsia-magia transition">
                <div class="flex items-start justify-between">
                    <div class="flex items-start flex-1">
                        <div class="flex-shrink-0">
                            <i class="fas fa-cookie-bite text-3xl text-orange-600"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-900">Cookie Marketing</h3>
                            <p class="text-sm text-gray-600 mt-1">
                                Cookie per mostrarti pubblicità personalizzata su altri siti web
                            </p>
                            <p class="text-xs text-gray-500 mt-2">
                                Utilizzati per campagne pubblicitarie mirate
                            </p>
                        </div>
                    </div>
                    <div class="ml-4">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="cookie_marketing" value="1"
                                   {{ $stato_consensi['cookie_marketing'] ? 'checked' : '' }}
                                   class="sr-only peer">
                            <div class="w-14 h-7 bg-gray-300 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-fucsia-magia/30 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-gradient-to-r peer-checked:from-viola-magia peer-checked:to-fucsia-magia"></div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Bottone Salva -->
            <div class="flex justify-end pt-4">
                <button type="submit"
                        class="px-8 py-3 bg-gradient-to-r from-viola-magia to-fucsia-magia text-white font-semibold rounded-lg hover:shadow-lg transition transform hover:-translate-y-0.5">
                    <i class="fas fa-save mr-2"></i>
                    Salva Preferenze
                </button>
            </div>

        </form>
    </div>

    <!-- Storico Consensi -->
    @if($consensi->isNotEmpty())
    <div class="bg-white rounded-lg shadow-lg p-8 mb-8">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">
            <i class="fas fa-history mr-2"></i>
            Storico Modifiche
        </h2>

        <div class="space-y-4">
            @foreach($consensi as $tipo => $consensiTipo)
                <div class="border-l-4 border-gray-300 pl-4 py-2">
                    <h4 class="font-semibold text-gray-800">
                        {{ $consensiTipo->first()->tipo_consenso_label }}
                    </h4>
                    <div class="mt-2 space-y-2">
                        @foreach($consensiTipo->take(3) as $consenso)
                            <div class="flex items-center justify-between text-sm">
                                <div class="flex items-center">
                                    @if($consenso->consenso_dato)
                                        <span class="inline-flex items-center px-2 py-1 bg-green-100 text-green-800 rounded text-xs font-medium mr-3">
                                            <i class="fas fa-check mr-1"></i> Accordato
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-1 bg-red-100 text-red-800 rounded text-xs font-medium mr-3">
                                            <i class="fas fa-times mr-1"></i> Negato
                                        </span>
                                    @endif
                                    <span class="text-gray-600">
                                        {{ $consenso->data_consenso->format('d/m/Y H:i') }}
                                    </span>
                                </div>
                                <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded text-xs">
                                    {{ $consenso->stato }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Azioni GDPR -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        <!-- Esporta Dati -->
        <div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition">
            <div class="text-center">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-green-100 rounded-full mb-4">
                    <i class="fas fa-download text-3xl text-green-600"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Esporta i Tuoi Dati</h3>
                <p class="text-sm text-gray-600 mb-4">
                    Scarica una copia di tutti i tuoi dati personali (Art. 20 GDPR)
                </p>
                <a href="{{ route('cliente.privacy.export') }}"
                   class="inline-flex items-center px-6 py-2 bg-green-500 text-white font-medium rounded-lg hover:bg-green-600 transition">
                    <i class="fas fa-file-download mr-2"></i>
                    Esporta Dati
                </a>
            </div>
        </div>

        <!-- Revoca Tutti i Consensi -->
        <div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition">
            <div class="text-center">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-yellow-100 rounded-full mb-4">
                    <i class="fas fa-ban text-3xl text-yellow-600"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Revoca Tutti i Consensi</h3>
                <p class="text-sm text-gray-600 mb-4">
                    Ritira tutti i consensi opzionali in un clic
                </p>
                <button onclick="revocaTuttiConsensi()"
                        class="inline-flex items-center px-6 py-2 bg-yellow-500 text-white font-medium rounded-lg hover:bg-yellow-600 transition">
                    <i class="fas fa-times-circle mr-2"></i>
                    Revoca Tutto
                </button>
            </div>
        </div>

        <!-- Cancella Account -->
        <div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition">
            <div class="text-center">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-red-100 rounded-full mb-4">
                    <i class="fas fa-user-slash text-3xl text-red-600"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Cancella il Tuo Account</h3>
                <p class="text-sm text-gray-600 mb-4">
                    Richiedi la cancellazione completa dei tuoi dati (Art. 17 GDPR)
                </p>
                <button onclick="richiediCancellazione()"
                        class="inline-flex items-center px-6 py-2 bg-red-500 text-white font-medium rounded-lg hover:bg-red-600 transition">
                    <i class="fas fa-trash-alt mr-2"></i>
                    Richiedi Cancellazione
                </button>
            </div>
        </div>

    </div>

    <!-- Informazioni Legali -->
    <div class="mt-8 p-6 bg-gray-50 rounded-lg border border-gray-200">
        <h3 class="font-semibold text-gray-800 mb-3">
            <i class="fas fa-gavel mr-2"></i>
            Informazioni Legali
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm text-gray-600">
            <a href="#" class="hover:text-viola-magia transition">
                <i class="fas fa-file-contract mr-1"></i> Privacy Policy Completa
            </a>
            <a href="#" class="hover:text-viola-magia transition">
                <i class="fas fa-cookie mr-1"></i> Cookie Policy
            </a>
            <a href="#" class="hover:text-viola-magia transition">
                <i class="fas fa-file-alt mr-1"></i> Termini e Condizioni
            </a>
        </div>
    </div>

</div>

@push('scripts')
<script>
// Salva preferenze consensi
document.getElementById('form-consensi').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const data = {
        _token: formData.get('_token'),
        marketing: formData.get('marketing') ? 1 : 0,
        profilazione: formData.get('profilazione') ? 1 : 0,
        cookie_analytics: formData.get('cookie_analytics') ? 1 : 0,
        cookie_marketing: formData.get('cookie_marketing') ? 1 : 0,
    };

    fetch('{{ route('cliente.privacy.update') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': data._token,
            'Accept': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Preferenze Aggiornate!',
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
    })
    .catch(error => {
        Swal.fire({
            icon: 'error',
            title: 'Errore',
            text: 'Si è verificato un errore durante il salvataggio',
            confirmButtonText: 'OK'
        });
    });
});

// Revoca tutti i consensi
function revocaTuttiConsensi() {
    Swal.fire({
        title: 'Conferma Revoca',
        text: 'Sei sicura di voler revocare tutti i consensi opzionali?',
        icon: 'warning',
        input: 'textarea',
        inputPlaceholder: 'Motivazione (opzionale)',
        showCancelButton: true,
        confirmButtonText: 'Sì, revoca tutto',
        cancelButtonText: 'Annulla',
        confirmButtonColor: '#d33'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('{{ route('cliente.privacy.revoca-all') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    motivazione: result.value || 'Revoca volontaria'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Revocato!', data.message, 'success')
                        .then(() => location.reload());
                } else {
                    Swal.fire('Errore', data.message, 'error');
                }
            });
        }
    });
}

// Richiedi cancellazione account
function richiediCancellazione() {
    Swal.fire({
        title: 'Richiesta Cancellazione Account',
        html: `
            <p class="mb-4">Questa azione richiederà la cancellazione completa dei tuoi dati personali.</p>
            <p class="mb-4 text-sm text-gray-600">Sarai contattata entro 30 giorni per confermare la richiesta.</p>
        `,
        icon: 'warning',
        input: 'textarea',
        inputPlaceholder: 'Motivazione della richiesta (opzionale)',
        showCancelButton: true,
        confirmButtonText: 'Conferma Richiesta',
        cancelButtonText: 'Annulla',
        confirmButtonColor: '#d33'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('{{ route('cliente.privacy.cancellazione') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    motivazione: result.value
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'info',
                        title: 'Richiesta Inviata',
                        text: data.message,
                        confirmButtonText: 'OK'
                    }).then(() => location.reload());
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
