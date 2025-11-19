@extends('layouts.admin')

@section('titolo', 'Dettagli Template Email')

@section('contenuto')
<div class="p-6">

    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">{{ $template->nome }}</h2>
            <p class="text-gray-600 mt-1">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-viola-magia bg-opacity-10 text-viola-magia">
                    <i class="fas fa-tag mr-1"></i>
                    {{ $template->tipo }}
                </span>
            </p>
        </div>
        <div class="mt-4 md:mt-0 flex gap-2">
            <a href="{{ route('admin.template-email.edit', $template->id) }}"
               class="inline-flex items-center px-4 py-2 bg-yellow-100 text-yellow-700 rounded-lg hover:bg-yellow-200 transition">
                <i class="fas fa-edit mr-2"></i>
                Modifica
            </a>
            <a href="{{ route('admin.template-email.index') }}"
               class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                <i class="fas fa-arrow-left mr-2"></i>
                Torna alla Lista
            </a>
        </div>
    </div>

    <!-- Messaggi Feedback -->
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

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Colonna Sinistra: Info Template -->
        <div class="lg:col-span-1 space-y-6">

            <!-- Info Base -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-info-circle text-viola-magia mr-2"></i>
                    Informazioni
                </h3>

                <div class="space-y-4">
                    <!-- Stato -->
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Stato</p>
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
                    </div>

                    <!-- Invio Automatico -->
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Invio</p>
                        @if($template->invia_automaticamente)
                            <span class="inline-flex items-center text-sm">
                                <i class="fas fa-robot text-blue-600 mr-2"></i>
                                Automatico
                            </span>
                        @else
                            <span class="inline-flex items-center text-sm">
                                <i class="fas fa-user text-gray-600 mr-2"></i>
                                Manuale
                            </span>
                        @endif
                    </div>

                    <!-- Mittente -->
                    @if($template->mittente_nome || $template->mittente_email)
                        <div>
                            <p class="text-sm text-gray-600 mb-1">Mittente Personalizzato</p>
                            <p class="text-sm font-medium text-gray-900">
                                {{ $template->mittente_nome }}<br>
                                <span class="text-gray-600">{{ $template->mittente_email }}</span>
                            </p>
                        </div>
                    @else
                        <div>
                            <p class="text-sm text-gray-600 mb-1">Mittente</p>
                            <p class="text-sm text-gray-900">Usa configurazione SMTP globale</p>
                        </div>
                    @endif

                    <!-- Date -->
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Creato</p>
                        <p class="text-sm text-gray-900">{{ $template->created_at->format('d/m/Y H:i') }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-600 mb-1">Ultima Modifica</p>
                        <p class="text-sm text-gray-900">{{ $template->updated_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
            </div>

            <!-- Variabili Disponibili -->
            @if($template->variabili_disponibili && count($template->variabili_disponibili) > 0)
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-code text-viola-magia mr-2"></i>
                        Variabili Disponibili
                    </h3>

                    <div class="space-y-2">
                        @foreach($template->variabili_disponibili as $variabile)
                            <div class="flex items-center text-sm">
                                <code class="px-2 py-1 bg-gray-100 text-viola-magia rounded font-mono">
                                    {{ '{{' . $variabile . '}}' }}
                                </code>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Note -->
            @if($template->note)
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-sticky-note text-viola-magia mr-2"></i>
                        Note
                    </h3>
                    <p class="text-sm text-gray-700 whitespace-pre-line">{{ $template->note }}</p>
                </div>
            @endif

            <!-- Invia Email Test -->
            <div class="bg-gradient-to-r from-viola-magia to-fucsia-magia rounded-lg shadow p-6 text-white">
                <h3 class="text-lg font-semibold mb-4 flex items-center">
                    <i class="fas fa-paper-plane mr-2"></i>
                    Invia Email di Test
                </h3>

                <form action="{{ route('admin.template-email.test', $template->id) }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-2">Destinatario Email Test</label>
                        <input type="email"
                               name="email_test"
                               class="w-full px-4 py-2 rounded-lg text-gray-900"
                               placeholder="tuo@email.com"
                               required>
                    </div>
                    <button type="submit"
                            class="w-full px-4 py-2 bg-white text-viola-magia font-semibold rounded-lg hover:bg-gray-100 transition">
                        <i class="fas fa-envelope mr-2"></i>
                        Invia Test
                    </button>
                </form>

                <p class="text-xs mt-3 opacity-90">L'email verrà inviata con dati di esempio</p>
            </div>

        </div>

        <!-- Colonna Destra: Preview Template -->
        <div class="lg:col-span-2">

            <!-- Oggetto -->
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-heading text-viola-magia mr-2"></i>
                    Oggetto Email
                </h3>
                <p class="text-gray-900 font-medium">{{ $template->oggetto }}</p>
            </div>

            <!-- Preview HTML -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="bg-gradient-to-r from-viola-magia to-fucsia-magia px-6 py-4">
                    <h3 class="text-lg font-semibold text-white flex items-center">
                        <i class="fas fa-eye mr-2"></i>
                        Preview Email (HTML)
                    </h3>
                </div>

                <div class="p-6">
                    <!-- Iframe per preview sicura -->
                    <div class="border border-gray-300 rounded-lg overflow-hidden">
                        <iframe id="preview-frame"
                                class="w-full"
                                style="min-height: 600px;"
                                frameborder="0"></iframe>
                    </div>
                </div>
            </div>

            <!-- Corpo Testo (se presente) -->
            @if($template->corpo_text)
                <div class="bg-white rounded-lg shadow p-6 mt-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-align-left text-viola-magia mr-2"></i>
                        Versione Testo Semplice
                    </h3>
                    <pre class="bg-gray-50 p-4 rounded-lg text-sm text-gray-700 overflow-x-auto whitespace-pre-wrap font-mono">{{ $template->corpo_text }}</pre>
                </div>
            @endif

        </div>

    </div>

</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Carica preview HTML nell'iframe
        const iframe = document.getElementById('preview-frame');
        const htmlContent = @json($template->corpo_html);

        iframe.srcdoc = htmlContent;

        // Aggiusta altezza iframe dopo il caricamento
        iframe.onload = function() {
            try {
                const iframeHeight = iframe.contentWindow.document.body.scrollHeight;
                iframe.style.height = (iframeHeight + 20) + 'px';
            } catch(e) {
                console.log('Impossibile calcolare altezza iframe');
            }
        };
    });
</script>
@endpush
@endsection
