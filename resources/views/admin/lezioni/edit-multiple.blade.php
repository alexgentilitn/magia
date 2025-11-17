@extends('layouts.admin')

@section('titolo', 'Modifica Multipla Lezioni')

@section('contenuto')
<div class="p-6">

    <div class="max-w-5xl mx-auto">

        <!-- Header -->
        <div class="mb-6">
            <a href="{{ route('admin.lezioni.index') }}" class="text-fucsia-magia hover:text-viola-magia mb-2 inline-block">
                <i class="fas fa-arrow-left mr-2"></i> Torna alla lista
            </a>
            <h2 class="text-2xl font-bold text-gray-800">Modifica Multipla Lezioni</h2>
            <p class="text-gray-600 mt-1">Stai per modificare <strong>{{ count($lezioni) }}</strong> lezioni contemporaneamente</p>
        </div>

        <!-- Avviso -->
        <div class="mb-6 bg-blue-50 border-l-4 border-blue-500 p-4 rounded-r-lg">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <i class="fas fa-info-circle text-blue-500 text-xl mt-0.5"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-bold text-blue-800 mb-2">Come funziona la modifica multipla</h3>
                    <ul class="text-sm text-blue-700 space-y-1 list-disc list-inside">
                        <li>I campi che lasci <strong>vuoti</strong> non verranno modificati</li>
                        <li>Solo i campi che compili verranno aggiornati in tutte le lezioni selezionate</li>
                        <li>La modifica è <strong>permanente</strong> e non può essere annullata</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Lezioni Selezionate -->
        <div class="mb-6 bg-white rounded-lg shadow p-4">
            <h3 class="font-bold text-gray-800 mb-3 flex items-center">
                <i class="fas fa-list text-fucsia-magia mr-2"></i>
                Lezioni Selezionate ({{ count($lezioni) }})
            </h3>
            <div class="max-h-40 overflow-y-auto space-y-1">
                @foreach($lezioni as $lezione)
                <div class="text-sm text-gray-700 px-3 py-2 bg-gray-50 rounded flex items-center justify-between">
                    <span>
                        <strong>{{ $lezione->titolo }}</strong> - {{ $lezione->data->format('d/m/Y') }} {{ $lezione->range_orario }}
                    </span>
                    <span class="text-xs text-gray-500">ID: {{ $lezione->id }}</span>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Form Modifica Multipla -->
        <form method="POST" action="{{ route('admin.lezioni.update-multiple') }}" class="bg-white rounded-lg shadow p-6" id="edit-multiple-form">
            @csrf

            <!-- ID delle lezioni -->
            @foreach($lezioni as $lezione)
            <input type="hidden" name="lezione_ids[]" value="{{ $lezione->id }}">
            @endforeach

            <!-- Sezione Informazioni Base -->
            <div class="mb-8">
                <h3 class="text-lg font-bold text-gray-800 mb-4 pb-2 border-b flex items-center">
                    <i class="fas fa-info-circle text-fucsia-magia mr-2"></i>
                    Informazioni Base
                    <span class="ml-2 text-xs font-normal text-gray-500">(lascia vuoto per non modificare)</span>
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                    <!-- Tipologia -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-tag mr-1"></i> Tipologia
                        </label>
                        <select name="tipologia" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia">
                            <option value="">-- Non modificare --</option>
                            @foreach($impostazioni['tipologie'] as $valore => $etichetta)
                            <option value="{{ $valore }}">{{ $etichetta }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Stato -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-flag mr-1"></i> Stato
                        </label>
                        <select name="stato" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia">
                            <option value="">-- Non modificare --</option>
                            @foreach($impostazioni['stati'] as $valore => $etichetta)
                            <option value="{{ $valore }}">{{ $etichetta }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Professionista -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-user-tie mr-1"></i> Professionista
                        </label>
                        <select name="professionista_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia">
                            <option value="">-- Non modificare --</option>
                            @foreach($professionisti as $professionista)
                            <option value="{{ $professionista->id }}">{{ $professionista->nome }} {{ $professionista->cognome }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Sede -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-map-marker-alt mr-1"></i> Sede
                        </label>
                        <select name="sede_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia">
                            <option value="">-- Non modificare --</option>
                            @foreach($sedi as $sede)
                            <option value="{{ $sede->id }}">{{ $sede->nome }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Programma -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-book mr-1"></i> Programma
                        </label>
                        <select name="programma_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia">
                            <option value="">-- Non modificare --</option>
                            @foreach($programmi as $programma)
                            <option value="{{ $programma->id }}">{{ $programma->nome }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Durata -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-hourglass-half mr-1"></i> Durata (minuti)
                        </label>
                        <input type="number" name="durata_minuti" min="15" max="480" step="15"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia"
                               placeholder="Es: 60">
                    </div>

                    <!-- Posti Totali -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-users mr-1"></i> Posti Totali
                        </label>
                        <input type="number" name="posti_totali" min="1" max="100"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia"
                               placeholder="Es: 20">
                    </div>

                </div>
            </div>

            <!-- Sezione Note e Informazioni -->
            <div class="mb-8">
                <h3 class="text-lg font-bold text-gray-800 mb-4 pb-2 border-b flex items-center">
                    <i class="fas fa-sticky-note text-fucsia-magia mr-2"></i>
                    Note e Descrizioni
                    <span class="ml-2 text-xs font-normal text-gray-500">(lascia vuoto per non modificare)</span>
                </h3>

                <div class="space-y-4">

                    <!-- Descrizione -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Descrizione
                        </label>
                        <textarea name="descrizione" rows="3"
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia"
                                  placeholder="Descrizione della lezione..."></textarea>
                    </div>

                    <!-- Note Pubbliche -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Note Pubbliche (visibili ai clienti)
                        </label>
                        <textarea name="note_pubbliche" rows="2"
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia"
                                  placeholder="Note visibili ai clienti..."></textarea>
                    </div>

                    <!-- Note Interne -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Note Interne (riservate)
                        </label>
                        <textarea name="note_interne" rows="2"
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia"
                                  placeholder="Note riservate allo staff..."></textarea>
                    </div>

                </div>
            </div>

            <!-- Sezione Online -->
            <div class="mb-8">
                <h3 class="text-lg font-bold text-gray-800 mb-4 pb-2 border-b flex items-center">
                    <i class="fas fa-video text-fucsia-magia mr-2"></i>
                    Informazioni Online
                    <span class="ml-2 text-xs font-normal text-gray-500">(lascia vuoto per non modificare)</span>
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                    <!-- Link Online -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Link Meeting Online
                        </label>
                        <input type="url" name="link_online"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia"
                               placeholder="https://...">
                    </div>

                    <!-- Password Online -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Password Meeting
                        </label>
                        <input type="text" name="password_online"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia"
                               placeholder="Password meeting...">
                    </div>

                </div>
            </div>

            <!-- Bottoni Azione -->
            <div class="flex items-center justify-between pt-6 border-t">
                <a href="{{ route('admin.lezioni.index') }}"
                   class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-medium">
                    <i class="fas fa-times mr-2"></i>
                    Annulla
                </a>
                <button type="button"
                        onclick="confermaAzione('edit-multiple-form', 'Modificare {{ count($lezioni) }} lezioni?', 'Le modifiche saranno applicate a tutte le lezioni selezionate. Questa operazione è irreversibile.', 'Sì, salva modifiche')"
                        class="px-6 py-3 bg-gradient-to-r from-viola-magia to-fucsia-magia text-white rounded-lg hover:shadow-lg transition font-medium">
                    <i class="fas fa-save mr-2"></i>
                    Salva Modifiche su {{ count($lezioni) }} Lezioni
                </button>
            </div>

        </form>

    </div>

</div>
@endsection
