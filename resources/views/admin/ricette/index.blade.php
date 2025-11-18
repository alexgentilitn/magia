@extends('layouts.admin')

@section('titolo', 'Gestione Ricette')

@section('contenuto')
<div class="p-6">

    <!-- Header con Azioni -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Gestione Ricette</h2>
            <p class="text-gray-600 mt-1">Gestisci le ricette alimentari e la loro distribuzione automatica</p>
        </div>
        <div class="mt-4 md:mt-0">
            <a href="{{ route('admin.ricette.create') }}"
               class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-viola-magia to-fucsia-magia text-white font-semibold rounded-lg hover:shadow-lg transition transform hover:-translate-y-0.5">
                <i class="fas fa-plus-circle mr-2"></i>
                Nuova Ricetta
            </a>
        </div>
    </div>

    <!-- Messaggi -->
    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-lg shadow">
            <div class="flex items-center">
                <i class="fas fa-check-circle text-xl mr-3"></i>
                <p class="font-medium">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    <!-- Statistiche Rapide -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-viola-magia">
            <p class="text-sm text-gray-600 font-medium">Totale Ricette</p>
            <p class="text-2xl font-bold text-gray-800">{{ $statistiche['totali'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-green-500">
            <p class="text-sm text-gray-600 font-medium">Pubblicate</p>
            <p class="text-2xl font-bold text-gray-800">{{ $statistiche['pubblicate'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-yellow-500">
            <p class="text-sm text-gray-600 font-medium">Bozze</p>
            <p class="text-2xl font-bold text-gray-800">{{ $statistiche['bozze'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-blue-500">
            <p class="text-sm text-gray-600 font-medium">Programmate</p>
            <p class="text-2xl font-bold text-gray-800">{{ $statistiche['programmate'] }}</p>
        </div>
    </div>

    <!-- Filtri -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <form method="GET" action="{{ route('admin.ricette.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">

            <!-- Ricerca -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-search mr-1"></i> Cerca
                </label>
                <input type="text"
                       name="cerca"
                       value="{{ request('cerca') }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-viola-magia focus:border-transparent"
                       placeholder="Titolo ricetta...">
            </div>

            <!-- Categoria -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-list mr-1"></i> Categoria
                </label>
                <select name="categoria"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-viola-magia focus:border-transparent">
                    <option value="">Tutte</option>
                    @foreach(\App\Models\Ricetta::getCategorie() as $key => $label)
                        <option value="{{ $key }}" {{ request('categoria') == $key ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Difficoltà -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-chart-line mr-1"></i> Difficoltà
                </label>
                <select name="difficolta"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-viola-magia focus:border-transparent">
                    <option value="">Tutte</option>
                    @foreach(\App\Models\Ricetta::getDifficolta() as $key => $label)
                        <option value="{{ $key }}" {{ request('difficolta') == $key ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Stato -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-toggle-on mr-1"></i> Stato
                </label>
                <select name="pubblicata"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-viola-magia focus:border-transparent">
                    <option value="">Tutte</option>
                    <option value="1" {{ request('pubblicata') === '1' ? 'selected' : '' }}>Pubblicate</option>
                    <option value="0" {{ request('pubblicata') === '0' ? 'selected' : '' }}>Bozze</option>
                </select>
            </div>

            <!-- Bottoni Azioni -->
            <div class="md:col-span-4 flex gap-2">
                <button type="submit"
                        class="px-6 py-2 bg-viola-magia text-white rounded-lg hover:bg-opacity-90 transition">
                    <i class="fas fa-filter mr-2"></i>
                    Filtra
                </button>
                <a href="{{ route('admin.ricette.index') }}"
                   class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                    <i class="fas fa-times mr-2"></i>
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Griglia Ricette -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($ricette as $ricetta)
            <div class="bg-white rounded-lg shadow hover:shadow-xl transition overflow-hidden">

                <!-- Immagine -->
                <div class="relative h-48 bg-gray-200">
                    @if($ricetta->immagine && file_exists(storage_path('app/public/' . $ricetta->immagine)))
                        <img src="{{ asset('storage/' . $ricetta->immagine) }}"
                             alt="{{ $ricetta->titolo }}"
                             class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-viola-magia to-fucsia-magia">
                            <i class="fas fa-utensils text-white text-6xl opacity-50"></i>
                        </div>
                    @endif

                    <!-- Badge Stato -->
                    <div class="absolute top-3 right-3">
                        @if($ricetta->pubblicata && (!$ricetta->data_pubblicazione || $ricetta->data_pubblicazione <= now()))
                            <span class="px-3 py-1 bg-green-500 text-white text-xs font-semibold rounded-full">
                                <i class="fas fa-check-circle mr-1"></i>Pubblicata
                            </span>
                        @elseif($ricetta->data_pubblicazione && $ricetta->data_pubblicazione > now())
                            <span class="px-3 py-1 bg-blue-500 text-white text-xs font-semibold rounded-full">
                                <i class="fas fa-clock mr-1"></i>Programmata
                            </span>
                        @else
                            <span class="px-3 py-1 bg-gray-500 text-white text-xs font-semibold rounded-full">
                                <i class="fas fa-edit mr-1"></i>Bozza
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Contenuto -->
                <div class="p-5">

                    <!-- Titolo -->
                    <h3 class="text-lg font-bold text-gray-900 mb-2 truncate">
                        {{ $ricetta->titolo }}
                    </h3>

                    <!-- Descrizione -->
                    @if($ricetta->descrizione)
                        <p class="text-sm text-gray-600 mb-4 line-clamp-2">
                            {{ $ricetta->descrizione }}
                        </p>
                    @endif

                    <!-- Info Ricetta -->
                    <div class="grid grid-cols-3 gap-2 mb-4 text-center">
                        <!-- Tempo -->
                        @if($ricetta->tempo_preparazione)
                            <div class="bg-gray-50 rounded p-2">
                                <i class="fas fa-clock text-viola-magia text-sm"></i>
                                <p class="text-xs text-gray-600 mt-1">{{ $ricetta->tempo_preparazione }} min</p>
                            </div>
                        @endif

                        <!-- Difficoltà -->
                        <div class="bg-gray-50 rounded p-2">
                            <i class="fas fa-chart-line {{ $ricetta->difficolta === 'facile' ? 'text-green-500' : ($ricetta->difficolta === 'media' ? 'text-yellow-500' : 'text-red-500') }} text-sm"></i>
                            <p class="text-xs text-gray-600 mt-1 capitalize">{{ $ricetta->difficolta }}</p>
                        </div>

                        <!-- Calorie -->
                        @if($ricetta->calorie)
                            <div class="bg-gray-50 rounded p-2">
                                <i class="fas fa-fire text-orange-500 text-sm"></i>
                                <p class="text-xs text-gray-600 mt-1">{{ $ricetta->calorie }} kcal</p>
                            </div>
                        @endif
                    </div>

                    <!-- Categoria -->
                    @if($ricetta->categoria)
                        <div class="mb-4">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-fucsia-magia bg-opacity-10 text-fucsia-magia">
                                <i class="fas fa-tag mr-1"></i>
                                {{ \App\Models\Ricetta::getCategorie()[$ricetta->categoria] ?? $ricetta->categoria }}
                            </span>
                        </div>
                    @endif

                    <!-- Data Pubblicazione -->
                    @if($ricetta->data_pubblicazione)
                        <p class="text-xs text-gray-500 mb-4">
                            <i class="fas fa-calendar-alt mr-1"></i>
                            Pubblicazione: {{ $ricetta->data_pubblicazione->format('d/m/Y') }}
                        </p>
                    @endif

                    <!-- Azioni -->
                    <div class="flex gap-2">
                        <a href="{{ route('admin.ricette.show', $ricetta->id) }}"
                           class="flex-1 px-4 py-2 bg-blue-100 text-blue-700 text-sm font-medium rounded-lg hover:bg-blue-200 transition text-center">
                            <i class="fas fa-eye mr-1"></i>Visualizza
                        </a>
                        <a href="{{ route('admin.ricette.edit', $ricetta->id) }}"
                           class="flex-1 px-4 py-2 bg-yellow-100 text-yellow-700 text-sm font-medium rounded-lg hover:bg-yellow-200 transition text-center">
                            <i class="fas fa-edit mr-1"></i>Modifica
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="md:col-span-3 bg-white rounded-lg shadow p-12 text-center">
                <i class="fas fa-utensils text-6xl text-gray-300 mb-4"></i>
                <p class="text-lg font-medium text-gray-600 mb-2">Nessuna ricetta trovata</p>
                <p class="text-sm text-gray-500 mb-6">Inizia creando la tua prima ricetta</p>
                <a href="{{ route('admin.ricette.create') }}"
                   class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-viola-magia to-fucsia-magia text-white rounded-lg hover:shadow-lg transition">
                    <i class="fas fa-plus-circle mr-2"></i>
                    Crea Ricetta
                </a>
            </div>
        @endforelse
    </div>

    <!-- Paginazione -->
    @if($ricette->hasPages())
        <div class="mt-6">
            {{ $ricette->links() }}
        </div>
    @endif

</div>
@endsection
