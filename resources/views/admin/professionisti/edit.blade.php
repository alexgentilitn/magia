@extends('layouts.admin')

@section('titolo', 'Modifica Professionista')

@section('contenuto')
<div class="p-6">
    <div class="mb-6">
        <a href="{{ route('admin.professionisti.show', $professionista->id) }}" class="text-fucsia-magia hover:text-viola-magia mb-2 inline-block">
            <i class="fas fa-arrow-left mr-2"></i> Torna al dettaglio
        </a>
        <h1 class="text-3xl font-bold text-gray-800">Modifica: {{ $professionista->nome }} {{ $professionista->cognome }}</h1>
    </div>

    <form method="POST" action="{{ route('admin.professionisti.update', $professionista->id) }}" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- SEZIONE 1: Dati Anagrafici -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4 border-b pb-2">
                <i class="fas fa-user mr-2 text-fucsia-magia"></i> Dati Anagrafici
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nome *</label>
                    <input type="text" name="nome" required value="{{ old('nome', $professionista->nome) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Cognome *</label>
                    <input type="text" name="cognome" required value="{{ old('cognome', $professionista->cognome) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Codice Fiscale</label>
                    <input type="text" name="codice_fiscale" value="{{ old('codice_fiscale', $professionista->codice_fiscale) }}" maxlength="16" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent uppercase">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Data di Nascita</label>
                    <input type="date" name="data_nascita" value="{{ old('data_nascita', $professionista->data_nascita?->format('Y-m-d')) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Sesso</label>
                    <select name="sesso" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent">
                        <option value="">Seleziona...</option>
                        <option value="F" {{ old('sesso', $professionista->sesso) == 'F' ? 'selected' : '' }}>Femminile</option>
                        <option value="M" {{ old('sesso', $professionista->sesso) == 'M' ? 'selected' : '' }}>Maschile</option>
                        <option value="Altro" {{ old('sesso', $professionista->sesso) == 'Altro' ? 'selected' : '' }}>Altro</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- SEZIONE 2: Contatti e Indirizzo -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4 border-b pb-2">
                <i class="fas fa-address-book mr-2 text-fucsia-magia"></i> Contatti e Indirizzo
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                    <input type="email" name="email" required value="{{ old('email', $professionista->email) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Telefono Mobile</label>
                    <input type="text" name="telefono_mobile" value="{{ old('telefono_mobile', $professionista->telefono_mobile) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Indirizzo</label>
                    <input type="text" name="indirizzo" value="{{ old('indirizzo', $professionista->indirizzo) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Città</label>
                    <input type="text" name="citta" value="{{ old('citta', $professionista->citta) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Provincia</label>
                    <input type="text" name="provincia" value="{{ old('provincia', $professionista->provincia) }}" maxlength="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent uppercase">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">CAP</label>
                    <input type="text" name="cap" value="{{ old('cap', $professionista->cap) }}" maxlength="5" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent">
                </div>
            </div>
        </div>

        <!-- SEZIONE 3: Dati Professionali -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4 border-b pb-2">
                <i class="fas fa-briefcase mr-2 text-fucsia-magia"></i> Dati Professionali
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Titolo Professionale</label>
                    <input type="text" name="titolo_professionale" value="{{ old('titolo_professionale', $professionista->titolo_professionale) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Anni di Esperienza</label>
                    <input type="number" name="anni_esperienza" min="0" value="{{ old('anni_esperienza', $professionista->anni_esperienza) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Partita IVA</label>
                    <input type="text" name="partita_iva" value="{{ old('partita_iva', $professionista->partita_iva) }}" maxlength="20" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tipo Contratto</label>
                    <select name="tipo_contratto" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent">
                        <option value="">Seleziona...</option>
                        <option value="dipendente" {{ old('tipo_contratto', $professionista->tipo_contratto) == 'dipendente' ? 'selected' : '' }}>Dipendente</option>
                        <option value="collaboratore" {{ old('tipo_contratto', $professionista->tipo_contratto) == 'collaboratore' ? 'selected' : '' }}>Collaboratore</option>
                        <option value="freelance" {{ old('tipo_contratto', $professionista->tipo_contratto) == 'freelance' ? 'selected' : '' }}>Freelance</option>
                        <option value="altro" {{ old('tipo_contratto', $professionista->tipo_contratto) == 'altro' ? 'selected' : '' }}>Altro</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Data Assunzione/Inizio Collaborazione</label>
                    <input type="date" name="data_assunzione" value="{{ old('data_assunzione', $professionista->data_assunzione?->format('Y-m-d')) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Biografia Professionale</label>
                    <textarea name="bio" rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent">{{ old('bio', $professionista->bio) }}</textarea>
                </div>
            </div>
        </div>

        <!-- SEZIONE 4: Tariffe -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4 border-b pb-2">
                <i class="fas fa-euro-sign mr-2 text-fucsia-magia"></i> Tariffe
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tariffa Oraria (€)</label>
                    <input type="number" name="tariffa_oraria" min="0" step="0.01" value="{{ old('tariffa_oraria', $professionista->tariffa_oraria) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tariffa Lezione Gruppo (€)</label>
                    <input type="number" name="tariffa_lezione_gruppo" min="0" step="0.01" value="{{ old('tariffa_lezione_gruppo', $professionista->tariffa_lezione_gruppo) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tariffa Lezione Privata (€)</label>
                    <input type="number" name="tariffa_lezione_privata" min="0" step="0.01" value="{{ old('tariffa_lezione_privata', $professionista->tariffa_lezione_privata) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent">
                </div>
            </div>
        </div>

        <!-- SEZIONE 5: Specializzazioni e Qualifiche -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4 border-b pb-2">
                <i class="fas fa-star mr-2 text-fucsia-magia"></i> Specializzazioni e Qualifiche
            </h2>
            <div class="space-y-4">
                <!-- Specializzazioni -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Specializzazioni</label>
                    <div id="specializzazioni-container" class="flex flex-wrap gap-2 mb-2">
                        @if($professionista->specializzazioni)
                            @foreach($professionista->specializzazioni as $index => $spec)
                            <span class="px-3 py-1 bg-fucsia-magia text-white rounded-full text-sm flex items-center gap-2">
                                {{ $spec }}
                                <button type="button" onclick="rimuoviTag('specializzazioni', {{ $index }})" class="text-white hover:text-red-200">
                                    <i class="fas fa-times"></i>
                                </button>
                                <input type="hidden" name="specializzazioni[]" value="{{ $spec }}">
                            </span>
                            @endforeach
                        @endif
                    </div>
                    <div class="flex gap-2">
                        <input type="text" id="nuova-specializzazione" placeholder="Es: Fitness, Yoga, Pilates..." class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent">
                        <button type="button" onclick="aggiungiTag('specializzazioni', 'nuova-specializzazione')" class="px-4 py-2 bg-fucsia-magia text-white rounded-lg hover:bg-viola-magia">
                            <i class="fas fa-plus mr-1"></i> Aggiungi
                        </button>
                    </div>
                </div>

                <!-- Qualifiche -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Qualifiche e Titoli di Studio</label>
                    <div id="qualifiche-container" class="flex flex-wrap gap-2 mb-2">
                        @if($professionista->qualifiche)
                            @foreach($professionista->qualifiche as $index => $qual)
                            <span class="px-3 py-1 bg-viola-magia text-white rounded-full text-sm flex items-center gap-2">
                                {{ $qual }}
                                <button type="button" onclick="rimuoviTag('qualifiche', {{ $index }})" class="text-white hover:text-red-200">
                                    <i class="fas fa-times"></i>
                                </button>
                                <input type="hidden" name="qualifiche[]" value="{{ $qual }}">
                            </span>
                            @endforeach
                        @endif
                    </div>
                    <div class="flex gap-2">
                        <input type="text" id="nuova-qualifica" placeholder="Es: Laurea in Scienze Motorie..." class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent">
                        <button type="button" onclick="aggiungiTag('qualifiche', 'nuova-qualifica')" class="px-4 py-2 bg-viola-magia text-white rounded-lg hover:bg-viola-magia">
                            <i class="fas fa-plus mr-1"></i> Aggiungi
                        </button>
                    </div>
                </div>

                <!-- Lingue Parlate -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Lingue Parlate</label>
                    <div id="lingue-container" class="flex flex-wrap gap-2 mb-2">
                        @if($professionista->lingue_parlate)
                            @foreach($professionista->lingue_parlate as $index => $lingua)
                            <span class="px-3 py-1 bg-blue-500 text-white rounded-full text-sm flex items-center gap-2">
                                {{ $lingua }}
                                <button type="button" onclick="rimuoviTag('lingue', {{ $index }})" class="text-white hover:text-red-200">
                                    <i class="fas fa-times"></i>
                                </button>
                                <input type="hidden" name="lingue_parlate[]" value="{{ $lingua }}">
                            </span>
                            @endforeach
                        @endif
                    </div>
                    <div class="flex gap-2">
                        <input type="text" id="nuova-lingua" placeholder="Es: Italiano, Inglese, Spagnolo..." class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent">
                        <button type="button" onclick="aggiungiTag('lingue', 'nuova-lingua')" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                            <i class="fas fa-plus mr-1"></i> Aggiungi
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- SEZIONE 6: Disponibilità Temporale -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4 border-b pb-2">
                <i class="fas fa-calendar-alt mr-2 text-fucsia-magia"></i> Disponibilità Temporale
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Disponibile Da</label>
                    <input type="date" name="disponibile_da" value="{{ old('disponibile_da', $professionista->disponibile_da?->format('Y-m-d')) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Disponibile Fino</label>
                    <input type="date" name="disponibile_fino" value="{{ old('disponibile_fino', $professionista->disponibile_fino?->format('Y-m-d')) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent">
                    <p class="text-xs text-gray-500 mt-1">Lascia vuoto se disponibilità a tempo indeterminato</p>
                </div>
            </div>
        </div>

        <!-- SEZIONE 7: Social Media e Web -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4 border-b pb-2">
                <i class="fas fa-share-alt mr-2 text-fucsia-magia"></i> Profili Online e Social Media
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2"><i class="fas fa-globe mr-1"></i> Sito Web</label>
                    <input type="url" name="sito_web" value="{{ old('sito_web', $professionista->sito_web) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2"><i class="fab fa-instagram mr-1"></i> Instagram</label>
                    <input type="text" name="instagram" value="{{ old('instagram', $professionista->instagram) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2"><i class="fab fa-facebook mr-1"></i> Facebook</label>
                    <input type="url" name="facebook" value="{{ old('facebook', $professionista->facebook) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2"><i class="fab fa-linkedin mr-1"></i> LinkedIn</label>
                    <input type="url" name="linkedin" value="{{ old('linkedin', $professionista->linkedin) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2"><i class="fab fa-tiktok mr-1"></i> TikTok</label>
                    <input type="text" name="tiktok" value="{{ old('tiktok', $professionista->tiktok) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2"><i class="fab fa-youtube mr-1"></i> Video Presentazione (URL)</label>
                    <input type="url" name="video_presentazione" value="{{ old('video_presentazione', $professionista->video_presentazione) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent">
                </div>
            </div>
        </div>

        <!-- SEZIONE 8: Stato e Visibilità -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4 border-b pb-2">
                <i class="fas fa-cog mr-2 text-fucsia-magia"></i> Stato e Configurazioni
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Stato *</label>
                    <select name="stato" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent">
                        <option value="pending" {{ old('stato', $professionista->stato) == 'pending' ? 'selected' : '' }}>In Attesa di Approvazione</option>
                        <option value="attivo" {{ old('stato', $professionista->stato) == 'attivo' ? 'selected' : '' }}>Attivo</option>
                        <option value="sospeso" {{ old('stato', $professionista->stato) == 'sospeso' ? 'selected' : '' }}>Sospeso</option>
                        <option value="inattivo" {{ old('stato', $professionista->stato) == 'inattivo' ? 'selected' : '' }}>Inattivo</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Visibilità</label>
                    <div class="flex items-center mt-3">
                        <input type="checkbox" name="visibile_pubblico" id="visibile_pubblico" value="1" {{ old('visibile_pubblico', $professionista->visibile_pubblico) ? 'checked' : '' }} class="rounded border-gray-300 text-fucsia-magia">
                        <label for="visibile_pubblico" class="ml-2 text-sm text-gray-700">Visibile al Pubblico</label>
                    </div>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Note Interne (riservate admin)</label>
                    <textarea name="note_interne" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent">{{ old('note_interne', $professionista->note_interne) }}</textarea>
                </div>
            </div>
        </div>

        <!-- Bottoni Azioni -->
        <div class="flex justify-end space-x-3">
            <a href="{{ route('admin.professionisti.show', $professionista->id) }}" class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                <i class="fas fa-times mr-2"></i> Annulla
            </a>
            <button type="submit" class="px-6 py-2 bg-fucsia-magia text-white rounded-lg hover:bg-viola-magia">
                <i class="fas fa-save mr-2"></i> Salva Modifiche
            </button>
        </div>
    </form>
</div>

<script>
// Gestione tag dinamici
function aggiungiTag(tipo, inputId) {
    const input = document.getElementById(inputId);
    const valore = input.value.trim();
    if (!valore) return;

    const container = document.getElementById(`${tipo}-container`);
    const index = container.children.length;
    const colore = tipo === 'specializzazioni' ? 'bg-fucsia-magia' : (tipo === 'qualifiche' ? 'bg-viola-magia' : 'bg-blue-500');

    const tagHtml = `
        <span class="px-3 py-1 ${colore} text-white rounded-full text-sm flex items-center gap-2">
            ${valore}
            <button type="button" onclick="rimuoviTag('${tipo}', ${index})" class="text-white hover:text-red-200">
                <i class="fas fa-times"></i>
            </button>
            <input type="hidden" name="${tipo === 'lingue' ? 'lingue_parlate' : tipo}[]" value="${valore}">
        </span>
    `;
    container.insertAdjacentHTML('beforeend', tagHtml);
    input.value = '';
}

function rimuoviTag(tipo, index) {
    const container = document.getElementById(`${tipo}-container`);
    const tags = container.querySelectorAll('span');
    if (tags[index]) tags[index].remove();
}

// Enter per aggiungere
['nuova-specializzazione', 'nuova-qualifica', 'nuova-lingua'].forEach(id => {
    document.getElementById(id)?.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            const tipo = id.replace('nuova-', '');
            aggiungiTag(tipo === 'lingua' ? 'lingue' : tipo, id);
        }
    });
});
</script>

@endsection
