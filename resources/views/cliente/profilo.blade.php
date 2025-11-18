@extends('layouts.cliente')

@section('titolo', 'Il Mio Profilo')

@section('contenuto')

<!-- Back Button (solo mobile) -->
<a href="{{ route('cliente.dashboard') }}" class="inline-flex items-center text-viola-magia hover:text-fucsia-magia mb-6 font-medium md:hidden">
    <i class="fas fa-arrow-left mr-2"></i> Torna alla Dashboard
</a>

<!-- Header Profilo -->
<div class="gradient-magia rounded-2xl p-8 text-white text-center mb-6">
    <div class="w-24 h-24 bg-white rounded-full flex items-center justify-center mx-auto mb-4">
        <span class="text-5xl font-bold text-fucsia-magia">{{ strtoupper(substr(Auth::user()->nome, 0, 1)) }}</span>
    </div>
    <h1 class="text-3xl font-bold mb-2">{{ Auth::user()->nome }} {{ Auth::user()->cognome ?? '' }}</h1>
    <p class="text-white text-opacity-90">{{ Auth::user()->email }}</p>
    <div class="mt-4 inline-block bg-white bg-opacity-20 px-4 py-2 rounded-lg">
        <i class="fas fa-crown mr-2"></i>
        <span class="font-semibold">Cliente MA.GIA DONNA</span>
    </div>
</div>

<!-- Tabs Navigation -->
<div class="bg-white rounded-2xl shadow-sm mb-6 overflow-hidden">
    <div class="flex border-b overflow-x-auto">
        <button onclick="switchTab('dati')" id="tab-dati" class="tab-button flex-1 px-4 py-3 font-semibold border-b-2 border-fucsia-magia text-fucsia-magia">
            <i class="fas fa-user mr-2"></i>
            <span class="hidden sm:inline">Dati Personali</span>
            <span class="sm:hidden">Dati</span>
        </button>
        <button onclick="switchTab('misure')" id="tab-misure" class="tab-button flex-1 px-4 py-3 font-semibold text-gray-500 hover:text-fucsia-magia">
            <i class="fas fa-ruler mr-2"></i>
            <span class="hidden sm:inline">Misure & Obiettivi</span>
            <span class="sm:hidden">Misure</span>
        </button>
        <button onclick="switchTab('programma')" id="tab-programma" class="tab-button flex-1 px-4 py-3 font-semibold text-gray-500 hover:text-fucsia-magia">
            <i class="fas fa-calendar-check mr-2"></i>
            <span class="hidden sm:inline">Il Mio Programma</span>
            <span class="sm:hidden">Programma</span>
        </button>
        <button onclick="switchTab('impostazioni')" id="tab-impostazioni" class="tab-button flex-1 px-4 py-3 font-semibold text-gray-500 hover:text-fucsia-magia">
            <i class="fas fa-cog mr-2"></i>
            <span class="hidden sm:inline">Impostazioni</span>
            <span class="sm:hidden">Settings</span>
        </button>
    </div>
</div>

<!-- Tab: Dati Personali -->
<div id="content-dati" class="tab-content">
    <div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-id-card text-fucsia-magia mr-2"></i>
                Informazioni Anagrafiche
            </h2>
            <button onclick="editMode('dati')" class="text-fucsia-magia hover:text-viola-magia font-semibold">
                <i class="fas fa-edit mr-1"></i> Modifica
            </button>
        </div>

        <div class="grid md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-semibold text-gray-600 mb-1">Nome</label>
                <p class="text-lg text-gray-800">{{ Auth::user()->nome ?? 'Non specificato' }}</p>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-600 mb-1">Cognome</label>
                <p class="text-lg text-gray-800">{{ Auth::user()->cognome ?? 'Non specificato' }}</p>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-600 mb-1">Email</label>
                <p class="text-lg text-gray-800">{{ Auth::user()->email }}</p>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-600 mb-1">Telefono</label>
                <p class="text-lg text-gray-800">{{ Auth::user()->telefono ?? 'Non specificato' }}</p>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-600 mb-1">Data di Nascita</label>
                <p class="text-lg text-gray-800">{{ Auth::user()->data_nascita ? Auth::user()->data_nascita->format('d/m/Y') : 'Non specificata' }}</p>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-600 mb-1">Codice Cliente</label>
                <p class="text-lg text-gray-800 font-mono">{{ Auth::user()->codice_cliente ?? 'Non assegnato' }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">
            <i class="fas fa-map-marker-alt text-fucsia-magia mr-2"></i>
            Indirizzo
        </h2>

        <div class="grid md:grid-cols-2 gap-6">
            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-gray-600 mb-1">Via</label>
                <p class="text-lg text-gray-800">{{ Auth::user()->indirizzo_via ?? 'Non specificato' }}</p>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-600 mb-1">Città</label>
                <p class="text-lg text-gray-800">{{ Auth::user()->indirizzo_citta ?? 'Non specificata' }}</p>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-600 mb-1">CAP</label>
                <p class="text-lg text-gray-800">{{ Auth::user()->indirizzo_cap ?? 'Non specificato' }}</p>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-600 mb-1">Provincia</label>
                <p class="text-lg text-gray-800">{{ Auth::user()->indirizzo_provincia ?? 'Non specificata' }}</p>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-600 mb-1">Nazione</label>
                <p class="text-lg text-gray-800">{{ Auth::user()->indirizzo_nazione ?? 'Italia' }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Tab: Misure & Obiettivi -->
<div id="content-misure" class="tab-content hidden">
    <div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-weight text-arancio-magia mr-2"></i>
                Dati Fisici Attuali
            </h2>
            <button onclick="editMode('misure')" class="text-fucsia-magia hover:text-viola-magia font-semibold">
                <i class="fas fa-edit mr-1"></i> Aggiorna Misure
            </button>
        </div>

        <div class="grid md:grid-cols-3 gap-6">
            <div class="bg-gradient-to-br from-arancio-magia to-orange-400 rounded-xl p-6 text-white">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium opacity-90">Peso</span>
                    <i class="fas fa-weight-hanging text-2xl opacity-75"></i>
                </div>
                <p class="text-3xl font-bold">{{ Auth::user()->peso_iniziale ?? '—' }} <span class="text-lg">kg</span></p>
            </div>

            <div class="bg-gradient-to-br from-fucsia-magia to-pink-400 rounded-xl p-6 text-white">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium opacity-90">Altezza</span>
                    <i class="fas fa-arrows-alt-v text-2xl opacity-75"></i>
                </div>
                <p class="text-3xl font-bold">{{ Auth::user()->altezza ?? '—' }} <span class="text-lg">cm</span></p>
            </div>

            <div class="bg-gradient-to-br from-viola-magia to-purple-400 rounded-xl p-6 text-white">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium opacity-90">IMC</span>
                    <i class="fas fa-calculator text-2xl opacity-75"></i>
                </div>
                <p class="text-3xl font-bold">
                    @if(Auth::user()->peso_iniziale && Auth::user()->altezza)
                        {{ number_format(Auth::user()->peso_iniziale / pow(Auth::user()->altezza / 100, 2), 1) }}
                    @else
                        —
                    @endif
                </p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">
            <i class="fas fa-ruler-combined text-fucsia-magia mr-2"></i>
            Circonferenze
        </h2>

        <div class="grid md:grid-cols-2 gap-6">
            <div class="flex items-center space-x-4 p-4 bg-gray-50 rounded-lg">
                <div class="w-12 h-12 bg-fucsia-magia bg-opacity-10 rounded-full flex items-center justify-center">
                    <i class="fas fa-circle text-fucsia-magia"></i>
                </div>
                <div class="flex-1">
                    <p class="text-sm text-gray-600">Vita</p>
                    <p class="text-xl font-bold text-gray-800">{{ Auth::user()->circonferenza_vita ?? '—' }} cm</p>
                </div>
            </div>

            <div class="flex items-center space-x-4 p-4 bg-gray-50 rounded-lg">
                <div class="w-12 h-12 bg-fucsia-magia bg-opacity-10 rounded-full flex items-center justify-center">
                    <i class="fas fa-circle text-fucsia-magia"></i>
                </div>
                <div class="flex-1">
                    <p class="text-sm text-gray-600">Fianchi</p>
                    <p class="text-xl font-bold text-gray-800">{{ Auth::user()->circonferenza_fianchi ?? '—' }} cm</p>
                </div>
            </div>

            <div class="flex items-center space-x-4 p-4 bg-gray-50 rounded-lg">
                <div class="w-12 h-12 bg-arancio-magia bg-opacity-10 rounded-full flex items-center justify-center">
                    <i class="fas fa-circle text-arancio-magia"></i>
                </div>
                <div class="flex-1">
                    <p class="text-sm text-gray-600">Braccia</p>
                    <p class="text-xl font-bold text-gray-800">{{ Auth::user()->circonferenza_braccia ?? '—' }} cm</p>
                </div>
            </div>

            <div class="flex items-center space-x-4 p-4 bg-gray-50 rounded-lg">
                <div class="w-12 h-12 bg-arancio-magia bg-opacity-10 rounded-full flex items-center justify-center">
                    <i class="fas fa-circle text-arancio-magia"></i>
                </div>
                <div class="flex-1">
                    <p class="text-sm text-gray-600">Cosce</p>
                    <p class="text-xl font-bold text-gray-800">{{ Auth::user()->circonferenza_cosce ?? '—' }} cm</p>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">
            <i class="fas fa-bullseye text-viola-magia mr-2"></i>
            I Miei Obiettivi
        </h2>

        <div class="prose max-w-none">
            <p class="text-gray-600">
                {{ Auth::user()->obiettivi_personali ?? 'Non hai ancora definito i tuoi obiettivi. Clicca su "Aggiorna Misure" per aggiungerli!' }}
            </p>
        </div>
    </div>
</div>

<!-- Tab: Il Mio Programma -->
<div id="content-programma" class="tab-content hidden">
    <div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">
            <i class="fas fa-fire text-arancio-magia mr-2"></i>
            Programma Attuale
        </h2>

        @if(Auth::user()->programma_attuale)
            <div class="bg-gradient-to-r from-viola-magia to-fucsia-magia rounded-xl p-6 text-white mb-6">
                <h3 class="text-2xl font-bold mb-2">{{ Auth::user()->programma_attuale }}</h3>
                <p class="text-white text-opacity-90">Stato: <span class="font-semibold">{{ ucfirst(Auth::user()->stato_programma ?? 'Attivo') }}</span></p>
            </div>

            <div class="grid md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-600 mb-1">Data Inizio</label>
                    <p class="text-lg text-gray-800">
                        <i class="far fa-calendar-alt text-fucsia-magia mr-2"></i>
                        {{ Auth::user()->inizio_programma ? Auth::user()->inizio_programma->format('d/m/Y') : 'Non specificata' }}
                    </p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-600 mb-1">Data Fine Prevista</label>
                    <p class="text-lg text-gray-800">
                        <i class="far fa-calendar-check text-fucsia-magia mr-2"></i>
                        {{ Auth::user()->fine_programma ? Auth::user()->fine_programma->format('d/m/Y') : 'Non specificata' }}
                    </p>
                </div>
            </div>
        @else
            <div class="text-center py-12">
                <div class="text-6xl mb-4">🎯</div>
                <h3 class="text-xl font-bold text-gray-800 mb-2">Nessun Programma Attivo</h3>
                <p class="text-gray-600 mb-6">Non hai ancora un programma assegnato. Contatta la tua coach per iniziare!</p>
                <a href="{{ route('messaging.index') }}" class="inline-block bg-fucsia-magia text-white px-6 py-3 rounded-xl font-bold hover:shadow-xl transition">
                    <i class="fas fa-comments mr-2"></i>
                    Contatta la Coach
                </a>
            </div>
        @endif
    </div>

    <div class="bg-white rounded-2xl shadow-sm p-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">
            <i class="fas fa-file-medical text-fucsia-magia mr-2"></i>
            Certificato Medico
        </h2>

        @if(Auth::user()->certificato_medico_valido)
            <div class="flex items-start space-x-4 p-4 bg-green-50 border-l-4 border-green-500 rounded">
                <i class="fas fa-check-circle text-green-500 text-2xl mt-1"></i>
                <div>
                    <h3 class="font-bold text-green-800">Certificato Valido</h3>
                    <p class="text-sm text-green-700">
                        Scadenza: {{ Auth::user()->certificato_scadenza ? Auth::user()->certificato_scadenza->format('d/m/Y') : 'Non specificata' }}
                    </p>
                </div>
            </div>
        @else
            <div class="flex items-start space-x-4 p-4 bg-yellow-50 border-l-4 border-yellow-500 rounded">
                <i class="fas fa-exclamation-triangle text-yellow-500 text-2xl mt-1"></i>
                <div>
                    <h3 class="font-bold text-yellow-800">Certificato Mancante o Scaduto</h3>
                    <p class="text-sm text-yellow-700">Ricordati di caricare il tuo certificato medico per continuare le attività.</p>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Tab: Impostazioni -->
<div id="content-impostazioni" class="tab-content hidden">
    <div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">
            <i class="fas fa-shield-alt text-viola-magia mr-2"></i>
            Privacy e Consensi
        </h2>

        <div class="space-y-4">
            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                <div>
                    <h3 class="font-semibold text-gray-800">Consenso Privacy</h3>
                    <p class="text-sm text-gray-600">Trattamento dati personali</p>
                </div>
                <div>
                    @if(Auth::user()->consenso_privacy)
                        <span class="inline-block bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-semibold">
                            <i class="fas fa-check mr-1"></i> Accettato
                        </span>
                    @else
                        <span class="inline-block bg-red-100 text-red-800 px-3 py-1 rounded-full text-sm font-semibold">
                            <i class="fas fa-times mr-1"></i> Non accettato
                        </span>
                    @endif
                </div>
            </div>

            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                <div>
                    <h3 class="font-semibold text-gray-800">Consenso Marketing</h3>
                    <p class="text-sm text-gray-600">Ricezione newsletter e promozioni</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" {{ Auth::user()->consenso_marketing ? 'checked' : '' }} class="sr-only peer">
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-fucsia-magia peer-focus:ring-opacity-30 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-fucsia-magia"></div>
                </label>
            </div>

            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                <div>
                    <h3 class="font-semibold text-gray-800">Consenso Foto/Video</h3>
                    <p class="text-sm text-gray-600">Utilizzo immagini per comunicazione</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" {{ Auth::user()->consenso_foto ? 'checked' : '' }} class="sr-only peer">
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-fucsia-magia peer-focus:ring-opacity-30 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-fucsia-magia"></div>
                </label>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">
            <i class="fas fa-key text-fucsia-magia mr-2"></i>
            Sicurezza Account
        </h2>

        <div class="space-y-4">
            <button class="w-full flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                <div class="flex items-center space-x-3">
                    <i class="fas fa-lock text-fucsia-magia"></i>
                    <span class="font-semibold text-gray-800">Cambia Password</span>
                </div>
                <i class="fas fa-chevron-right text-gray-400"></i>
            </button>

            <button class="w-full flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                <div class="flex items-center space-x-3">
                    <i class="fas fa-envelope text-fucsia-magia"></i>
                    <span class="font-semibold text-gray-800">Modifica Email</span>
                </div>
                <i class="fas fa-chevron-right text-gray-400"></i>
            </button>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm p-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">
            <i class="fas fa-bell text-arancio-magia mr-2"></i>
            Notifiche
        </h2>

        <div class="space-y-4">
            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                <div>
                    <h3 class="font-semibold text-gray-800">Notifiche Email</h3>
                    <p class="text-sm text-gray-600">Ricevi notifiche via email</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" checked class="sr-only peer">
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-arancio-magia peer-focus:ring-opacity-30 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-arancio-magia"></div>
                </label>
            </div>

            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                <div>
                    <h3 class="font-semibold text-gray-800">Notifiche Push</h3>
                    <p class="text-sm text-gray-600">Ricevi notifiche sul dispositivo</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" checked class="sr-only peer">
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-arancio-magia peer-focus:ring-opacity-30 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-arancio-magia"></div>
                </label>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function switchTab(tabName) {
    // Nascondi tutti i contenuti
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.add('hidden');
    });

    // Rimuovi stile attivo da tutti i bottoni
    document.querySelectorAll('.tab-button').forEach(button => {
        button.classList.remove('border-fucsia-magia', 'text-fucsia-magia');
        button.classList.add('text-gray-500');
    });

    // Mostra il contenuto selezionato
    document.getElementById('content-' + tabName).classList.remove('hidden');

    // Aggiungi stile attivo al bottone selezionato
    const activeButton = document.getElementById('tab-' + tabName);
    activeButton.classList.remove('text-gray-500');
    activeButton.classList.add('border-fucsia-magia', 'text-fucsia-magia');
}

function editMode(section) {
    alert('Funzionalità di modifica in arrivo! Per ora contatta la tua coach per aggiornare i dati.');
}
</script>
@endpush

@endsection
