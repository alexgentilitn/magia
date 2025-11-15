@extends('layouts.admin')

@section('titolo', 'Modifica Scheda Allenamento')

@section('contenuto')
<div class="p-6">

    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Modifica Scheda Allenamento</h2>
                <p class="text-gray-600 mt-1">{{ $scheda->nome_scheda }}</p>
            </div>
            <a href="{{ route('admin.schede.show', $scheda) }}" class="text-gray-600 hover:text-gray-900">
                <i class="fas fa-arrow-left mr-2"></i> Torna ai dettagli
            </a>
        </div>
    </div>

    <form action="{{ route('admin.schede.update', $scheda) }}" method="POST" id="form-scheda">
        @csrf
        @method('PUT')

        <!-- Informazioni Base -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4 pb-2 border-b">
                <i class="fas fa-info-circle text-fucsia-magia mr-2"></i> Informazioni Base
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Cliente (readonly) -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Cliente
                    </label>
                    <input type="text" readonly
                           value="{{ $scheda->cliente->cognome }} {{ $scheda->cliente->nome }} ({{ $scheda->cliente->email }})"
                           class="w-full px-4 py-2 border rounded-lg bg-gray-100 text-gray-600">
                    <p class="text-xs text-gray-500 mt-1">Il cliente non può essere modificato dopo la creazione</p>
                </div>

                <!-- Nome Scheda -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Nome Scheda <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="nome_scheda" value="{{ old('nome_scheda', $scheda->nome_scheda) }}" required
                           placeholder="Es: Programma Tonificazione Base"
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-fucsia-magia">
                    @error('nome_scheda')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Descrizione -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Descrizione</label>
                    <textarea name="descrizione" rows="3" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-fucsia-magia"
                              placeholder="Breve descrizione del programma...">{{ old('descrizione', $scheda->descrizione) }}</textarea>
                </div>

                <!-- Obiettivi -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Obiettivi</label>
                    <textarea name="obiettivi" rows="3" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-fucsia-magia"
                              placeholder="Es: Tonificazione, perdita peso, aumento forza...">{{ old('obiettivi', $scheda->obiettivi) }}</textarea>
                </div>

                <!-- Date -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Data Inizio</label>
                    <input type="date" name="data_inizio" value="{{ old('data_inizio', $scheda->data_inizio?->format('Y-m-d')) }}"
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-fucsia-magia">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Data Fine</label>
                    <input type="date" name="data_fine" value="{{ old('data_fine', $scheda->data_fine?->format('Y-m-d')) }}"
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-fucsia-magia">
                </div>

                <!-- Durata Settimane -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Durata (settimane)</label>
                    <input type="number" name="durata_settimane" value="{{ old('durata_settimane', $scheda->durata_settimane) }}" min="1" max="52"
                           placeholder="Es: 12"
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-fucsia-magia">
                </div>

                <!-- Stato -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Stato <span class="text-red-500">*</span>
                    </label>
                    <select name="stato" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-fucsia-magia">
                        <option value="bozza" {{ old('stato', $scheda->stato) == 'bozza' ? 'selected' : '' }}>Bozza</option>
                        <option value="attiva" {{ old('stato', $scheda->stato) == 'attiva' ? 'selected' : '' }}>Attiva</option>
                        <option value="completata" {{ old('stato', $scheda->stato) == 'completata' ? 'selected' : '' }}>Completata</option>
                        <option value="archiviata" {{ old('stato', $scheda->stato) == 'archiviata' ? 'selected' : '' }}>Archiviata</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Note Aggiuntive -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4 pb-2 border-b">
                <i class="fas fa-sticky-note text-fucsia-magia mr-2"></i> Note Aggiuntive
            </h3>

            <div class="grid grid-cols-1 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Note Generali</label>
                    <textarea name="note_generali" rows="3" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-fucsia-magia"
                              placeholder="Note generali sul programma...">{{ old('note_generali', $scheda->note_generali) }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Note Alimentazione</label>
                    <textarea name="note_alimentazione" rows="3" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-fucsia-magia"
                              placeholder="Consigli alimentari...">{{ old('note_alimentazione', $scheda->note_alimentazione) }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Consigli Professionista</label>
                    <textarea name="consigli_professionista" rows="3" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-fucsia-magia"
                              placeholder="Raccomandazioni e suggerimenti...">{{ old('consigli_professionista', $scheda->consigli_professionista) }}</textarea>
                </div>
            </div>
        </div>

        <!-- Esercizi per Giorno -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <div class="flex items-center justify-between mb-4 pb-2 border-b">
                <h3 class="text-lg font-bold text-gray-800">
                    <i class="fas fa-dumbbell text-fucsia-magia mr-2"></i> Esercizi Settimanali
                </h3>
                <p class="text-sm text-gray-600">Modifica, aggiungi o rimuovi esercizi</p>
            </div>

            @foreach($giorni_settimana as $giorno)
                <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                    <div class="flex items-center justify-between mb-3">
                        <h4 class="font-bold text-gray-700">{{ $giorno }}</h4>
                        <button type="button" onclick="aggiungiEsercizio('{{ $giorno }}')"
                                class="text-sm px-3 py-1 bg-fucsia-magia text-white rounded hover:bg-opacity-90">
                            <i class="fas fa-plus mr-1"></i> Aggiungi Esercizio
                        </button>
                    </div>
                    <div id="esercizi-{{ $giorno }}" class="space-y-3">
                        @php
                            $esercizi_giorno = $scheda->esercizi->where('giorno_settimana', $giorno)->sortBy('ordine');
                        @endphp
                        @foreach($esercizi_giorno as $esercizio)
                            <div class="bg-white p-4 rounded border relative esercizio-esistente" data-giorno="{{ $giorno }}">
                                <button type="button" onclick="rimuoviEsercizio(this)"
                                        class="absolute top-2 right-2 text-red-500 hover:text-red-700">
                                    <i class="fas fa-times"></i>
                                </button>

                                <input type="hidden" name="esercizi[{{ $loop->parent->index }}_{{ $loop->index }}][id]" value="{{ $esercizio->id }}">
                                <input type="hidden" name="esercizi[{{ $loop->parent->index }}_{{ $loop->index }}][giorno_settimana]" value="{{ $giorno }}">
                                <input type="hidden" name="esercizi[{{ $loop->parent->index }}_{{ $loop->index }}][ordine]" value="{{ $esercizio->ordine }}" class="ordine-input">

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                    <div class="md:col-span-2">
                                        <label class="block text-xs font-medium text-gray-700 mb-1">Nome Esercizio *</label>
                                        <input type="text" name="esercizi[{{ $loop->parent->index }}_{{ $loop->index }}][nome_esercizio]" required
                                               value="{{ $esercizio->nome_esercizio }}"
                                               placeholder="Es: Squat" class="w-full px-3 py-2 text-sm border rounded focus:ring-2 focus:ring-fucsia-magia">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-1">Categoria</label>
                                        <select name="esercizi[{{ $loop->parent->index }}_{{ $loop->index }}][categoria]" class="w-full px-3 py-2 text-sm border rounded focus:ring-2 focus:ring-fucsia-magia">
                                            <option value="forza" {{ $esercizio->categoria == 'forza' ? 'selected' : '' }}>Forza</option>
                                            <option value="cardio" {{ $esercizio->categoria == 'cardio' ? 'selected' : '' }}>Cardio</option>
                                            <option value="stretching" {{ $esercizio->categoria == 'stretching' ? 'selected' : '' }}>Stretching</option>
                                            <option value="mobilità" {{ $esercizio->categoria == 'mobilità' ? 'selected' : '' }}>Mobilità</option>
                                            <option value="altro" {{ $esercizio->categoria == 'altro' ? 'selected' : '' }}>Altro</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-1">Serie</label>
                                        <input type="number" name="esercizi[{{ $loop->parent->index }}_{{ $loop->index }}][serie]" min="1"
                                               value="{{ $esercizio->serie }}"
                                               placeholder="3" class="w-full px-3 py-2 text-sm border rounded focus:ring-2 focus:ring-fucsia-magia">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-1">Ripetizioni</label>
                                        <input type="text" name="esercizi[{{ $loop->parent->index }}_{{ $loop->index }}][ripetizioni]"
                                               value="{{ $esercizio->ripetizioni }}"
                                               placeholder="12-15" class="w-full px-3 py-2 text-sm border rounded focus:ring-2 focus:ring-fucsia-magia">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-1">Recupero (sec)</label>
                                        <input type="number" name="esercizi[{{ $loop->parent->index }}_{{ $loop->index }}][recupero_secondi]" min="0"
                                               value="{{ $esercizio->recupero_secondi }}"
                                               placeholder="60" class="w-full px-3 py-2 text-sm border rounded focus:ring-2 focus:ring-fucsia-magia">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-1">Peso</label>
                                        <input type="text" name="esercizi[{{ $loop->parent->index }}_{{ $loop->index }}][peso_suggerito]"
                                               value="{{ $esercizio->peso_suggerito }}"
                                               placeholder="10kg" class="w-full px-3 py-2 text-sm border rounded focus:ring-2 focus:ring-fucsia-magia">
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="block text-xs font-medium text-gray-700 mb-1">Note Esecuzione</label>
                                        <textarea name="esercizi[{{ $loop->parent->index }}_{{ $loop->index }}][note_esecuzione]" rows="2"
                                                  placeholder="Indicazioni tecniche..."
                                                  class="w-full px-3 py-2 text-sm border rounded focus:ring-2 focus:ring-fucsia-magia">{{ $esercizio->note_esecuzione }}</textarea>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pulsanti Azione -->
        <div class="flex items-center justify-between">
            <a href="{{ route('admin.schede.show', $scheda) }}" class="px-6 py-3 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                <i class="fas fa-times mr-2"></i> Annulla
            </a>
            <button type="submit" class="px-6 py-3 bg-gradient-to-r from-viola-magia to-fucsia-magia text-white font-semibold rounded-lg hover:shadow-lg">
                <i class="fas fa-save mr-2"></i> Salva Modifiche
            </button>
        </div>
    </form>
</div>

<script>
// Inizializza contatori per nuovi esercizi
let contatoriEsercizi = {};
let contatoreGlobale = 1000; // Inizia da 1000 per evitare conflitti con esercizi esistenti

@foreach($giorni_settimana as $giorno)
    contatoriEsercizi['{{ $giorno }}'] = {{ $scheda->esercizi->where('giorno_settimana', $giorno)->count() }};
@endforeach

function aggiungiEsercizio(giorno) {
    const container = document.getElementById(`esercizi-${giorno}`);
    const index = `${giorno}_${contatoreGlobale++}`;
    const ordine = contatoriEsercizi[giorno] + 1;
    contatoriEsercizi[giorno]++;

    const html = `
        <div class="bg-white p-4 rounded border relative esercizio-nuovo" id="esercizio-${index}">
            <button type="button" onclick="rimuoviEsercizio(this)"
                    class="absolute top-2 right-2 text-red-500 hover:text-red-700">
                <i class="fas fa-times"></i>
            </button>

            <input type="hidden" name="esercizi[${index}][giorno_settimana]" value="${giorno}">
            <input type="hidden" name="esercizi[${index}][ordine]" value="${ordine}" class="ordine-input">

            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <div class="md:col-span-2">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Nome Esercizio *</label>
                    <input type="text" name="esercizi[${index}][nome_esercizio]" required
                           placeholder="Es: Squat" class="w-full px-3 py-2 text-sm border rounded focus:ring-2 focus:ring-fucsia-magia">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Categoria</label>
                    <select name="esercizi[${index}][categoria]" class="w-full px-3 py-2 text-sm border rounded focus:ring-2 focus:ring-fucsia-magia">
                        <option value="forza">Forza</option>
                        <option value="cardio">Cardio</option>
                        <option value="stretching">Stretching</option>
                        <option value="mobilità">Mobilità</option>
                        <option value="altro">Altro</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Serie</label>
                    <input type="number" name="esercizi[${index}][serie]" min="1" placeholder="3"
                           class="w-full px-3 py-2 text-sm border rounded focus:ring-2 focus:ring-fucsia-magia">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Ripetizioni</label>
                    <input type="text" name="esercizi[${index}][ripetizioni]" placeholder="12-15"
                           class="w-full px-3 py-2 text-sm border rounded focus:ring-2 focus:ring-fucsia-magia">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Recupero (sec)</label>
                    <input type="number" name="esercizi[${index}][recupero_secondi]" min="0" placeholder="60"
                           class="w-full px-3 py-2 text-sm border rounded focus:ring-2 focus:ring-fucsia-magia">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Peso</label>
                    <input type="text" name="esercizi[${index}][peso_suggerito]" placeholder="10kg"
                           class="w-full px-3 py-2 text-sm border rounded focus:ring-2 focus:ring-fucsia-magia">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Note Esecuzione</label>
                    <textarea name="esercizi[${index}][note_esecuzione]" rows="2"
                              placeholder="Indicazioni tecniche..."
                              class="w-full px-3 py-2 text-sm border rounded focus:ring-2 focus:ring-fucsia-magia"></textarea>
                </div>
            </div>
        </div>
    `;

    container.insertAdjacentHTML('beforeend', html);
}

function rimuoviEsercizio(button) {
    const esercizioDiv = button.closest('.bg-white');
    const giorno = esercizioDiv.getAttribute('data-giorno') || esercizioDiv.querySelector('[name*="giorno_settimana"]').value;

    if (confirm('Sei sicuro di voler rimuovere questo esercizio?')) {
        esercizioDiv.remove();
        contatoriEsercizi[giorno]--;

        // Riordina gli esercizi rimasti per questo giorno
        riordinaEsercizi(giorno);
    }
}

function riordinaEsercizi(giorno) {
    const container = document.getElementById(`esercizi-${giorno}`);
    const esercizi = container.querySelectorAll('.bg-white');

    esercizi.forEach((esercizio, index) => {
        const ordineInput = esercizio.querySelector('.ordine-input');
        if (ordineInput) {
            ordineInput.value = index + 1;
        }
    });
}

// Riordina tutti gli esercizi prima del submit per mantenere l'ordine corretto
document.getElementById('form-scheda').addEventListener('submit', function(e) {
    @foreach($giorni_settimana as $giorno)
        riordinaEsercizi('{{ $giorno }}');
    @endforeach
});
</script>
@endsection
