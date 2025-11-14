@extends('layouts.admin')

@section('titolo', 'Nuovo Programma')

@section('contenuto')
<div class="p-6">

    <div class="max-w-5xl mx-auto">

        <!-- Header -->
        <div class="mb-6">
            <a href="{{ route('admin.programmi.index') }}" class="text-fucsia-magia hover:text-viola-magia mb-2 inline-block">
                <i class="fas fa-arrow-left mr-2"></i> Torna alla lista
            </a>
            <h2 class="text-2xl font-bold text-gray-800">Crea Nuovo Programma</h2>
            <p class="text-gray-600 mt-1">Compila tutti i campi obbligatori</p>
        </div>

        <!-- Form -->
        <form method="POST" action="{{ route('admin.programmi.store') }}" class="bg-white rounded-lg shadow p-6">
            @csrf

            <!-- Sezione Informazioni Base -->
            <div class="mb-8">
                <h3 class="text-lg font-bold text-gray-800 mb-4 pb-2 border-b">
                    <i class="fas fa-info-circle text-fucsia-magia mr-2"></i> Informazioni Base
                </h3>
                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nome Programma *</label>
                        <input type="text" name="nome" value="{{ old('nome') }}" required
                               placeholder="Es: Balla & Snella - Livello Base"
                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-fucsia-magia @error('nome') border-red-500 @enderror">
                        @error('nome')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Slug (URL) <span class="text-gray-500 text-xs">(lascia vuoto per generazione automatica)</span></label>
                        <input type="text" name="slug" value="{{ old('slug') }}"
                               placeholder="balla-snella-livello-base"
                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-fucsia-magia @error('slug') border-red-500 @enderror">
                        @error('slug')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Descrizione Breve</label>
                        <textarea name="descrizione_breve" rows="2" maxlength="500"
                                  placeholder="Breve descrizione per la lista programmi (max 500 caratteri)"
                                  class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-fucsia-magia @error('descrizione_breve') border-red-500 @enderror">{{ old('descrizione_breve') }}</textarea>
                        @error('descrizione_breve')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Descrizione Completa</label>
                        <textarea name="descrizione" rows="5"
                                  placeholder="Descrizione dettagliata del programma, obiettivi, metodologia..."
                                  class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-fucsia-magia @error('descrizione') border-red-500 @enderror">{{ old('descrizione') }}</textarea>
                        @error('descrizione')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Sezione Tipologia e Livello -->
            <div class="mb-8">
                <h3 class="text-lg font-bold text-gray-800 mb-4 pb-2 border-b">
                    <i class="fas fa-tag text-fucsia-magia mr-2"></i> Tipologia e Livello
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tipologia *</label>
                        <select name="tipologia" required
                                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-fucsia-magia @error('tipologia') border-red-500 @enderror">
                            <option value="">Seleziona...</option>
                            <option value="fitness" {{ old('tipologia') == 'fitness' ? 'selected' : '' }}>Fitness</option>
                            <option value="danza" {{ old('tipologia') == 'danza' ? 'selected' : '' }}>Danza</option>
                            <option value="alimentazione" {{ old('tipologia') == 'alimentazione' ? 'selected' : '' }}>Alimentazione</option>
                            <option value="wellness" {{ old('tipologia') == 'wellness' ? 'selected' : '' }}>Wellness</option>
                            <option value="personalizzato" {{ old('tipologia') == 'personalizzato' ? 'selected' : '' }}>Personalizzato</option>
                            <option value="altro" {{ old('tipologia') == 'altro' ? 'selected' : '' }}>Altro</option>
                        </select>
                        @error('tipologia')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Livello *</label>
                        <select name="livello" required
                                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-fucsia-magia @error('livello') border-red-500 @enderror">
                            <option value="">Seleziona...</option>
                            <option value="principiante" {{ old('livello') == 'principiante' ? 'selected' : '' }}>Principiante</option>
                            <option value="intermedio" {{ old('livello') == 'intermedio' ? 'selected' : '' }}>Intermedio</option>
                            <option value="avanzato" {{ old('livello') == 'avanzato' ? 'selected' : '' }}>Avanzato</option>
                            <option value="tutti" {{ old('livello', 'tutti') == 'tutti' ? 'selected' : '' }}>Tutti i livelli</option>
                        </select>
                        @error('livello')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Sezione Durata e Lezioni -->
            <div class="mb-8">
                <h3 class="text-lg font-bold text-gray-800 mb-4 pb-2 border-b">
                    <i class="fas fa-calendar-check text-fucsia-magia mr-2"></i> Durata e Lezioni
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Durata (giorni)</label>
                        <input type="number" name="durata_giorni" value="{{ old('durata_giorni') }}" min="1"
                               placeholder="30"
                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-fucsia-magia">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Durata (mesi)</label>
                        <input type="number" name="durata_mesi" value="{{ old('durata_mesi') }}" min="1"
                               placeholder="3"
                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-fucsia-magia">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Lezioni Totali</label>
                        <input type="number" name="lezioni_totali" value="{{ old('lezioni_totali') }}" min="1"
                               placeholder="24"
                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-fucsia-magia">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Lezioni a Settimana</label>
                        <input type="number" name="lezioni_settimana" value="{{ old('lezioni_settimana') }}" min="1" max="7"
                               placeholder="2"
                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-fucsia-magia">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Durata Singola Lezione (min)</label>
                        <input type="number" name="durata_singola_lezione" value="{{ old('durata_singola_lezione') }}" min="15"
                               placeholder="60"
                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-fucsia-magia">
                    </div>
                </div>
            </div>

            <!-- Sezione Prezzi -->
            <div class="mb-8">
                <h3 class="text-lg font-bold text-gray-800 mb-4 pb-2 border-b">
                    <i class="fas fa-euro-sign text-fucsia-magia mr-2"></i> Prezzi
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Prezzo Base *</label>
                        <div class="relative">
                            <span class="absolute left-3 top-2.5 text-gray-500">€</span>
                            <input type="number" name="prezzo_base" value="{{ old('prezzo_base') }}" required
                                   min="0" step="0.01" placeholder="99.00"
                                   class="w-full pl-8 pr-4 py-2 border rounded-lg focus:ring-2 focus:ring-fucsia-magia @error('prezzo_base') border-red-500 @enderror">
                        </div>
                        @error('prezzo_base')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Prezzo Promo (opzionale)</label>
                        <div class="relative">
                            <span class="absolute left-3 top-2.5 text-gray-500">€</span>
                            <input type="number" name="prezzo_promo" value="{{ old('prezzo_promo') }}"
                                   min="0" step="0.01" placeholder="79.00"
                                   class="w-full pl-8 pr-4 py-2 border rounded-lg focus:ring-2 focus:ring-fucsia-magia">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Promo Valida Da</label>
                        <input type="date" name="promo_valida_da" value="{{ old('promo_valida_da') }}"
                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-fucsia-magia">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Promo Valida A</label>
                        <input type="date" name="promo_valida_a" value="{{ old('promo_valida_a') }}"
                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-fucsia-magia">
                    </div>
                    <div class="md:col-span-2">
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="prezzo_su_richiesta" value="1" {{ old('prezzo_su_richiesta') ? 'checked' : '' }}
                                   class="rounded text-fucsia-magia focus:ring-fucsia-magia">
                            <span class="ml-2 text-sm text-gray-700">Prezzo su richiesta (nasconde i prezzi e mostra "Contattaci")</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Sezione Posti e Partecipanti -->
            <div class="mb-8">
                <h3 class="text-lg font-bold text-gray-800 mb-4 pb-2 border-b">
                    <i class="fas fa-users text-fucsia-magia mr-2"></i> Posti e Partecipanti
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Posti Disponibili</label>
                        <input type="number" name="posti_disponibili" value="{{ old('posti_disponibili') }}" min="1"
                               placeholder="20"
                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-fucsia-magia">
                        <p class="text-xs text-gray-500 mt-1">Lascia vuoto per posti illimitati</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Min Partecipanti</label>
                        <input type="number" name="min_partecipanti" value="{{ old('min_partecipanti') }}" min="1"
                               placeholder="5"
                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-fucsia-magia">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Max Partecipanti</label>
                        <input type="number" name="max_partecipanti" value="{{ old('max_partecipanti') }}" min="1"
                               placeholder="20"
                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-fucsia-magia">
                    </div>
                    <div class="md:col-span-3">
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="sempre_disponibile" value="1" {{ old('sempre_disponibile') ? 'checked' : '' }}
                                   class="rounded text-fucsia-magia focus:ring-fucsia-magia">
                            <span class="ml-2 text-sm text-gray-700">Sempre disponibile (ignora posti disponibili)</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Sezione Assegnazioni -->
            <div class="mb-8">
                <h3 class="text-lg font-bold text-gray-800 mb-4 pb-2 border-b">
                    <i class="fas fa-map-marker-alt text-fucsia-magia mr-2"></i> Assegnazioni
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Sede</label>
                        <select name="sede_id"
                                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-fucsia-magia">
                            <option value="">Nessuna sede specifica</option>
                            @foreach($sedi as $sede)
                            <option value="{{ $sede->id }}" {{ old('sede_id') == $sede->id ? 'selected' : '' }}>
                                {{ $sede->nome }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Professionista Responsabile</label>
                        <select name="professionista_id"
                                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-fucsia-magia">
                            <option value="">Nessun responsabile</option>
                            @foreach($professionisti as $professionista)
                            <option value="{{ $professionista->id }}" {{ old('professionista_id') == $professionista->id ? 'selected' : '' }}>
                                {{ $professionista->nome }} {{ $professionista->cognome }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- Sezione Date -->
            <div class="mb-8">
                <h3 class="text-lg font-bold text-gray-800 mb-4 pb-2 border-b">
                    <i class="fas fa-calendar text-fucsia-magia mr-2"></i> Periodo
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Data Inizio</label>
                        <input type="date" name="data_inizio" value="{{ old('data_inizio') }}"
                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-fucsia-magia">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Data Fine</label>
                        <input type="date" name="data_fine" value="{{ old('data_fine') }}"
                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-fucsia-magia">
                    </div>
                </div>
            </div>

            <!-- Sezione Stato e Visibilità -->
            <div class="mb-8">
                <h3 class="text-lg font-bold text-gray-800 mb-4 pb-2 border-b">
                    <i class="fas fa-toggle-on text-fucsia-magia mr-2"></i> Stato e Visibilità
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="attivo" value="1" {{ old('attivo', true) ? 'checked' : '' }}
                                   class="rounded text-fucsia-magia focus:ring-fucsia-magia">
                            <span class="ml-2 text-sm font-medium text-gray-700">Programma Attivo</span>
                        </label>
                        <p class="text-xs text-gray-500 mt-1 ml-6">Gli utenti possono iscriversi</p>
                    </div>
                    <div>
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="visibile_pubblico" value="1" {{ old('visibile_pubblico') ? 'checked' : '' }}
                                   class="rounded text-fucsia-magia focus:ring-fucsia-magia">
                            <span class="ml-2 text-sm font-medium text-gray-700">Visibile al Pubblico</span>
                        </label>
                        <p class="text-xs text-gray-500 mt-1 ml-6">Mostra sul sito pubblico</p>
                    </div>
                    <div>
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="in_evidenza" value="1" {{ old('in_evidenza') ? 'checked' : '' }}
                                   class="rounded text-fucsia-magia focus:ring-fucsia-magia">
                            <span class="ml-2 text-sm font-medium text-gray-700">In Evidenza</span>
                        </label>
                        <p class="text-xs text-gray-500 mt-1 ml-6">Mostra in homepage</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Ordine di Visualizzazione</label>
                        <input type="number" name="ordine" value="{{ old('ordine', 0) }}" min="0"
                               placeholder="0"
                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-fucsia-magia">
                        <p class="text-xs text-gray-500 mt-1">Più basso = più in alto nella lista</p>
                    </div>
                </div>
            </div>

            <!-- Bottoni -->
            <div class="flex items-center justify-between pt-6 border-t">
                <a href="{{ route('admin.programmi.index') }}"
                   class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                    <i class="fas fa-times mr-2"></i> Annulla
                </a>
                <button type="submit"
                        class="px-6 py-2 bg-gradient-to-r from-viola-magia to-fucsia-magia text-white rounded-lg hover:shadow-lg transition">
                    <i class="fas fa-save mr-2"></i> Crea Programma
                </button>
            </div>

        </form>

    </div>

</div>
@endsection
