@extends('layouts.admin')

@section('titolo', 'Dettaglio Email Template')

@section('contenuto')
<div class="p-6">

    <div class="max-w-5xl mx-auto">

        <!-- Header -->
        <div class="mb-6">
            <a href="{{ route('admin.email-templates.index') }}" class="text-fucsia-magia hover:text-viola-magia mb-2 inline-block">
                <i class="fas fa-arrow-left mr-2"></i> Torna alla lista
            </a>
            <div class="flex items-start justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">{{ $template->nome }}</h2>
                    <p class="text-gray-600 mt-1">
                        <code class="bg-gray-100 px-2 py-1 rounded text-sm">{{ $template->codice }}</code>
                    </p>
                </div>
                <div class="flex gap-2">
                    @if($template->attivo)
                        <span class="px-4 py-2 text-sm font-semibold rounded-lg bg-green-100 text-green-800">
                            <i class="fas fa-check-circle mr-1"></i> Attivo
                        </span>
                    @else
                        <span class="px-4 py-2 text-sm font-semibold rounded-lg bg-gray-100 text-gray-800">
                            <i class="fas fa-pause-circle mr-1"></i> Disattivo
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Azioni Rapide -->
        <div class="bg-white rounded-lg shadow p-4 mb-6">
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.email-templates.edit', $template->id) }}"
                   class="px-4 py-2 bg-fucsia-magia text-white rounded-lg hover:bg-viola-magia transition">
                    <i class="fas fa-edit mr-2"></i> Modifica
                </a>
                <form method="POST" action="{{ route('admin.email-templates.duplica', $template->id) }}" class="inline">
                    @csrf
                    <button type="submit"
                            class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                        <i class="fas fa-copy mr-2"></i> Duplica
                    </button>
                </form>
                <form method="POST" action="{{ route('admin.email-templates.toggle-attivo', $template->id) }}" class="inline">
                    @csrf
                    @method('PATCH')
                    <button type="submit"
                            class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition">
                        <i class="fas fa-power-off mr-2"></i>
                        {{ $template->attivo ? 'Disattiva' : 'Attiva' }}
                    </button>
                </form>
                <button type="button"
                        onclick="document.getElementById('test-email-section').classList.toggle('hidden')"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    <i class="fas fa-paper-plane mr-2"></i> Invia Test
                </button>
                <form method="POST" action="{{ route('admin.email-templates.destroy', $template->id) }}" class="inline" id="delete-form">
                    @csrf
                    @method('DELETE')
                    <button type="button"
                            onclick="if(confirm('Eliminare il template {{ $template->nome }}?\n\nQuesta azione è irreversibile!')) { document.getElementById('delete-form').submit(); }"
                            class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                        <i class="fas fa-trash mr-2"></i> Elimina
                    </button>
                </form>
            </div>
        </div>

        <!-- Sezione Test Email (nascosta di default) -->
        <div id="test-email-section" class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-6 hidden">
            <h3 class="text-lg font-bold text-gray-800 mb-4">
                <i class="fas fa-paper-plane text-blue-600 mr-2"></i> Invia Email di Test
            </h3>
            <form method="POST" action="{{ route('admin.email-templates.test', $template->id) }}">
                @csrf
                <div class="flex gap-2">
                    <input type="email"
                           name="email_test"
                           placeholder="Inserisci email destinatario"
                           required
                           class="flex-1 px-4 py-2 border border-blue-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia">
                    <button type="submit"
                            class="px-6 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition">
                        <i class="fas fa-paper-plane mr-2"></i> Invia
                    </button>
                </div>
                <p class="text-sm text-blue-700 mt-2">
                    <i class="fas fa-info-circle mr-1"></i>
                    L'email verrà inviata con variabili di esempio per testare il template
                </p>
            </form>
        </div>

        <!-- Informazioni Template -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4 pb-2 border-b">
                <i class="fas fa-info-circle text-fucsia-magia mr-2"></i> Informazioni
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-sm font-medium text-gray-500">Codice</p>
                    <p class="text-gray-800">
                        <code class="bg-gray-100 px-2 py-1 rounded">{{ $template->codice }}</code>
                    </p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Tipo</p>
                    <p class="text-gray-800">
                        <span class="px-2 py-1 inline-flex text-sm rounded-full bg-purple-100 text-purple-800">
                            {{ ucfirst($template->tipo) }}
                        </span>
                    </p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Creato il</p>
                    <p class="text-gray-800">{{ $template->created_at->format('d/m/Y H:i') }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Ultima modifica</p>
                    <p class="text-gray-800">{{ $template->updated_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>
        </div>

        <!-- Oggetto Email -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4 pb-2 border-b">
                <i class="fas fa-envelope text-fucsia-magia mr-2"></i> Oggetto Email
            </h3>
            <p class="text-gray-800 bg-gray-50 p-4 rounded-lg border border-gray-200">
                {{ $template->oggetto }}
            </p>
        </div>

        <!-- Variabili Disponibili -->
        @if($template->variabili_disponibili && count($template->variabili_disponibili) > 0)
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4 pb-2 border-b">
                <i class="fas fa-code text-fucsia-magia mr-2"></i> Variabili Disponibili
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                @foreach($template->variabili_disponibili as $variabile)
                <div class="bg-gray-50 p-3 rounded-lg">
                    <code class="text-sm bg-white px-2 py-1 rounded border border-gray-200">{{'{{'}}{{ $variabile }}{{'}}'}}</code>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Corpo HTML -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4 pb-2 border-b flex items-center justify-between">
                <span><i class="fas fa-code text-fucsia-magia mr-2"></i> Corpo HTML</span>
                <button type="button"
                        onclick="document.getElementById('html-preview').classList.toggle('hidden'); this.textContent = this.textContent === 'Mostra Anteprima' ? 'Nascondi Anteprima' : 'Mostra Anteprima';"
                        class="text-sm px-3 py-1 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">
                    Mostra Anteprima
                </button>
            </h3>
            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 overflow-x-auto">
                <pre class="text-sm text-gray-800 font-mono whitespace-pre-wrap">{{ $template->corpo_html }}</pre>
            </div>

            <!-- Anteprima HTML (nascosta di default) -->
            <div id="html-preview" class="mt-4 hidden">
                <h4 class="text-md font-bold text-gray-700 mb-2">Anteprima:</h4>
                <div class="bg-white p-4 rounded-lg border border-gray-300">
                    {!! $template->corpo_html !!}
                </div>
            </div>
        </div>

        <!-- Corpo Testo -->
        @if($template->corpo_testo)
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4 pb-2 border-b">
                <i class="fas fa-align-left text-fucsia-magia mr-2"></i> Corpo Testo Semplice
            </h3>
            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                <pre class="text-sm text-gray-800 whitespace-pre-wrap">{{ $template->corpo_testo }}</pre>
            </div>
        </div>
        @endif

        <!-- Bottone Torna Indietro -->
        <div class="text-center">
            <a href="{{ route('admin.email-templates.index') }}"
               class="inline-block px-6 py-3 bg-gray-200 text-gray-700 font-medium rounded-lg hover:bg-gray-300 transition">
                <i class="fas fa-arrow-left mr-2"></i> Torna alla Lista
            </a>
        </div>

    </div>

</div>
@endsection
