@extends('layouts.admin')

@section('titolo', 'Gestione Lezioni')

@section('contenuto')
<div class="p-6">

    <!-- Header con Azioni -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Gestione Lezioni</h2>
            <p class="text-gray-600 mt-1">Gestisci le lezioni e i corsi del centro fitness</p>
        </div>
        <div class="mt-4 md:mt-0">
            <a href="{{ route('admin.lezioni.create') }}"
               class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-viola-magia to-fucsia-magia text-white font-semibold rounded-lg hover:shadow-lg transition transform hover:-translate-y-0.5">
                <i class="fas fa-plus-circle mr-2"></i>
                Nuova Lezione
            </a>
        </div>
    </div>

    <!-- Toolbar Azioni Multiple (nascosta inizialmente) -->
    <div id="bulkActionsBar" class="hidden bg-gradient-to-r from-viola-magia to-fucsia-magia rounded-lg p-4 mb-6 shadow-lg">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between text-white">
            <div class="flex items-center mb-3 md:mb-0">
                <i class="fas fa-check-square text-2xl mr-3"></i>
                <div>
                    <p class="font-bold text-lg"><span id="selectedCount">0</span> lezioni selezionate</p>
                    <p class="text-sm opacity-90">Puoi modificarle tutte insieme</p>
                </div>
            </div>
            <div class="flex gap-2">
                <button type="button" onclick="editMultiple()"
                        class="px-4 py-2 bg-white text-viola-magia rounded-lg hover:bg-gray-100 transition font-medium">
                    <i class="fas fa-edit mr-2"></i>Modifica Selezionate
                </button>
                <button type="button" onclick="clearSelection()"
                        class="px-4 py-2 bg-white bg-opacity-20 text-white rounded-lg hover:bg-opacity-30 transition font-medium">
                    <i class="fas fa-times mr-2"></i>Annulla Selezione
                </button>
            </div>
        </div>
    </div>

    <!-- Statistiche Rapide -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-viola-magia">
            <p class="text-sm text-gray-600 font-medium">Totali</p>
            <p class="text-2xl font-bold text-gray-800">{{ $statistiche['totali'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-blue-500">
            <p class="text-sm text-gray-600 font-medium">Oggi</p>
            <p class="text-2xl font-bold text-gray-800">{{ $statistiche['oggi'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-yellow-500">
            <p class="text-sm text-gray-600 font-medium">Future</p>
            <p class="text-2xl font-bold text-gray-800">{{ $statistiche['future'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-green-500">
            <p class="text-sm text-gray-600 font-medium">Completate</p>
            <p class="text-2xl font-bold text-gray-800">{{ $statistiche['completate'] }}</p>
        </div>
    </div>

    <!-- Filtri e Ricerca -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <form method="GET" action="{{ route('admin.lezioni.index') }}" class="space-y-4">

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

                <!-- Filtro Data Da -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-calendar mr-1"></i> Data Da
                    </label>
                    <input
                        type="date"
                        name="data_da"
                        value="{{ request('data_da') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent"
                    >
                </div>

                <!-- Filtro Data A -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-calendar mr-1"></i> Data A
                    </label>
                    <input
                        type="date"
                        name="data_a"
                        value="{{ request('data_a') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent"
                    >
                </div>

                <!-- Filtro Stato -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-filter mr-1"></i> Stato
                    </label>
                    <select
                        name="stato"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent"
                    >
                        <option value="">Tutti</option>
                        <option value="programmata" {{ request('stato') == 'programmata' ? 'selected' : '' }}>Programmata</option>
                        <option value="confermata" {{ request('stato') == 'confermata' ? 'selected' : '' }}>Confermata</option>
                        <option value="in_corso" {{ request('stato') == 'in_corso' ? 'selected' : '' }}>In Corso</option>
                        <option value="completata" {{ request('stato') == 'completata' ? 'selected' : '' }}>Completata</option>
                        <option value="cancellata" {{ request('stato') == 'cancellata' ? 'selected' : '' }}>Cancellata</option>
                        <option value="rinviata" {{ request('stato') == 'rinviata' ? 'selected' : '' }}>Rinviata</option>
                    </select>
                </div>

                <!-- Filtro Tipologia -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-tag mr-1"></i> Tipologia
                    </label>
                    <select
                        name="tipologia"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent"
                    >
                        <option value="">Tutte</option>
                        <option value="individuale" {{ request('tipologia') == 'individuale' ? 'selected' : '' }}>Individuale</option>
                        <option value="gruppo" {{ request('tipologia') == 'gruppo' ? 'selected' : '' }}>Gruppo</option>
                        <option value="online" {{ request('tipologia') == 'online' ? 'selected' : '' }}>Online</option>
                        <option value="ibrida" {{ request('tipologia') == 'ibrida' ? 'selected' : '' }}>Ibrida</option>
                    </select>
                </div>

            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                <!-- Filtro Programma -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-dumbbell mr-1"></i> Programma
                    </label>
                    <select
                        name="programma_id"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent"
                    >
                        <option value="">Tutti</option>
                        @foreach($programmi as $programma)
                        <option value="{{ $programma->id }}" {{ request('programma_id') == $programma->id ? 'selected' : '' }}>
                            {{ $programma->nome }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- Filtro Sede -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-map-marker-alt mr-1"></i> Sede
                    </label>
                    <select
                        name="sede_id"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent"
                    >
                        <option value="">Tutte</option>
                        @foreach($sedi as $sede)
                        <option value="{{ $sede->id }}" {{ request('sede_id') == $sede->id ? 'selected' : '' }}>
                            {{ $sede->nome }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- Filtro Professionista -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-user-tie mr-1"></i> Professionista
                    </label>
                    <select
                        name="professionista_id"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent"
                    >
                        <option value="">Tutti</option>
                        @foreach($professionisti as $professionista)
                        <option value="{{ $professionista->id }}" {{ request('professionista_id') == $professionista->id ? 'selected' : '' }}>
                            {{ $professionista->nome }} {{ $professionista->cognome }}
                        </option>
                        @endforeach
                    </select>
                </div>

            </div>

            <!-- Filtri Rapidi -->
            <div class="flex flex-wrap gap-2">
                <label class="inline-flex items-center">
                    <input type="checkbox" name="solo_future" value="1" {{ request('solo_future') ? 'checked' : '' }}
                           class="rounded text-fucsia-magia focus:ring-fucsia-magia">
                    <span class="ml-2 text-sm text-gray-700">Solo Future</span>
                </label>
                <label class="inline-flex items-center">
                    <input type="checkbox" name="solo_passate" value="1" {{ request('solo_passate') ? 'checked' : '' }}
                           class="rounded text-fucsia-magia focus:ring-fucsia-magia">
                    <span class="ml-2 text-sm text-gray-700">Solo Passate</span>
                </label>
                <label class="inline-flex items-center">
                    <input type="checkbox" name="solo_oggi" value="1" {{ request('solo_oggi') ? 'checked' : '' }}
                           class="rounded text-fucsia-magia focus:ring-fucsia-magia">
                    <span class="ml-2 text-sm text-gray-700">Solo Oggi</span>
                </label>
            </div>

            <!-- Bottoni Azione -->
            <div class="flex items-center gap-2">
                <button
                    type="submit"
                    class="px-6 py-2 bg-fucsia-magia text-white font-medium rounded-lg hover:bg-viola-magia transition"
                >
                    <i class="fas fa-search mr-2"></i> Cerca
                </button>
                <a
                    href="{{ route('admin.lezioni.index') }}"
                    class="px-6 py-2 bg-gray-200 text-gray-700 font-medium rounded-lg hover:bg-gray-300 transition"
                >
                    <i class="fas fa-redo mr-2"></i> Reset
                </a>
            </div>

        </form>
    </div>

    <!-- Tabella Lezioni Desktop -->
    <div class="bg-white rounded-lg shadow overflow-hidden hidden md:block">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-3 text-left">
                            <input type="checkbox" id="selectAll"
                                   class="rounded text-fucsia-magia focus:ring-fucsia-magia cursor-pointer"
                                   onchange="toggleSelectAll(this)">
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Lezione</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Data e Ora</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipologia</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Professionista</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sede</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Posti</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stato</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Azioni</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($lezioni as $lezione)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-3 py-4">
                            <input type="checkbox"
                                   class="lesson-checkbox rounded text-fucsia-magia focus:ring-fucsia-magia cursor-pointer"
                                   value="{{ $lezione->id }}"
                                   onchange="updateSelection()">
                        </td>
                        <td class="px-6 py-4">
                            <div>
                                <div class="text-sm font-medium text-gray-900">{{ $lezione->titolo }}</div>
                                @if($lezione->programma)
                                <div class="text-xs text-gray-500">{{ $lezione->programma->nome }}</div>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">
                                <i class="fas fa-calendar mr-1 text-fucsia-magia"></i>
                                {{ $lezione->data->format('d/m/Y') }}
                            </div>
                            <div class="text-xs text-gray-500">
                                <i class="fas fa-clock mr-1"></i>
                                {{ $lezione->range_orario }}
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @if($lezione->tipologia === 'individuale')
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                    <i class="fas fa-user mr-1"></i> Individuale
                                </span>
                            @elseif($lezione->tipologia === 'gruppo')
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                    <i class="fas fa-users mr-1"></i> Gruppo
                                </span>
                            @elseif($lezione->tipologia === 'online')
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    <i class="fas fa-video mr-1"></i> Online
                                </span>
                            @else
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-cyan-100 text-cyan-800">
                                    <i class="fas fa-globe mr-1"></i> Ibrida
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            @if($lezione->professionista)
                                {{ $lezione->professionista->nome }} {{ $lezione->professionista->cognome }}
                            @else
                                <span class="text-gray-400">Non assegnato</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            @if($lezione->sede)
                                {{ $lezione->sede->nome }}
                            @else
                                <span class="text-gray-400">N/D</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="text-sm font-medium text-gray-900">
                                {{ $lezione->posti_occupati }} / {{ $lezione->posti_totali }}
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2 mt-1">
                                <div class="bg-fucsia-magia h-2 rounded-full"
                                     style="width: {{ $lezione->percentuale_occupazione }}%">
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $lezione->badge_stato }}">
                                {{ ucfirst(str_replace('_', ' ', $lezione->stato)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right text-sm font-medium space-x-2">
                            <a href="{{ route('admin.lezioni.show', $lezione->id) }}"
                               class="text-blue-600 hover:text-blue-900" title="Visualizza">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('admin.lezioni.edit', $lezione->id) }}"
                               class="text-fucsia-magia hover:text-viola-magia" title="Modifica">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="{{ route('admin.lezioni.prenotazioni', $lezione->id) }}"
                               class="text-green-600 hover:text-green-900" title="Prenotazioni">
                                <i class="fas fa-users"></i>
                            </a>
                            <form method="POST" action="{{ route('admin.lezioni.destroy', $lezione->id) }}" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        onclick="return confirm('Sei sicuro di voler eliminare questa lezione?')"
                                        class="text-red-600 hover:text-red-900" title="Elimina">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center">
                            <i class="fas fa-calendar-times text-gray-400 text-5xl mb-4"></i>
                            <p class="text-gray-500 font-medium">Nessuna lezione trovata</p>
                            <a href="{{ route('admin.lezioni.create') }}" class="text-fucsia-magia hover:text-viola-magia mt-2 inline-block">
                                <i class="fas fa-plus-circle mr-1"></i> Crea la prima lezione
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginazione -->
        @if($lezioni->hasPages())
        <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
            {{ $lezioni->links() }}
        </div>
        @endif
    </div>

    <!-- Card Lezioni Mobile -->
    <div class="md:hidden space-y-4">
        @forelse($lezioni as $lezione)
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-start justify-between mb-3">
                <div class="flex-1">
                    <p class="font-semibold text-gray-800">{{ $lezione->titolo }}</p>
                    @if($lezione->programma)
                    <p class="text-xs text-gray-500">{{ $lezione->programma->nome }}</p>
                    @endif
                </div>
                <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $lezione->badge_stato }}">
                    {{ ucfirst(str_replace('_', ' ', $lezione->stato)) }}
                </span>
            </div>

            <div class="space-y-2 text-sm mb-3">
                <p class="text-gray-600">
                    <i class="fas fa-calendar text-fucsia-magia mr-2"></i>
                    {{ $lezione->data->format('d/m/Y') }} - {{ $lezione->range_orario }}
                </p>
                @if($lezione->professionista)
                <p class="text-gray-600">
                    <i class="fas fa-user-tie text-fucsia-magia mr-2"></i>
                    {{ $lezione->professionista->nome }} {{ $lezione->professionista->cognome }}
                </p>
                @endif
                @if($lezione->sede)
                <p class="text-gray-600">
                    <i class="fas fa-map-marker-alt text-fucsia-magia mr-2"></i>
                    {{ $lezione->sede->nome }}
                </p>
                @endif
                <div class="flex items-center">
                    <i class="fas fa-users text-fucsia-magia mr-2"></i>
                    <span class="text-gray-600">{{ $lezione->posti_occupati }} / {{ $lezione->posti_totali }} posti</span>
                    <div class="ml-2 flex-1 bg-gray-200 rounded-full h-2">
                        <div class="bg-fucsia-magia h-2 rounded-full"
                             style="width: {{ $lezione->percentuale_occupazione }}%">
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex gap-2">
                <a href="{{ route('admin.lezioni.show', $lezione->id) }}"
                   class="flex-1 px-4 py-2 bg-blue-500 text-white text-center rounded-lg text-sm font-medium">
                    <i class="fas fa-eye mr-1"></i> Vedi
                </a>
                <a href="{{ route('admin.lezioni.edit', $lezione->id) }}"
                   class="flex-1 px-4 py-2 bg-fucsia-magia text-white text-center rounded-lg text-sm font-medium">
                    <i class="fas fa-edit mr-1"></i> Modifica
                </a>
            </div>
        </div>
        @empty
        <div class="bg-white rounded-lg shadow p-8 text-center">
            <i class="fas fa-calendar-times text-gray-400 text-5xl mb-4"></i>
            <p class="text-gray-500 font-medium mb-4">Nessuna lezione trovata</p>
            <a href="{{ route('admin.lezioni.create') }}"
               class="inline-block px-6 py-3 bg-gradient-to-r from-viola-magia to-fucsia-magia text-white font-semibold rounded-lg">
                <i class="fas fa-plus-circle mr-2"></i> Crea Lezione
            </a>
        </div>
        @endforelse

        @if($lezioni->hasPages())
        <div class="bg-white rounded-lg shadow p-4">
            {{ $lezioni->links() }}
        </div>
        @endif
    </div>

</div>

<script>
// Gestione selezione multipla lezioni
function toggleSelectAll(checkbox) {
    const checkboxes = document.querySelectorAll('.lesson-checkbox');
    checkboxes.forEach(cb => cb.checked = checkbox.checked);
    updateSelection();
}

function updateSelection() {
    const checkboxes = document.querySelectorAll('.lesson-checkbox');
    const checked = Array.from(checkboxes).filter(cb => cb.checked);
    const count = checked.length;

    // Aggiorna contatore
    document.getElementById('selectedCount').textContent = count;

    // Mostra/nascondi toolbar
    const toolbar = document.getElementById('bulkActionsBar');
    if (count > 0) {
        toolbar.classList.remove('hidden');
    } else {
        toolbar.classList.add('hidden');
    }

    // Aggiorna stato checkbox "seleziona tutti"
    const selectAll = document.getElementById('selectAll');
    if (selectAll) {
        selectAll.checked = count > 0 && count === checkboxes.length;
        selectAll.indeterminate = count > 0 && count < checkboxes.length;
    }
}

function clearSelection() {
    const checkboxes = document.querySelectorAll('.lesson-checkbox');
    checkboxes.forEach(cb => cb.checked = false);
    document.getElementById('selectAll').checked = false;
    updateSelection();
}

function editMultiple() {
    const checkboxes = document.querySelectorAll('.lesson-checkbox:checked');
    const ids = Array.from(checkboxes).map(cb => cb.value);

    if (ids.length === 0) {
        alert('Seleziona almeno una lezione da modificare');
        return;
    }

    // Redirect alla pagina di modifica multipla con gli ID selezionati
    window.location.href = '{{ route("admin.lezioni.edit-multiple") }}?ids=' + ids.join(',');
}
</script>

@endsection
