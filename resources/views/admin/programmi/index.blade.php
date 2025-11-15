@extends('layouts.admin')

@section('titolo', 'Gestione Programmi')

@section('contenuto')
<div class="p-6">

    <!-- Header con Azioni -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Gestione Programmi</h2>
            <p class="text-gray-600 mt-1">Gestisci i programmi e corsi offerti dal centro</p>
        </div>
        <div class="mt-4 md:mt-0">
            <a href="{{ route('admin.programmi.create') }}"
               class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-viola-magia to-fucsia-magia text-white font-semibold rounded-lg hover:shadow-lg transition transform hover:-translate-y-0.5">
                <i class="fas fa-plus-circle mr-2"></i>
                Nuovo Programma
            </a>
        </div>
    </div>

    <!-- Statistiche Rapide -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-viola-magia">
            <p class="text-sm text-gray-600 font-medium">Totali</p>
            <p class="text-2xl font-bold text-gray-800">{{ $statistiche['totali'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-green-500">
            <p class="text-sm text-gray-600 font-medium">Attivi</p>
            <p class="text-2xl font-bold text-gray-800">{{ $statistiche['attivi'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-blue-500">
            <p class="text-sm text-gray-600 font-medium">Visibili</p>
            <p class="text-2xl font-bold text-gray-800">{{ $statistiche['visibili'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-yellow-500">
            <p class="text-sm text-gray-600 font-medium">In Promo</p>
            <p class="text-2xl font-bold text-gray-800">{{ $statistiche['in_promo'] }}</p>
        </div>
    </div>

    <!-- Filtri e Ricerca -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <form method="GET" action="{{ route('admin.programmi.index') }}" class="space-y-4">

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

                <!-- Ricerca Generale -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-search mr-1"></i> Cerca Programma
                    </label>
                    <input
                        type="text"
                        name="ricerca"
                        value="{{ request('ricerca') }}"
                        placeholder="Nome, descrizione..."
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent"
                    >
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
                        <option value="fitness" {{ request('tipologia') == 'fitness' ? 'selected' : '' }}>Fitness</option>
                        <option value="danza" {{ request('tipologia') == 'danza' ? 'selected' : '' }}>Danza</option>
                        <option value="alimentazione" {{ request('tipologia') == 'alimentazione' ? 'selected' : '' }}>Alimentazione</option>
                        <option value="wellness" {{ request('tipologia') == 'wellness' ? 'selected' : '' }}>Wellness</option>
                        <option value="personalizzato" {{ request('tipologia') == 'personalizzato' ? 'selected' : '' }}>Personalizzato</option>
                        <option value="altro" {{ request('tipologia') == 'altro' ? 'selected' : '' }}>Altro</option>
                    </select>
                </div>

                <!-- Filtro Livello -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-signal mr-1"></i> Livello
                    </label>
                    <select
                        name="livello"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent"
                    >
                        <option value="">Tutti</option>
                        <option value="principiante" {{ request('livello') == 'principiante' ? 'selected' : '' }}>Principiante</option>
                        <option value="intermedio" {{ request('livello') == 'intermedio' ? 'selected' : '' }}>Intermedio</option>
                        <option value="avanzato" {{ request('livello') == 'avanzato' ? 'selected' : '' }}>Avanzato</option>
                        <option value="tutti" {{ request('livello') == 'tutti' ? 'selected' : '' }}>Tutti i livelli</option>
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

            </div>

            <!-- Filtri Rapidi -->
            <div class="flex flex-wrap gap-2">
                <label class="inline-flex items-center">
                    <input type="checkbox" name="solo_attivi" value="1" {{ request('solo_attivi') ? 'checked' : '' }}
                           class="rounded text-fucsia-magia focus:ring-fucsia-magia">
                    <span class="ml-2 text-sm text-gray-700">Solo Attivi</span>
                </label>
                <label class="inline-flex items-center">
                    <input type="checkbox" name="solo_visibili" value="1" {{ request('solo_visibili') ? 'checked' : '' }}
                           class="rounded text-fucsia-magia focus:ring-fucsia-magia">
                    <span class="ml-2 text-sm text-gray-700">Solo Visibili</span>
                </label>
                <label class="inline-flex items-center">
                    <input type="checkbox" name="solo_evidenza" value="1" {{ request('solo_evidenza') ? 'checked' : '' }}
                           class="rounded text-fucsia-magia focus:ring-fucsia-magia">
                    <span class="ml-2 text-sm text-gray-700">Solo in Evidenza</span>
                </label>
                <label class="inline-flex items-center">
                    <input type="checkbox" name="solo_promo" value="1" {{ request('solo_promo') ? 'checked' : '' }}
                           class="rounded text-fucsia-magia focus:ring-fucsia-magia">
                    <span class="ml-2 text-sm text-gray-700">Solo in Promo</span>
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
                    href="{{ route('admin.programmi.index') }}"
                    class="px-6 py-2 bg-gray-200 text-gray-700 font-medium rounded-lg hover:bg-gray-300 transition"
                >
                    <i class="fas fa-redo mr-2"></i> Reset
                </a>
            </div>

        </form>
    </div>

    <!-- Tabella Programmi Desktop -->
    <div class="bg-white rounded-lg shadow overflow-hidden hidden md:block">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Programma</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipologia</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Livello</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Prezzo</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Iscritti</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Stato</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Azioni</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($programmi as $programma)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4">
                            <div>
                                <div class="text-sm font-medium text-gray-900">{{ $programma->nome }}</div>
                                @if($programma->descrizione_breve)
                                <div class="text-xs text-gray-500">{{ Str::limit($programma->descrizione_breve, 50) }}</div>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                {{ ucfirst($programma->tipologia) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            {{ ucfirst($programma->livello) }}
                        </td>
                        <td class="px-6 py-4">
                            @if($programma->prezzo_su_richiesta)
                                <span class="text-sm text-gray-600">Su richiesta</span>
                            @elseif($programma->isInPromo())
                                <div>
                                    <span class="text-sm font-bold text-green-600">€ {{ number_format($programma->prezzo_promo, 2) }}</span>
                                    <span class="text-xs text-gray-400 line-through ml-1">€ {{ number_format($programma->prezzo_base, 2) }}</span>
                                    <span class="ml-1 px-2 py-0.5 text-xs bg-red-100 text-red-800 rounded">-{{ $programma->sconto_percentuale }}%</span>
                                </div>
                            @else
                                <span class="text-sm font-medium text-gray-800">€ {{ number_format($programma->prezzo_base, 2) }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="text-sm font-medium text-gray-900">
                                {{ $programma->posti_occupati }} @if($programma->posti_disponibili)/ {{ $programma->posti_disponibili }}@endif
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex flex-col items-center gap-1">
                                @if($programma->attivo)
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                        <i class="fas fa-check-circle mr-1"></i> Attivo
                                    </span>
                                @else
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                        <i class="fas fa-pause-circle mr-1"></i> Non attivo
                                    </span>
                                @endif
                                @if($programma->in_evidenza)
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        <i class="fas fa-star mr-1"></i> In evidenza
                                    </span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 text-right text-sm font-medium space-x-2">
                            <a href="{{ route('admin.programmi.show', $programma->id) }}"
                               class="text-blue-600 hover:text-blue-900" title="Visualizza">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('admin.programmi.edit', $programma->id) }}"
                               class="text-fucsia-magia hover:text-viola-magia" title="Modifica">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form method="POST" action="{{ route('admin.programmi.destroy', $programma->id) }}" class="inline" id="delete-form-{{ $programma->id }}">
                                @csrf
                                @method('DELETE')
                                <button type="button"
                                        onclick="confermaEliminazione('delete-form-{{ $programma->id }}', 'Eliminare il programma?', 'Il programma {{ $programma->nome }} sarà eliminato definitivamente. Le lezioni esistenti non saranno eliminate.')"
                                        class="text-red-600 hover:text-red-900" title="Elimina">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <i class="fas fa-dumbbell text-gray-400 text-5xl mb-4"></i>
                            <p class="text-gray-500 font-medium">Nessun programma trovato</p>
                            <a href="{{ route('admin.programmi.create') }}" class="text-fucsia-magia hover:text-viola-magia mt-2 inline-block">
                                <i class="fas fa-plus-circle mr-1"></i> Crea il primo programma
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginazione -->
        @if($programmi->hasPages())
        <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
            {{ $programmi->links() }}
        </div>
        @endif
    </div>

    <!-- Card Programmi Mobile -->
    <div class="md:hidden space-y-4">
        @forelse($programmi as $programma)
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-start justify-between mb-3">
                <div class="flex-1">
                    <p class="font-semibold text-gray-800">{{ $programma->nome }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ ucfirst($programma->tipologia) }} - {{ ucfirst($programma->livello) }}</p>
                </div>
                @if($programma->attivo)
                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                        Attivo
                    </span>
                @endif
            </div>

            <div class="space-y-2 text-sm mb-3">
                <p class="text-gray-600">
                    <i class="fas fa-euro-sign text-fucsia-magia mr-2"></i>
                    @if($programma->prezzo_su_richiesta)
                        Su richiesta
                    @elseif($programma->isInPromo())
                        <span class="font-bold text-green-600">€ {{ number_format($programma->prezzo_promo, 2) }}</span>
                        <span class="line-through text-xs ml-1">€ {{ number_format($programma->prezzo_base, 2) }}</span>
                    @else
                        € {{ number_format($programma->prezzo_base, 2) }}
                    @endif
                </p>
                <p class="text-gray-600">
                    <i class="fas fa-users text-fucsia-magia mr-2"></i>
                    Iscritti: {{ $programma->posti_occupati }} @if($programma->posti_disponibili)/ {{ $programma->posti_disponibili }}@endif
                </p>
            </div>

            <div class="flex gap-2">
                <a href="{{ route('admin.programmi.show', $programma->id) }}"
                   class="flex-1 px-4 py-2 bg-blue-500 text-white text-center rounded-lg text-sm font-medium">
                    <i class="fas fa-eye mr-1"></i> Vedi
                </a>
                <a href="{{ route('admin.programmi.edit', $programma->id) }}"
                   class="flex-1 px-4 py-2 bg-fucsia-magia text-white text-center rounded-lg text-sm font-medium">
                    <i class="fas fa-edit mr-1"></i> Modifica
                </a>
            </div>
        </div>
        @empty
        <div class="bg-white rounded-lg shadow p-8 text-center">
            <i class="fas fa-dumbbell text-gray-400 text-5xl mb-4"></i>
            <p class="text-gray-500 font-medium mb-4">Nessun programma trovato</p>
            <a href="{{ route('admin.programmi.create') }}"
               class="inline-block px-6 py-3 bg-gradient-to-r from-viola-magia to-fucsia-magia text-white font-semibold rounded-lg">
                <i class="fas fa-plus-circle mr-2"></i> Crea Programma
            </a>
        </div>
        @endforelse

        @if($programmi->hasPages())
        <div class="bg-white rounded-lg shadow p-4">
            {{ $programmi->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
