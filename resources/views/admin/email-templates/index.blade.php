@extends('layouts.admin')

@section('titolo', 'Gestione Email Templates')

@section('contenuto')
<div class="p-6">

    <!-- Header con Azioni -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Gestione Email Templates</h2>
            <p class="text-gray-600 mt-1">Gestisci i template email del sistema</p>
        </div>
        <div class="mt-4 md:mt-0">
            <a href="{{ route('admin.email-templates.create') }}"
               class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-viola-magia to-fucsia-magia text-white font-semibold rounded-lg hover:shadow-lg transition transform hover:-translate-y-0.5">
                <i class="fas fa-plus-circle mr-2"></i>
                Nuovo Template
            </a>
        </div>
    </div>

    <!-- Statistiche Rapide -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-viola-magia">
            <p class="text-sm text-gray-600 font-medium">Totali</p>
            <p class="text-2xl font-bold text-gray-800">{{ $statistiche['totali'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-green-500">
            <p class="text-sm text-gray-600 font-medium">Attivi</p>
            <p class="text-2xl font-bold text-gray-800">{{ $statistiche['attivi'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-red-500">
            <p class="text-sm text-gray-600 font-medium">Disattivi</p>
            <p class="text-2xl font-bold text-gray-800">{{ $statistiche['disattivi'] }}</p>
        </div>
    </div>

    <!-- Filtri e Ricerca -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <form method="GET" action="{{ route('admin.email-templates.index') }}" class="space-y-4">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                <!-- Ricerca Generale -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-search mr-1"></i> Cerca Template
                    </label>
                    <input
                        type="text"
                        name="ricerca"
                        value="{{ request('ricerca') }}"
                        placeholder="Nome, codice, oggetto..."
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent"
                    >
                </div>

                <!-- Filtro Tipo -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-tag mr-1"></i> Tipo
                    </label>
                    <select
                        name="tipo"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent"
                    >
                        <option value="">Tutti</option>
                        @foreach($tipi as $tipo)
                        <option value="{{ $tipo }}" {{ request('tipo') == $tipo ? 'selected' : '' }}>
                            {{ ucfirst($tipo) }}
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
                    href="{{ route('admin.email-templates.index') }}"
                    class="px-6 py-2 bg-gray-200 text-gray-700 font-medium rounded-lg hover:bg-gray-300 transition"
                >
                    <i class="fas fa-redo mr-2"></i> Reset
                </a>
            </div>

        </form>
    </div>

    <!-- Tabella Templates Desktop -->
    <div class="bg-white rounded-lg shadow overflow-hidden hidden md:block">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Template</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Oggetto</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Variabili</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Stato</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Azioni</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($templates as $template)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4">
                            <div>
                                <div class="text-sm font-medium text-gray-900">{{ $template->nome }}</div>
                                <div class="text-xs text-gray-500">
                                    <code class="bg-gray-100 px-2 py-0.5 rounded">{{ $template->codice }}</code>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                {{ ucfirst($template->tipo) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            {{ Str::limit($template->oggetto, 50) }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($template->variabili_disponibili && count($template->variabili_disponibili) > 0)
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                    {{ count($template->variabili_disponibili) }}
                                </span>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($template->attivo)
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                    <i class="fas fa-check-circle mr-1"></i> Attivo
                                </span>
                            @else
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                    <i class="fas fa-pause-circle mr-1"></i> Disattivo
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right text-sm font-medium space-x-2">
                            <a href="{{ route('admin.email-templates.show', $template->id) }}"
                               class="text-blue-600 hover:text-blue-900" title="Visualizza">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('admin.email-templates.edit', $template->id) }}"
                               class="text-fucsia-magia hover:text-viola-magia" title="Modifica">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form method="POST" action="{{ route('admin.email-templates.duplica', $template->id) }}" class="inline">
                                @csrf
                                <button type="submit" class="text-green-600 hover:text-green-900" title="Duplica">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </form>
                            <form method="POST" action="{{ route('admin.email-templates.toggle-attivo', $template->id) }}" class="inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="text-yellow-600 hover:text-yellow-900"
                                        title="{{ $template->attivo ? 'Disattiva' : 'Attiva' }}">
                                    <i class="fas fa-power-off"></i>
                                </button>
                            </form>
                            <form method="POST" action="{{ route('admin.email-templates.destroy', $template->id) }}" class="inline" id="delete-form-{{ $template->id }}">
                                @csrf
                                @method('DELETE')
                                <button type="button"
                                        onclick="confermaEliminazione('delete-form-{{ $template->id }}', 'Eliminare il template?', 'Il template {{ $template->nome }} sarà eliminato definitivamente.')"
                                        class="text-red-600 hover:text-red-900" title="Elimina">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <i class="fas fa-envelope text-gray-400 text-5xl mb-4"></i>
                            <p class="text-gray-500 font-medium">Nessun template trovato</p>
                            <a href="{{ route('admin.email-templates.create') }}" class="text-fucsia-magia hover:text-viola-magia mt-2 inline-block">
                                <i class="fas fa-plus-circle mr-1"></i> Crea il primo template
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginazione -->
        @if($templates->hasPages())
        <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
            {{ $templates->links() }}
        </div>
        @endif
    </div>

    <!-- Card Templates Mobile -->
    <div class="md:hidden space-y-4">
        @forelse($templates as $template)
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-start justify-between mb-3">
                <div class="flex-1">
                    <p class="font-semibold text-gray-800">{{ $template->nome }}</p>
                    <p class="text-xs text-gray-500 mt-1">
                        <code class="bg-gray-100 px-2 py-0.5 rounded">{{ $template->codice }}</code>
                    </p>
                    <p class="text-xs text-gray-600 mt-2">{{ Str::limit($template->oggetto, 40) }}</p>
                </div>
                @if($template->attivo)
                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                        Attivo
                    </span>
                @else
                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                        Disattivo
                    </span>
                @endif
            </div>

            <div class="space-y-2 text-sm mb-3">
                <p class="text-gray-600">
                    <i class="fas fa-tag text-fucsia-magia mr-2"></i>
                    <span class="px-2 py-1 text-xs rounded-full bg-purple-100 text-purple-800">
                        {{ ucfirst($template->tipo) }}
                    </span>
                </p>
                @if($template->variabili_disponibili && count($template->variabili_disponibili) > 0)
                <p class="text-gray-600">
                    <i class="fas fa-code text-fucsia-magia mr-2"></i>
                    {{ count($template->variabili_disponibili) }} variabili
                </p>
                @endif
            </div>

            <div class="flex gap-2">
                <a href="{{ route('admin.email-templates.show', $template->id) }}"
                   class="flex-1 px-4 py-2 bg-blue-500 text-white text-center rounded-lg text-sm font-medium">
                    <i class="fas fa-eye mr-1"></i> Vedi
                </a>
                <a href="{{ route('admin.email-templates.edit', $template->id) }}"
                   class="flex-1 px-4 py-2 bg-fucsia-magia text-white text-center rounded-lg text-sm font-medium">
                    <i class="fas fa-edit mr-1"></i> Modifica
                </a>
            </div>
        </div>
        @empty
        <div class="bg-white rounded-lg shadow p-8 text-center">
            <i class="fas fa-envelope text-gray-400 text-5xl mb-4"></i>
            <p class="text-gray-500 font-medium mb-4">Nessun template trovato</p>
            <a href="{{ route('admin.email-templates.create') }}"
               class="inline-block px-6 py-3 bg-gradient-to-r from-viola-magia to-fucsia-magia text-white font-semibold rounded-lg">
                <i class="fas fa-plus-circle mr-2"></i> Crea Template
            </a>
        </div>
        @endforelse

        @if($templates->hasPages())
        <div class="bg-white rounded-lg shadow p-4">
            {{ $templates->links() }}
        </div>
        @endif
    </div>

</div>

@push('scripts')
<script>
function confermaEliminazione(formId, titolo, messaggio) {
    if (confirm(titolo + '\n\n' + messaggio)) {
        document.getElementById(formId).submit();
    }
}
</script>
@endpush

@endsection
