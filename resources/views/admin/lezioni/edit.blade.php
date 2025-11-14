@extends('layouts.admin')

@section('titolo', 'Modifica Lezione')

@section('contenuto')
<div class="p-6">

    <div class="max-w-5xl mx-auto">

        <!-- Header -->
        <div class="mb-6">
            @if(request()->get('return') === 'calendario')
            <a href="{{ route('admin.calendario.index') }}" class="text-fucsia-magia hover:text-viola-magia mb-2 inline-block">
                <i class="fas fa-arrow-left mr-2"></i> Torna al calendario
            </a>
            @else
            <a href="{{ route('admin.lezioni.show', $lezione->id) }}" class="text-fucsia-magia hover:text-viola-magia mb-2 inline-block">
                <i class="fas fa-arrow-left mr-2"></i> Torna al dettaglio
            </a>
            @endif
            <h2 class="text-2xl font-bold text-gray-800">Modifica Lezione</h2>
            <p class="text-gray-600 mt-1">{{ $lezione->titolo }}</p>
        </div>

        <!-- Form -->
        <form method="POST" action="{{ route('admin.lezioni.update', $lezione->id) }}" class="bg-white rounded-lg shadow p-6">
            @csrf
            @method('PUT')

            <!-- Campo nascosto per ricordare da dove si proviene -->
            @if(request()->get('return'))
            <input type="hidden" name="return" value="{{ request()->get('return') }}">
            @endif

            <!-- Avviso Lezione Ricorrente -->
            @if($lezione->lezione_padre_id || $lezione->lezioniFiglie()->count() > 0)
            <div class="mb-6 bg-blue-50 border-l-4 border-blue-500 p-4 rounded-r-lg">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <i class="fas fa-repeat text-blue-500 text-xl mt-0.5"></i>
                    </div>
                    <div class="ml-3 flex-1">
                        <h3 class="text-sm font-bold text-blue-800 mb-2">Questa lezione fa parte di una serie ricorrente</h3>
                        <p class="text-sm text-blue-700 mb-3">
                            @if($lezione->lezione_padre_id)
                                Questa è una lezione derivata da una serie ricorrente.
                            @else
                                Questa lezione ha {{ $lezione->lezioniFiglie()->count() }} occorrenze future.
                            @endif
                        </p>
                        <div class="space-y-2">
                            <label class="flex items-center text-sm">
                                <input type="radio" name="applica_modifiche" value="solo_questa" checked class="mr-2 text-fucsia-magia focus:ring-fucsia-magia">
                                <span class="font-medium text-gray-900">Modifica solo questa lezione</span>
                            </label>
                            @if($lezione->lezione_padre_id)
                            <label class="flex items-center text-sm">
                                <input type="radio" name="applica_modifiche" value="serie_completa" class="mr-2 text-fucsia-magia focus:ring-fucsia-magia">
                                <span class="font-medium text-gray-900">Modifica tutta la serie ricorrente ({{ $lezione->lezionePadre->lezioniFiglie()->count() + 1 }} lezioni)</span>
                            </label>
                            @else
                            <label class="flex items-center text-sm">
                                <input type="radio" name="applica_modifiche" value="questa_e_future" class="mr-2 text-fucsia-magia focus:ring-fucsia-magia">
                                <span class="font-medium text-gray-900">Modifica questa e tutte le occorrenze future ({{ $lezione->lezioniFiglie()->count() + 1 }} lezioni)</span>
                            </label>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Sezione Informazioni Base -->
            <div class="mb-8">
                <h3 class="text-lg font-bold text-gray-800 mb-4 pb-2 border-b">
                    <i class="fas fa-info-circle text-fucsia-magia mr-2"></i> Informazioni Base
                </h3>
                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Titolo Lezione *</label>
                        <input type="text" name="titolo" value="{{ old('titolo', $lezione->titolo) }}" required
                               placeholder="Es: Lezione di Zumba"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent @error('titolo') border-red-500 @enderror">
                        @error('titolo')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Descrizione</label>
                        <textarea name="descrizione" rows="3"
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent @error('descrizione') border-red-500 @enderror">{{ old('descrizione', $lezione->descrizione) }}</textarea>
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
                            <option value="{{ $valore }}" {{ old('tipologia', $lezione->tipologia) == $valore ? 'selected' : '' }}>
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
                            <option value="{{ $professionista->id }}" {{ old('professionista_id', $lezione->professionista_id) == $professionista->id ? 'selected' : '' }}>
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
                            <option value="{{ $programma->id }}" {{ old('programma_id', $lezione->programma_id) == $programma->id ? 'selected' : '' }}>
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
                            <option value="{{ $sede->id }}" {{ old('sede_id', $lezione->sede_id) == $sede->id ? 'selected' : '' }}>
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
                        <input type="date" name="data" value="{{ old('data', $lezione->data->format('Y-m-d')) }}" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent @error('data') border-red-500 @enderror">
                        @error('data')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Ora Inizio *</label>
                        <input type="time" name="ora_inizio" value="{{ old('ora_inizio', \Carbon\Carbon::parse($lezione->ora_inizio)->format('H:i')) }}" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent @error('ora_inizio') border-red-500 @enderror">
                        @error('ora_inizio')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Ora Fine *</label>
                        <input type="time" name="ora_fine" value="{{ old('ora_fine', \Carbon\Carbon::parse($lezione->ora_fine)->format('H:i')) }}" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent @error('ora_fine') border-red-500 @enderror">
                        @error('ora_fine')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Durata (minuti) *</label>
                        <input type="number" name="durata_minuti" value="{{ old('durata_minuti', $lezione->durata_minuti) }}" required
                               min="15" max="480" placeholder="60"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent @error('durata_minuti') border-red-500 @enderror">
                        @error('durata_minuti')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Posti Totali *</label>
                        <input type="number" name="posti_totali" value="{{ old('posti_totali', $lezione->posti_totali) }}" required
                               min="1" max="100" placeholder="10"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent @error('posti_totali') border-red-500 @enderror">
                        @error('posti_totali')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                        @if($lezione->posti_occupati > 0)
                        <p class="text-xs text-gray-500 mt-1">
                            <i class="fas fa-info-circle mr-1"></i> Attualmente occupati: {{ $lezione->posti_occupati }}
                        </p>
                        @endif
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Stato *</label>
                        <select name="stato" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent @error('stato') border-red-500 @enderror">
                            @foreach($impostazioni['stati'] as $valore => $etichetta)
                            <option value="{{ $valore }}" {{ old('stato', $lezione->stato) == $valore ? 'selected' : '' }}>
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

            <!-- Sezione Link Online -->
            <div class="mb-8">
                <h3 class="text-lg font-bold text-gray-800 mb-4 pb-2 border-b">
                    <i class="fas fa-video text-fucsia-magia mr-2"></i> Link Online
                    <span class="text-xs font-normal text-gray-500">(solo per lezioni online o ibride)</span>
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Link Meeting</label>
                        <input type="url" name="link_online" value="{{ old('link_online', $lezione->link_online) }}"
                               placeholder="https://zoom.us/j/..."
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent @error('link_online') border-red-500 @enderror">
                        @error('link_online')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Password Meeting</label>
                        <input type="text" name="password_online" value="{{ old('password_online', $lezione->password_online) }}"
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
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent">{{ old('note_pubbliche', $lezione->note_pubbliche) }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Note Interne</label>
                        <textarea name="note_interne" rows="3"
                                  placeholder="Note interne visibili solo agli amministratori"
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent">{{ old('note_interne', $lezione->note_interne) }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Bottoni -->
            <div class="flex items-center justify-between pt-6 border-t">
                <a href="{{ route('admin.lezioni.show', $lezione->id) }}"
                   class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                    <i class="fas fa-times mr-2"></i> Annulla
                </a>
                <button type="submit"
                        class="px-6 py-2 bg-gradient-to-r from-viola-magia to-fucsia-magia text-white rounded-lg hover:shadow-lg transition">
                    <i class="fas fa-save mr-2"></i> Salva Modifiche
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
