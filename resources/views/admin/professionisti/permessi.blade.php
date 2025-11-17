@extends('layouts.admin')

@section('titolo', 'Gestione Permessi - ' . $utente->nome_completo)

@section('contenuto')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <!-- Header Minimalista -->
    <div class="mb-8">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h1 class="text-3xl font-light text-gray-900">Gestione Permessi</h1>
                <div class="mt-2 flex items-center gap-3 text-sm">
                    <span class="text-gray-600">{{ $utente->nome_completo }}</span>
                    <span class="text-gray-400">•</span>
                    <span class="text-gray-500">{{ $utente->email }}</span>
                    @if($utente->ruolo)
                        <span class="text-gray-400">•</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-gray-100 text-gray-800">
                            {{ $utente->ruolo->nome }}
                        </span>
                    @endif
                </div>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.professionisti.show', $utente->professionista->id) }}"
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                    <i class="fas fa-arrow-left mr-2 text-xs"></i> Indietro
                </a>
            </div>
        </div>

        <!-- Linea separatrice -->
        <div class="border-b border-gray-200"></div>
    </div>

    <!-- Alert messaggi -->
    @if(session('success'))
        <div class="mb-6 rounded-md bg-green-50 p-4 border-l-4 border-green-400">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-check-circle text-green-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-green-700">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 rounded-md bg-red-50 p-4 border-l-4 border-red-400">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-circle text-red-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-red-700">{{ session('error') }}</p>
                </div>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">

        <!-- Colonna principale - Permessi -->
        <div class="lg:col-span-3">

            <form action="{{ route('admin.professionisti.permessi.update', $utente->id) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Permessi per categoria -->
                <div class="space-y-8">
                    @foreach($permessiDisponibili as $categoria => $permessi)
                        <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">

                            <!-- Intestazione categoria -->
                            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                                <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider flex items-center">
                                    <i class="fas {{ $permessi->first()->icona_categoria ?? 'fa-circle' }} mr-2 text-gray-500 text-xs"></i>
                                    {{ $permessi->first()->categoria_formattata ?? ucfirst($categoria) }}
                                </h3>
                            </div>

                            <!-- Lista permessi -->
                            <div class="divide-y divide-gray-100">
                                @foreach($permessi as $permesso)
                                    <div class="px-6 py-4 hover:bg-gray-50 transition-colors duration-150">
                                        <label class="flex items-start cursor-pointer group">
                                            <div class="flex items-center h-5">
                                                <input type="checkbox"
                                                       name="permessi[]"
                                                       value="{{ $permesso->id }}"
                                                       {{ in_array($permesso->id, $permessiAssegnati) ? 'checked' : '' }}
                                                       class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded {{ in_array($permesso->id, $permessiRuolo) ? 'opacity-40' : '' }}">
                                            </div>
                                            <div class="ml-3 flex-1">
                                                <div class="flex items-center justify-between">
                                                    <div>
                                                        <span class="text-sm font-medium text-gray-900 group-hover:text-indigo-600">
                                                            {{ $permesso->nome }}
                                                        </span>
                                                        @if(in_array($permesso->id, $permessiRuolo))
                                                            <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-600">
                                                                <i class="fas fa-user-shield mr-1 text-xs"></i>Ruolo
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                                @if($permesso->descrizione)
                                                    <p class="text-xs text-gray-500 mt-1">{{ $permesso->descrizione }}</p>
                                                @endif
                                            </div>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pulsanti azione -->
                <div class="mt-8 flex items-center justify-between pt-6 border-t border-gray-200">
                    <button type="button"
                            @click="$dispatch('open-modal', 'reset-permessi')"
                            class="inline-flex items-center px-4 py-2 border border-red-300 rounded-md shadow-sm text-sm font-medium text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        <i class="fas fa-undo mr-2 text-xs"></i> Resetta Permessi
                    </button>
                    <div class="flex gap-3">
                        <a href="{{ route('admin.professionisti.show', $utente->professionista->id) }}"
                           class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                            Annulla
                        </a>
                        <button type="submit"
                                class="inline-flex items-center px-6 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <i class="fas fa-check mr-2 text-xs"></i> Salva Modifiche
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Sidebar - Statistiche -->
        <div class="lg:col-span-1">
            <div class="sticky top-6 space-y-4">

                <!-- Card Statistiche -->
                <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                    <div class="px-4 py-3 bg-gray-50 border-b border-gray-200">
                        <h3 class="text-xs font-semibold text-gray-900 uppercase tracking-wider">
                            Riepilogo
                        </h3>
                    </div>
                    <div class="p-4 space-y-4">
                        <div>
                            <div class="text-xs text-gray-500 mb-1">Dal Ruolo</div>
                            <div class="text-2xl font-semibold text-gray-900">{{ count($permessiRuolo) }}</div>
                        </div>
                        <div class="border-t border-gray-100 pt-4">
                            <div class="text-xs text-gray-500 mb-1">Individuali</div>
                            <div class="text-2xl font-semibold text-indigo-600">{{ count($permessiAssegnati) }}</div>
                        </div>
                        <div class="border-t border-gray-100 pt-4">
                            <div class="text-xs text-gray-500 mb-1">Totale</div>
                            <div class="text-2xl font-semibold text-green-600">{{ count(array_unique(array_merge($permessiRuolo, $permessiAssegnati))) }}</div>
                        </div>
                    </div>
                </div>

                <!-- Info Card -->
                <div class="bg-blue-50 rounded-lg border border-blue-100 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-info-circle text-blue-400 text-sm"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-xs text-blue-700">
                                I permessi individuali si aggiungono a quelli del ruolo base.
                            </p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- Modal Conferma Reset -->
<div x-data="{ open: false }"
     @open-modal.window="if ($event.detail === 'reset-permessi') open = true"
     x-show="open"
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto"
     aria-labelledby="modal-title"
     role="dialog"
     aria-modal="true">

    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">

        <!-- Backdrop -->
        <div x-show="open"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="open = false"
             class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

        <!-- Modal Panel -->
        <div x-show="open"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">

            <div class="sm:flex sm:items-start">
                <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                    <i class="fas fa-exclamation-triangle text-red-600"></i>
                </div>
                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left flex-1">
                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                        Conferma Reset Permessi
                    </h3>
                    <div class="mt-2">
                        <p class="text-sm text-gray-500">
                            Sei sicuro di voler rimuovere tutti i permessi individuali per <strong>{{ $utente->nome_completo }}</strong>?
                        </p>
                        <p class="text-sm text-gray-500 mt-2">
                            L'utente manterrà solo i permessi del ruolo {{ $utente->ruolo->nome ?? 'Nessun ruolo' }}.
                        </p>
                    </div>
                </div>
            </div>

            <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                <form action="{{ route('admin.professionisti.permessi.reset', $utente->id) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Conferma Reset
                    </button>
                </form>
                <button type="button"
                        @click="open = false"
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 sm:mt-0 sm:w-auto sm:text-sm">
                    Annulla
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
