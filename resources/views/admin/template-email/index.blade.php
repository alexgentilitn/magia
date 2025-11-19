@extends('layouts.admin')

@section('titolo', 'Template Email')

@section('contenuto')
<div class="p-6">

    <!-- Header con Azioni -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Template Email</h2>
            <p class="text-gray-600 mt-1">Gestisci i template per l'invio automatico delle email</p>
        </div>
        <div class="mt-4 md:mt-0">
            <a href="{{ route('admin.template-email.create') }}"
               class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-viola-magia to-fucsia-magia text-white font-semibold rounded-lg hover:shadow-lg transition transform hover:-translate-y-0.5">
                <i class="fas fa-plus-circle mr-2"></i>
                Nuovo Template
            </a>
        </div>
    </div>

    <!-- Messaggi di Feedback -->
    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-lg shadow">
            <div class="flex items-center">
                <i class="fas fa-check-circle text-xl mr-3"></i>
                <p class="font-medium">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-lg shadow">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle text-xl mr-3"></i>
                <p class="font-medium">{{ session('error') }}</p>
            </div>
        </div>
    @endif

    <!-- Statistiche Rapide -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-viola-magia">
            <p class="text-sm text-gray-600 font-medium">Totale Template</p>
            <p class="text-2xl font-bold text-gray-800">{{ $templates->count() }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-green-500">
            <p class="text-sm text-gray-600 font-medium">Attivi</p>
            <p class="text-2xl font-bold text-gray-800">{{ $templates->where('attivo', true)->count() }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-blue-500">
            <p class="text-sm text-gray-600 font-medium">Invio Automatico</p>
            <p class="text-2xl font-bold text-gray-800">{{ $templates->where('invia_automaticamente', true)->count() }}</p>
        </div>
    </div>

    <!-- Tabella Template -->
    <div class="bg-white rounded-lg shadow overflow-hidden">

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gradient-to-r from-viola-magia to-fucsia-magia text-white">
                    <tr>
                        <th class="px-6 py-4 text-left text-sm font-semibold">Tipo</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold">Nome</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold">Oggetto</th>
                        <th class="px-6 py-4 text-center text-sm font-semibold">Stato</th>
                        <th class="px-6 py-4 text-center text-sm font-semibold">Invio Auto</th>
                        <th class="px-6 py-4 text-center text-sm font-semibold">Azioni</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($templates as $template)
                        <tr class="hover:bg-gray-50 transition">
                            <!-- Tipo -->
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-viola-magia bg-opacity-10 text-viola-magia">
                                    <i class="fas fa-tag mr-1"></i>
                                    {{ $template->tipo }}
                                </span>
                            </td>

                            <!-- Nome -->
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <i class="fas fa-envelope text-gray-400 mr-2"></i>
                                    <span class="font-medium text-gray-900">{{ $template->nome }}</span>
                                </div>
                            </td>

                            <!-- Oggetto -->
                            <td class="px-6 py-4">
                                <p class="text-sm text-gray-600 truncate max-w-xs">{{ $template->oggetto }}</p>
                            </td>

                            <!-- Stato -->
                            <td class="px-6 py-4 text-center">
                                @if($template->attivo)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <i class="fas fa-check-circle mr-1"></i>
                                        Attivo
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        <i class="fas fa-pause-circle mr-1"></i>
                                        Inattivo
                                    </span>
                                @endif
                            </td>

                            <!-- Invio Automatico -->
                            <td class="px-6 py-4 text-center">
                                @if($template->invia_automaticamente)
                                    <i class="fas fa-robot text-blue-600 text-lg" title="Invio automatico attivo"></i>
                                @else
                                    <i class="fas fa-user text-gray-400 text-lg" title="Invio manuale"></i>
                                @endif
                            </td>

                            <!-- Azioni -->
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <!-- Visualizza -->
                                    <a href="{{ route('admin.template-email.show', $template->id) }}"
                                       class="text-blue-600 hover:text-blue-800 transition"
                                       title="Visualizza">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    <!-- Modifica -->
                                    <a href="{{ route('admin.template-email.edit', $template->id) }}"
                                       class="text-yellow-600 hover:text-yellow-800 transition"
                                       title="Modifica">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    <!-- Toggle Attivo/Inattivo -->
                                    <form action="{{ route('admin.template-email.toggle-attivo', $template->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                                class="{{ $template->attivo ? 'text-gray-600 hover:text-gray-800' : 'text-green-600 hover:text-green-800' }} transition"
                                                title="{{ $template->attivo ? 'Disattiva' : 'Attiva' }}">
                                            <i class="fas fa-{{ $template->attivo ? 'toggle-on' : 'toggle-off' }}"></i>
                                        </button>
                                    </form>

                                    <!-- Elimina -->
                                    <form action="{{ route('admin.template-email.destroy', $template->id) }}" method="POST" class="inline" onsubmit="return confirm('Sei sicuro di voler eliminare questo template?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="text-red-600 hover:text-red-800 transition"
                                                title="Elimina">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center text-gray-400">
                                    <i class="fas fa-inbox text-6xl mb-4"></i>
                                    <p class="text-lg font-medium">Nessun template email trovato</p>
                                    <p class="text-sm mt-2">Inizia creando il tuo primo template</p>
                                    <a href="{{ route('admin.template-email.create') }}"
                                       class="mt-4 inline-flex items-center px-6 py-2 bg-gradient-to-r from-viola-magia to-fucsia-magia text-white rounded-lg hover:shadow-lg transition">
                                        <i class="fas fa-plus-circle mr-2"></i>
                                        Crea Template
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
