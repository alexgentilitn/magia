@extends('layouts.admin')

@section('titolo', 'Gestione Permessi - ' . $utente->nome_completo)

@section('contenuto')
<div class="container mx-auto px-4 max-w-7xl">
    <!-- Header -->
    <div class="flex justify-between items-start mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 mb-2">Gestione Permessi Collaboratore</h1>
            <div class="flex items-center gap-2 flex-wrap">
                <span class="text-gray-600">
                    <i class="fas fa-user mr-1"></i> {{ $utente->nome_completo }}
                </span>
                <span class="bg-blue-500 text-white px-3 py-1 rounded-full text-xs">
                    {{ $utente->email }}
                </span>
                @if($utente->ruolo)
                    <span class="bg-gray-500 text-white px-3 py-1 rounded-full text-xs">
                        Ruolo: {{ $utente->ruolo->nome }}
                    </span>
                @endif
            </div>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.professionisti.show', $utente->professionista->id) }}"
               class="border border-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-100 transition">
                <i class="fas fa-arrow-left mr-1"></i> Torna al Profilo
            </a>
            <a href="{{ route('admin.professionisti.index') }}"
               class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                <i class="fas fa-users mr-1"></i> Lista Professionisti
            </a>
        </div>
    </div>

    <!-- Alert messaggi -->
    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded" role="alert">
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-2"></i>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded" role="alert">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle mr-2"></i>
                <span>{{ session('error') }}</span>
            </div>
        </div>
    @endif

    <!-- Form Gestione Permessi -->
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="bg-purple-600 text-white p-4">
            <h5 class="text-lg font-semibold flex items-center">
                <i class="fas fa-shield-alt mr-2"></i>Seleziona Permessi Individuali
            </h5>
        </div>
        <div class="p-6">
            <form action="{{ route('admin.professionisti.permessi.update', $utente->id) }}" method="POST">
                @csrf
                @method('PUT')

                @foreach($permessiDisponibili as $categoria => $permessi)
                    <div class="mb-8">
                        <h5 class="text-lg font-semibold border-b-2 border-gray-200 pb-3 mb-4 flex items-center">
                            <i class="fas {{ $permessi->first()->icona_categoria ?? 'fa-circle' }} mr-2 text-purple-600"></i>
                            {{ $permessi->first()->categoria_formattata ?? ucfirst($categoria) }}
                        </h5>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($permessi as $permesso)
                                <div class="border border-gray-200 rounded-lg p-4 hover:border-purple-300 transition {{ in_array($permesso->id, $permessiRuolo) ? 'bg-gray-50' : 'bg-white' }}">
                                    <label class="flex items-start cursor-pointer">
                                        <input type="checkbox"
                                               name="permessi[]"
                                               value="{{ $permesso->id }}"
                                               {{ in_array($permesso->id, $permessiAssegnati) ? 'checked' : '' }}
                                               class="mt-1 w-5 h-5 text-purple-600 rounded focus:ring-purple-500 {{ in_array($permesso->id, $permessiRuolo) ? 'opacity-50' : '' }}">
                                        <div class="ml-3 flex-1">
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <span class="font-semibold text-gray-800">{{ $permesso->nome }}</span>
                                                @if(in_array($permesso->id, $permessiRuolo))
                                                    <span class="bg-gray-400 text-white px-2 py-1 rounded text-xs" title="Permesso già incluso nel ruolo">
                                                        <i class="fas fa-user-tag mr-1"></i>Dal Ruolo
                                                    </span>
                                                @endif
                                            </div>
                                            @if($permesso->descrizione)
                                                <p class="text-sm text-gray-600 mt-1">{{ $permesso->descrizione }}</p>
                                            @endif
                                        </div>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach

                <!-- Pulsanti Azione -->
                <div class="flex justify-between items-center border-t-2 border-gray-200 pt-6 mt-6">
                    <button type="button"
                            @click="$dispatch('open-modal', 'reset-permessi')"
                            class="border-2 border-red-500 text-red-600 px-4 py-2 rounded-lg hover:bg-red-50 transition font-medium">
                        <i class="fas fa-undo mr-1"></i> Resetta Permessi Individuali
                    </button>
                    <div class="flex gap-3">
                        <a href="{{ route('admin.professionisti.show', $utente->professionista->id) }}"
                           class="border border-gray-300 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-100 transition">
                            <i class="fas fa-times mr-1"></i> Annulla
                        </a>
                        <button type="submit"
                                class="bg-purple-600 text-white px-6 py-2 rounded-lg hover:bg-purple-700 transition font-medium">
                            <i class="fas fa-save mr-1"></i> Salva Permessi
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Statistiche Permessi -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
        <div class="bg-white rounded-lg shadow p-6 text-center">
            <h6 class="text-gray-600 text-sm font-medium mb-2">Permessi dal Ruolo</h6>
            <p class="text-4xl font-bold text-gray-800">{{ count($permessiRuolo) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6 text-center">
            <h6 class="text-gray-600 text-sm font-medium mb-2">Permessi Individuali</h6>
            <p class="text-4xl font-bold text-purple-600">{{ count($permessiAssegnati) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6 text-center">
            <h6 class="text-gray-600 text-sm font-medium mb-2">Totale Permessi</h6>
            <p class="text-4xl font-bold text-green-600">{{ count(array_unique(array_merge($permessiRuolo, $permessiAssegnati))) }}</p>
        </div>
    </div>
</div>

<!-- Modal Conferma Reset (AlpineJS) -->
<div x-data="{ open: false }"
     @open-modal.window="if ($event.detail === 'reset-permessi') open = true"
     x-show="open"
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto"
     aria-labelledby="modal-title"
     role="dialog"
     aria-modal="true">

    <!-- Backdrop -->
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div x-show="open"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="open = false"
             class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
             aria-hidden="true"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <!-- Modal Panel -->
        <div x-show="open"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">

            <div class="bg-red-600 px-6 py-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-white flex items-center">
                        <i class="fas fa-exclamation-triangle mr-2"></i>Conferma Reset
                    </h3>
                    <button @click="open = false" class="text-white hover:text-gray-200">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>

            <div class="bg-white px-6 py-4">
                <p class="text-gray-800 mb-3">
                    Sei sicuro di voler <strong>rimuovere tutti i permessi individuali</strong> per
                    <strong>{{ $utente->nome_completo }}</strong>?
                </p>
                <p class="text-gray-600 text-sm">
                    L'utente manterrà solo i permessi del suo ruolo ({{ $utente->ruolo->nome ?? 'Nessun ruolo' }}).
                </p>
            </div>

            <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3">
                <button @click="open = false"
                        class="border border-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-100 transition">
                    Annulla
                </button>
                <form action="{{ route('admin.professionisti.permessi.reset', $utente->id) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition font-medium">
                        <i class="fas fa-undo mr-1"></i> Conferma Reset
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
