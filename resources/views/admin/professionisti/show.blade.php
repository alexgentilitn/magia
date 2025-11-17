@extends('layouts.admin')

@section('titolo', 'Dettaglio Professionista')

@section('contenuto')
<div class="p-6">
    <div class="mb-6">
        <a href="{{ route('admin.professionisti.index') }}" class="text-fucsia-magia hover:text-viola-magia mb-2 inline-block">
            <i class="fas fa-arrow-left mr-2"></i> Torna alla lista
        </a>
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">{{ $professionista->nome }} {{ $professionista->cognome }}</h1>
                <p class="text-gray-600 mt-1">{{ $professionista->titolo_professionale ?? 'Professionista' }}</p>
            </div>
            <div class="flex gap-2">
                <span class="px-3 py-1 rounded-full {{ $professionista->badge_stato }}">
                    {{ ucfirst($professionista->stato) }}
                </span>
                @if($professionista->visibile_pubblico)
                <span class="px-3 py-1 rounded-full bg-blue-100 text-blue-800">
                    <i class="fas fa-eye"></i> Pubblico
                </span>
                @endif
            </div>
        </div>
    </div>

    <!-- Statistiche -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-xs text-gray-600">Lezioni Totali</p>
            <p class="text-2xl font-bold text-gray-800">{{ $statistiche['lezioni_totali'] }}</p>
            <p class="text-xs text-blue-600">{{ $statistiche['lezioni_future'] }} future</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-xs text-gray-600">Programmi</p>
            <p class="text-2xl font-bold text-gray-800">{{ $statistiche['programmi_totali'] }}</p>
            <p class="text-xs text-green-600">{{ $statistiche['programmi_attivi'] }} attivi</p>
        </div>
        @if($professionista->tariffa_oraria)
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-xs text-gray-600">Tariffa Oraria</p>
            <p class="text-2xl font-bold text-gray-800">€{{ number_format($professionista->tariffa_oraria, 2) }}</p>
        </div>
        @endif
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-xs text-gray-600">Certificazioni</p>
            <p class="text-2xl font-bold text-gray-800">
                @if($statistiche['certificazioni_valide'])
                    <i class="fas fa-check-circle text-green-600"></i>
                @else
                    <i class="fas fa-exclamation-circle text-yellow-600"></i>
                @endif
            </p>
            @if($statistiche['certificazioni_scadenza'] > 0)
            <p class="text-xs text-yellow-600">{{ $statistiche['certificazioni_scadenza'] }} in scadenza</p>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Info Principali -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Informazioni Personali -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold mb-4">Informazioni Personali</h2>

                <div class="grid grid-cols-2 gap-4">
                    @if($professionista->email)
                    <div>
                        <p class="text-sm text-gray-600">Email</p>
                        <p class="font-medium">{{ $professionista->email }}</p>
                    </div>
                    @endif

                    @if($professionista->telefono_mobile)
                    <div>
                        <p class="text-sm text-gray-600">Telefono</p>
                        <p class="font-medium">{{ $professionista->telefono_mobile }}</p>
                    </div>
                    @endif

                    @if($professionista->anni_esperienza)
                    <div>
                        <p class="text-sm text-gray-600">Esperienza</p>
                        <p class="font-medium">{{ $professionista->anni_esperienza }} anni</p>
                    </div>
                    @endif

                    <div>
                        <p class="text-sm text-gray-600">Codice Professionista</p>
                        <p class="font-medium">{{ $professionista->codice_professionista }}</p>
                    </div>
                </div>

                @if($professionista->bio)
                <div class="mt-4 pt-4 border-t">
                    <p class="text-sm text-gray-600 mb-1">Biografia</p>
                    <p class="text-gray-800">{{ $professionista->bio }}</p>
                </div>
                @endif
            </div>

            <!-- Prossime Lezioni -->
            @if($prossimeLezioni->count() > 0)
            <div class="bg-white rounded-lg shadow">
                <div class="p-4 border-b">
                    <h2 class="text-lg font-semibold">Prossime Lezioni</h2>
                </div>
                <div class="p-4">
                    <div class="space-y-3">
                        @foreach($prossimeLezioni as $lezione)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex-1">
                                <p class="font-medium text-gray-800">{{ $lezione->titolo }}</p>
                                <p class="text-sm text-gray-600">
                                    <i class="far fa-calendar mr-1"></i>
                                    {{ $lezione->data->format('d/m/Y') }} - {{ substr($lezione->ora_inizio, 0, 5) }}
                                    @if($lezione->sede)
                                    <span class="ml-2">
                                        <i class="fas fa-map-marker-alt mr-1"></i>{{ $lezione->sede->nome }}
                                    </span>
                                    @endif
                                </p>
                            </div>
                            <a href="{{ route('admin.lezioni.show', $lezione->id) }}" class="text-blue-600 hover:text-blue-800">
                                <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Azioni Rapide -->
        <div class="space-y-4">
            <!-- Foto Profilo -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold mb-4">Foto Profilo</h2>
                <div class="text-center mb-4">
                    @if($professionista->foto_profilo)
                    <img src="{{ asset('storage/' . $professionista->foto_profilo) }}" alt="Foto Profilo" class="w-32 h-32 rounded-full mx-auto object-cover mb-3">
                    <form method="POST" action="{{ route('admin.professionisti.elimina-foto', $professionista->id) }}" class="inline" id="elimina-foto-form">
                        @csrf
                        @method('DELETE')
                        <button type="button" onclick="confermaEliminazione('elimina-foto-form', 'Eliminare la foto profilo?', 'L\'immagine sarà rimossa definitivamente.')" class="text-sm text-red-600 hover:text-red-800">
                            <i class="fas fa-trash mr-1"></i> Rimuovi foto
                        </button>
                    </form>
                    @else
                    <div class="w-32 h-32 rounded-full mx-auto bg-gradient-to-br from-fucsia-magia to-viola-magia flex items-center justify-center text-white text-4xl font-bold mb-3">
                        {{ substr($professionista->nome, 0, 1) }}{{ substr($professionista->cognome, 0, 1) }}
                    </div>
                    <p class="text-sm text-gray-500">Nessuna foto profilo</p>
                    @endif
                </div>
                <form method="POST" action="{{ route('admin.professionisti.upload-foto', $professionista->id) }}" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <input type="file" name="foto_profilo" accept="image/jpeg,image/png,image/jpg" required class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-fucsia-magia file:text-white hover:file:bg-viola-magia">
                        <p class="text-xs text-gray-500 mt-1">Formati: JPG, PNG (max 2MB)</p>
                    </div>
                    <button type="submit" class="w-full px-4 py-2 bg-fucsia-magia text-white rounded-lg hover:bg-viola-magia">
                        <i class="fas fa-upload mr-2"></i> Carica Foto
                    </button>
                </form>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold mb-4">Azioni</h2>
                <div class="space-y-2">
                    @if($professionista->stato === 'pending')
                    <div class="mb-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                        <p class="text-sm font-semibold text-yellow-800 mb-2">
                            <i class="fas fa-clock mr-2"></i> Professionista in attesa di approvazione
                        </p>
                        <div class="flex gap-2">
                            <form method="POST" action="{{ route('admin.professionisti.approva', $professionista->id) }}" class="flex-1" id="approva-form">
                                @csrf
                                <button type="button" onclick="confermaAzione('approva-form', 'Approvare questo professionista?', 'Il professionista riceverà una email di conferma e potrà accedere al sistema.', 'Sì, approva')" class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                                    <i class="fas fa-check mr-2"></i> Approva
                                </button>
                            </form>
                            <button type="button" onclick="mostraModalRifiuto()" class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                                <i class="fas fa-times mr-2"></i> Rifiuta
                            </button>
                        </div>
                    </div>
                    @endif

                    <a href="{{ route('admin.professionisti.edit', $professionista->id) }}" class="block w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-center">
                        <i class="fas fa-edit mr-2"></i> Modifica
                    </a>

                    <a href="{{ route('admin.professionisti.certificazioni', $professionista->id) }}" class="block w-full px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 text-center">
                        <i class="fas fa-certificate mr-2"></i> Gestisci Certificazioni
                    </a>

                    <a href="{{ route('admin.professionisti.disponibilita', $professionista->id) }}" class="block w-full px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-center">
                        <i class="fas fa-calendar-check mr-2"></i> Gestisci Disponibilità
                    </a>

                    <a href="{{ route('admin.professionisti.documenti.index', $professionista->id) }}" class="block w-full px-4 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700 text-center">
                        <i class="fas fa-folder-open mr-2"></i> Gestisci Documenti
                    </a>

                    <a href="{{ route('admin.professionisti.galleria.index', $professionista->id) }}" class="block w-full px-4 py-2 bg-pink-600 text-white rounded-lg hover:bg-pink-700 text-center">
                        <i class="fas fa-images mr-2"></i> Galleria Foto
                    </a>

                    <form method="POST" action="{{ route('admin.professionisti.reset-password', $professionista->id) }}" class="block" id="reset-password-form">
                        @csrf
                        <button type="button" onclick="confermaAzione('reset-password-form', 'Resettare la password?', 'Verrà generata una nuova password temporanea per {{ $professionista->nome }} {{ $professionista->cognome }}.', 'Sì, reset password')" class="w-full px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700">
                            <i class="fas fa-key mr-2"></i> Reset Password
                        </button>
                    </form>

                    <form method="POST" action="{{ route('admin.professionisti.destroy', $professionista->id) }}" class="block" id="delete-professionista-form">
                        @csrf
                        @method('DELETE')
                        <button type="button" onclick="confermaEliminazione('delete-professionista-form', 'Eliminare il professionista?', 'Il professionista {{ $professionista->nome }} {{ $professionista->cognome }} sarà eliminato definitivamente. Le lezioni esistenti non saranno eliminate.')" class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                            <i class="fas fa-trash mr-2"></i> Elimina
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Rifiuto -->
<div id="modal-rifiuto" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-lg max-w-md w-full mx-4">
        <div class="p-6">
            <h3 class="text-xl font-bold mb-4">Rifiuta Professionista</h3>
            <form method="POST" action="{{ route('admin.professionisti.rifiuta', $professionista->id) }}" id="rifiuta-form">
                @csrf
                <div class="mb-4">
                    <label for="motivo_rifiuto" class="block text-sm font-medium text-gray-700 mb-2">
                        Motivo del rifiuto (opzionale)
                    </label>
                    <textarea name="motivo_rifiuto" id="motivo_rifiuto" rows="4"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-fucsia-magia focus:border-fucsia-magia"
                        placeholder="Es: Mancanza di certificazioni richieste..."></textarea>
                </div>
                <div class="flex gap-2">
                    <button type="button" onclick="chiudiModalRifiuto()" class="flex-1 px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                        Annulla
                    </button>
                    <button type="submit" class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                        Conferma Rifiuto
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function mostraModalRifiuto() {
    document.getElementById('modal-rifiuto').classList.remove('hidden');
    document.getElementById('modal-rifiuto').classList.add('flex');
}

function chiudiModalRifiuto() {
    document.getElementById('modal-rifiuto').classList.add('hidden');
    document.getElementById('modal-rifiuto').classList.remove('flex');
}

// Chiudi modal cliccando fuori
document.getElementById('modal-rifiuto')?.addEventListener('click', function(e) {
    if (e.target === this) {
        chiudiModalRifiuto();
    }
});
</script>

@endsection
