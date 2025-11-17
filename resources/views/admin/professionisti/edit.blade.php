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

    <form method="POST" action="{{ route('admin.professionisti.update', $professionista->id) }}" class="bg-white rounded-lg shadow p-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Nome -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Nome *</label>
                <input type="text" name="nome" required value="{{ old('nome', $professionista->nome) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent">
            </div>

            <!-- Cognome -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Cognome *</label>
                <input type="text" name="cognome" required value="{{ old('cognome', $professionista->cognome) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent">
            </div>

            <!-- Email -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                <input type="email" name="email" required value="{{ old('email', $professionista->email) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent">
            </div>

            <!-- Telefono -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Telefono Mobile</label>
                <input type="text" name="telefono_mobile" value="{{ old('telefono_mobile', $professionista->telefono_mobile) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent">
            </div>

            <!-- Titolo Professionale -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Titolo Professionale</label>
                <input type="text" name="titolo_professionale" value="{{ old('titolo_professionale', $professionista->titolo_professionale) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent" placeholder="Es: Personal Trainer, Nutrizionista">
            </div>

            <!-- Anni Esperienza -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Anni di Esperienza</label>
                <input type="number" name="anni_esperienza" min="0" value="{{ old('anni_esperienza', $professionista->anni_esperienza) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent">
            </div>

            <!-- Tariffa Oraria -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tariffa Oraria (€)</label>
                <input type="number" name="tariffa_oraria" min="0" step="0.01" value="{{ old('tariffa_oraria', $professionista->tariffa_oraria) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent">
            </div>

            <!-- Tariffa Lezione Gruppo -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tariffa Lezione Gruppo (€)</label>
                <input type="number" name="tariffa_lezione_gruppo" min="0" step="0.01" value="{{ old('tariffa_lezione_gruppo', $professionista->tariffa_lezione_gruppo) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent">
            </div>

            <!-- Tariffa Lezione Privata -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tariffa Lezione Privata (€)</label>
                <input type="number" name="tariffa_lezione_privata" min="0" step="0.01" value="{{ old('tariffa_lezione_privata', $professionista->tariffa_lezione_privata) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent">
            </div>

            <!-- Stato -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Stato *</label>
                <select name="stato" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent">
                    <option value="pending" {{ old('stato', $professionista->stato) == 'pending' ? 'selected' : '' }}>In Attesa di Approvazione</option>
                    <option value="attivo" {{ old('stato', $professionista->stato) == 'attivo' ? 'selected' : '' }}>Attivo</option>
                    <option value="sospeso" {{ old('stato', $professionista->stato) == 'sospeso' ? 'selected' : '' }}>Sospeso</option>
                    <option value="inattivo" {{ old('stato', $professionista->stato) == 'inattivo' ? 'selected' : '' }}>Inattivo</option>
                </select>
            </div>

            <!-- Bio -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Biografia Professionale</label>
                <textarea name="bio" rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent" placeholder="Descrizione delle competenze e dell'esperienza...">{{ old('bio', $professionista->bio) }}</textarea>
            </div>

            <!-- Specializzazioni -->
            <div class="md:col-span-2">
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
            <div class="md:col-span-2">
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
                    <input type="text" id="nuova-qualifica" placeholder="Es: Laurea in Scienze Motorie, Diploma CONI..." class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent">
                    <button type="button" onclick="aggiungiTag('qualifiche', 'nuova-qualifica')" class="px-4 py-2 bg-viola-magia text-white rounded-lg hover:bg-viola-magia">
                        <i class="fas fa-plus mr-1"></i> Aggiungi
                    </button>
                </div>
            </div>
        </div>

        <!-- Flags -->
        <div class="mt-6 space-y-3">
            <div class="flex items-center">
                <input type="checkbox" name="visibile_pubblico" id="visibile_pubblico" value="1" {{ old('visibile_pubblico', $professionista->visibile_pubblico) ? 'checked' : '' }} class="rounded border-gray-300 text-fucsia-magia">
                <label for="visibile_pubblico" class="ml-2 text-sm text-gray-700">Visibile al Pubblico</label>
            </div>
        </div>

        <div class="mt-6 flex justify-end space-x-3">
            <a href="{{ route('admin.professionisti.show', $professionista->id) }}" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                Annulla
            </a>
            <button type="submit" class="px-4 py-2 bg-fucsia-magia text-white rounded-lg hover:bg-viola-magia">
                <i class="fas fa-save mr-2"></i> Salva Modifiche
            </button>
        </div>
    </form>
</div>

<script>
// Gestione tag dinamici per specializzazioni e qualifiche
function aggiungiTag(tipo, inputId) {
    const input = document.getElementById(inputId);
    const valore = input.value.trim();

    if (!valore) return;

    const container = document.getElementById(`${tipo}-container`);
    const index = container.children.length;

    const tagHtml = `
        <span class="px-3 py-1 ${tipo === 'specializzazioni' ? 'bg-fucsia-magia' : 'bg-viola-magia'} text-white rounded-full text-sm flex items-center gap-2">
            ${valore}
            <button type="button" onclick="rimuoviTag('${tipo}', ${index})" class="text-white hover:text-red-200">
                <i class="fas fa-times"></i>
            </button>
            <input type="hidden" name="${tipo}[]" value="${valore}">
        </span>
    `;

    container.insertAdjacentHTML('beforeend', tagHtml);
    input.value = '';
}

function rimuoviTag(tipo, index) {
    const container = document.getElementById(`${tipo}-container`);
    const tags = container.querySelectorAll('span');
    if (tags[index]) {
        tags[index].remove();
    }
}

// Permetti di aggiungere tag con Enter
document.getElementById('nuova-specializzazione')?.addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        aggiungiTag('specializzazioni', 'nuova-specializzazione');
    }
});

document.getElementById('nuova-qualifica')?.addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        aggiungiTag('qualifiche', 'nuova-qualifica');
    }
});
</script>

@endsection
