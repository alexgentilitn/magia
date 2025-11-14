@extends('layouts.admin')

@section('titolo', 'Nuova Lezione')

@section('contenuto')
<div class="p-6">

    <div class="max-w-5xl mx-auto">

        <!-- Header -->
        <div class="mb-6">
            <a href="{{ route('admin.lezioni.index') }}" class="text-fucsia-magia hover:text-viola-magia mb-2 inline-block">
                <i class="fas fa-arrow-left mr-2"></i> Torna alla lista
            </a>
            <h2 class="text-2xl font-bold text-gray-800">Crea Nuova Lezione</h2>
            <p class="text-gray-600 mt-1">Compila tutti i campi obbligatori</p>
        </div>

        <!-- Form -->
        <form method="POST" action="{{ route('admin.lezioni.store') }}" class="bg-white rounded-lg shadow p-6">
            @csrf

            <!-- Sezione Informazioni Base -->
            <div class="mb-8">
                <h3 class="text-lg font-bold text-gray-800 mb-4 pb-2 border-b">
                    <i class="fas fa-info-circle text-fucsia-magia mr-2"></i> Informazioni Base
                </h3>
                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Titolo Lezione *</label>
                        <input type="text" name="titolo" value="{{ old('titolo') }}" required
                               placeholder="Es: Lezione di Zumba"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent @error('titolo') border-red-500 @enderror">
                        @error('titolo')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Descrizione</label>
                        <textarea name="descrizione" rows="3"
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent @error('descrizione') border-red-500 @enderror">{{ old('descrizione') }}</textarea>
                        @error('descrizione')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Sezione Tipologia e Assegnazioni -->
            <div class="mb-8">
                <h3 class="text-lg font-bold text-gray-800 mb-4 pb-2 border-b">
                    <i class="fas fa-tag text-fucsia-magia mr-2"></i> Tipologia e Assegnazioni
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tipologia *</label>
                        <select name="tipologia" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent @error('tipologia') border-red-500 @enderror">
                            <option value="">Seleziona...</option>
                            @foreach($impostazioni['tipologie'] as $valore => $etichetta)
                            <option value="{{ $valore }}" {{ old('tipologia') == $valore ? 'selected' : '' }}>
                                {{ $etichetta }}
                            </option>
                            @endforeach
                        </select>
                        @error('tipologia')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Professionista *</label>
                        <select name="professionista_id" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent @error('professionista_id') border-red-500 @enderror">
                            <option value="">Seleziona...</option>
                            @foreach($professionisti as $professionista)
                            <option value="{{ $professionista->id }}" {{ old('professionista_id') == $professionista->id ? 'selected' : '' }}>
                                {{ $professionista->nome }} {{ $professionista->cognome }}
                            </option>
                            @endforeach
                        </select>
                        @error('professionista_id')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Programma</label>
                        <select name="programma_id"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent @error('programma_id') border-red-500 @enderror">
                            <option value="">Nessun programma</option>
                            @foreach($programmi as $programma)
                            <option value="{{ $programma->id }}" {{ old('programma_id') == $programma->id ? 'selected' : '' }}>
                                {{ $programma->nome }}
                            </option>
                            @endforeach
                        </select>
                        @error('programma_id')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Sede</label>
                        <select name="sede_id"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent @error('sede_id') border-red-500 @enderror">
                            <option value="">Nessuna sede (online)</option>
                            @foreach($sedi as $sede)
                            <option value="{{ $sede->id }}" {{ old('sede_id') == $sede->id ? 'selected' : '' }}>
                                {{ $sede->nome }}
                            </option>
                            @endforeach
                        </select>
                        @error('sede_id')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Sezione Data e Orario -->
            <div class="mb-8">
                <h3 class="text-lg font-bold text-gray-800 mb-4 pb-2 border-b">
                    <i class="fas fa-calendar-alt text-fucsia-magia mr-2"></i> Data e Orario
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Data *</label>
                        <input type="date" name="data" value="{{ old('data') }}" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent @error('data') border-red-500 @enderror">
                        @error('data')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Ora Inizio *</label>
                        <input type="time" name="ora_inizio" value="{{ old('ora_inizio') }}" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent @error('ora_inizio') border-red-500 @enderror">
                        @error('ora_inizio')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Ora Fine *</label>
                        <input type="time" name="ora_fine" value="{{ old('ora_fine') }}" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent @error('ora_fine') border-red-500 @enderror">
                        @error('ora_fine')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Durata (minuti) *</label>
                        <input type="number" name="durata_minuti" value="{{ old('durata_minuti', 60) }}" required
                               min="15" max="480" placeholder="60"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent @error('durata_minuti') border-red-500 @enderror">
                        @error('durata_minuti')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Posti Totali *</label>
                        <input type="number" name="posti_totali" value="{{ old('posti_totali', 10) }}" required
                               min="1" max="100" placeholder="10"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent @error('posti_totali') border-red-500 @enderror">
                        @error('posti_totali')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Stato *</label>
                        <select name="stato" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent @error('stato') border-red-500 @enderror">
                            @foreach($impostazioni['stati'] as $valore => $etichetta)
                            <option value="{{ $valore }}" {{ old('stato', 'programmata') == $valore ? 'selected' : '' }}>
                                {{ $etichetta }}
                            </option>
                            @endforeach
                        </select>
                        @error('stato')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Sezione Ricorrenza -->
            <div class="mb-8" x-data="{ ricorrente: {{ old('ricorrente') ? 'true' : 'false' }} }">
                <h3 class="text-lg font-bold text-gray-800 mb-4 pb-2 border-b">
                    <i class="fas fa-repeat text-fucsia-magia mr-2"></i> Ricorrenza
                </h3>
                <div class="mb-4">
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="ricorrente" value="1" x-model="ricorrente"
                               class="rounded text-fucsia-magia focus:ring-fucsia-magia">
                        <span class="ml-2 text-sm text-gray-700">Lezione ricorrente (crea automaticamente lezioni successive)</span>
                    </label>
                </div>
                <div x-show="ricorrente" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Frequenza</label>
                        <select name="frequenza_ricorrenza"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent">
                            <option value="">Seleziona...</option>
                            @foreach($impostazioni['frequenze'] as $valore => $etichetta)
                            <option value="{{ $valore }}" {{ old('frequenza_ricorrenza') == $valore ? 'selected' : '' }}>
                                {{ $etichetta }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Ripeti Fino A</label>
                        <input type="date" name="fine_ricorrenza" value="{{ old('fine_ricorrenza') }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent">
                    </div>
                </div>
            </div>

            <!-- Sezione Link Online -->
            <div class="mb-8" x-data="{ isOnline: {{ in_array(old('tipologia'), ['online', 'ibrida']) ? 'true' : 'false' }} }">
                <h3 class="text-lg font-bold text-gray-800 mb-4 pb-2 border-b">
                    <i class="fas fa-video text-fucsia-magia mr-2"></i> Link Online
                    <span class="text-xs font-normal text-gray-500">(solo per lezioni online o ibride)</span>
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Link Meeting</label>
                        <input type="url" name="link_online" value="{{ old('link_online') }}"
                               placeholder="https://zoom.us/j/..."
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent @error('link_online') border-red-500 @enderror">
                        @error('link_online')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Password Meeting</label>
                        <input type="text" name="password_online" value="{{ old('password_online') }}"
                               placeholder="Password per accedere"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent">
                    </div>
                </div>
            </div>

            <!-- Sezione Note -->
            <div class="mb-8">
                <h3 class="text-lg font-bold text-gray-800 mb-4 pb-2 border-b">
                    <i class="fas fa-sticky-note text-fucsia-magia mr-2"></i> Note
                </h3>
                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Note Pubbliche</label>
                        <textarea name="note_pubbliche" rows="3"
                                  placeholder="Note visibili ai clienti (es: cosa portare, requisiti...)"
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent">{{ old('note_pubbliche') }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Note Interne</label>
                        <textarea name="note_interne" rows="3"
                                  placeholder="Note interne visibili solo agli amministratori"
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent">{{ old('note_interne') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Bottoni -->
            <div class="flex items-center justify-between pt-6 border-t">
                <a href="{{ route('admin.lezioni.index') }}"
                   class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                    <i class="fas fa-times mr-2"></i> Annulla
                </a>
                <button type="submit"
                        class="px-6 py-2 bg-gradient-to-r from-viola-magia to-fucsia-magia text-white rounded-lg hover:shadow-lg transition">
                    <i class="fas fa-save mr-2"></i> Crea Lezione
                </button>
            </div>

        </form>

    </div>

</div>

<script>
    // Calcola automaticamente durata_minuti quando cambiano ora_inizio e ora_fine
    document.addEventListener('DOMContentLoaded', function() {
        const oraInizio = document.querySelector('[name="ora_inizio"]');
        const oraFine = document.querySelector('[name="ora_fine"]');
        const durataMinuti = document.querySelector('[name="durata_minuti"]');

        function calcolaDurata() {
            if (oraInizio.value && oraFine.value) {
                const [oreInizio, minutiInizio] = oraInizio.value.split(':').map(Number);
                const [oreFine, minutiFine] = oraFine.value.split(':').map(Number);

                const minutiTotaliInizio = oreInizio * 60 + minutiInizio;
                const minutiTotaliFine = oreFine * 60 + minutiFine;

                const durata = minutiTotaliFine - minutiTotaliInizio;

                if (durata > 0) {
                    durataMinuti.value = durata;
                }
            }
        }

        oraInizio.addEventListener('change', calcolaDurata);
        oraFine.addEventListener('change', calcolaDurata);
    });
</script>
@endsection
