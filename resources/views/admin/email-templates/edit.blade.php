@extends('layouts.admin')

@section('titolo', 'Modifica Email Template')

@section('contenuto')
<div class="p-6">

    <div class="max-w-5xl mx-auto">

        <!-- Header -->
        <div class="mb-6">
            <a href="{{ route('admin.email-templates.index') }}" class="text-fucsia-magia hover:text-viola-magia mb-2 inline-block">
                <i class="fas fa-arrow-left mr-2"></i> Torna alla lista
            </a>
            <h2 class="text-2xl font-bold text-gray-800">Modifica Email Template</h2>
            <p class="text-gray-600 mt-1">{{ $template->nome }}</p>
        </div>

        <!-- Form -->
        <form method="POST" action="{{ route('admin.email-templates.update', $template->id) }}" class="bg-white rounded-lg shadow p-6">
            @csrf
            @method('PUT')

            <!-- Sezione Informazioni Base -->
            <div class="mb-8">
                <h3 class="text-lg font-bold text-gray-800 mb-4 pb-2 border-b">
                    <i class="fas fa-info-circle text-fucsia-magia mr-2"></i> Informazioni Base
                </h3>
                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Codice Template *
                            <span class="text-gray-500 text-xs">(univoco, es: benvenuto_cliente)</span>
                        </label>
                        <input type="text" name="codice" value="{{ old('codice', $template->codice) }}" required
                               placeholder="es: benvenuto_cliente"
                               pattern="[a-z0-9_-]+"
                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-fucsia-magia @error('codice') border-red-500 @enderror">
                        <p class="text-xs text-gray-500 mt-1">Usa solo lettere minuscole, numeri, trattini e underscore</p>
                        @error('codice')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nome Template *</label>
                        <input type="text" name="nome" value="{{ old('nome', $template->nome) }}" required
                               placeholder="es: Email di Benvenuto Cliente"
                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-fucsia-magia @error('nome') border-red-500 @enderror">
                        @error('nome')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tipo *</label>
                        <select name="tipo" required
                                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-fucsia-magia @error('tipo') border-red-500 @enderror">
                            <option value="">Seleziona...</option>
                            @foreach($tipiDisponibili as $valore => $etichetta)
                                <option value="{{ $valore }}" {{ old('tipo', $template->tipo) == $valore ? 'selected' : '' }}>
                                    {{ $etichetta }}
                                </option>
                            @endforeach
                        </select>
                        @error('tipo')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Sezione Contenuto Email -->
            <div class="mb-8">
                <h3 class="text-lg font-bold text-gray-800 mb-4 pb-2 border-b">
                    <i class="fas fa-envelope text-fucsia-magia mr-2"></i> Contenuto Email
                </h3>
                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Oggetto Email *</label>
                        <input type="text" name="oggetto" value="{{ old('oggetto', $template->oggetto) }}" required
                               placeholder="es: Benvenuta in MA.GIA {{nome_cliente}}!"
                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-fucsia-magia @error('oggetto') border-red-500 @enderror">
                        <p class="text-xs text-gray-500 mt-1">Puoi usare variabili come {{nome_cliente}}, {{cognome_cliente}}, ecc.</p>
                        @error('oggetto')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Corpo HTML *</label>
                        <textarea name="corpo_html" rows="15" required
                                  placeholder="Inserisci il contenuto HTML dell'email..."
                                  class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-fucsia-magia font-mono text-sm @error('corpo_html') border-red-500 @enderror">{{ old('corpo_html', $template->corpo_html) }}</textarea>
                        <p class="text-xs text-gray-500 mt-1">
                            Puoi usare HTML completo. Variabili disponibili:
                            @foreach($variabiliComuni as $codice => $desc)
                                <code class="bg-gray-100 px-1">{{'{{'}}{{ $codice }}{{'}}'}}</code>{{ !$loop->last ? ',' : '' }}
                            @endforeach
                        </p>
                        @error('corpo_html')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Corpo Testo Semplice
                            <span class="text-gray-500 text-xs">(opzionale, viene generato automaticamente da HTML)</span>
                        </label>
                        <textarea name="corpo_testo" rows="8"
                                  placeholder="Versione testo semplice per client email che non supportano HTML..."
                                  class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-fucsia-magia font-mono text-sm @error('corpo_testo') border-red-500 @enderror">{{ old('corpo_testo', $template->corpo_testo) }}</textarea>
                        @error('corpo_testo')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Sezione Variabili -->
            <div class="mb-8">
                <h3 class="text-lg font-bold text-gray-800 mb-4 pb-2 border-b">
                    <i class="fas fa-code text-fucsia-magia mr-2"></i> Variabili Disponibili
                </h3>
                <p class="text-sm text-gray-600 mb-4">
                    Seleziona le variabili che userai in questo template. Questo aiuta a documentare il template.
                </p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    @foreach($variabiliComuni as $codice => $descrizione)
                    @php
                        $variabiliTemplate = old('variabili_disponibili', $template->variabili_disponibili ?? []);
                        $isChecked = is_array($variabiliTemplate) && in_array($codice, $variabiliTemplate);
                    @endphp
                    <label class="inline-flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 cursor-pointer">
                        <input type="checkbox"
                               name="variabili_disponibili[]"
                               value="{{ $codice }}"
                               {{ $isChecked ? 'checked' : '' }}
                               class="rounded text-fucsia-magia focus:ring-fucsia-magia">
                        <span class="ml-2 text-sm">
                            <code class="text-xs bg-white px-2 py-0.5 rounded">{{'{{'}}{{ $codice }}{{'}}'}}</code>
                            <span class="text-gray-600 ml-2">{{ $descrizione }}</span>
                        </span>
                    </label>
                    @endforeach
                </div>
            </div>

            <!-- Sezione Test Email -->
            <div class="mb-8">
                <h3 class="text-lg font-bold text-gray-800 mb-4 pb-2 border-b">
                    <i class="fas fa-paper-plane text-fucsia-magia mr-2"></i> Test Email
                </h3>
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <p class="text-sm text-blue-800 mb-3">
                        <i class="fas fa-info-circle mr-1"></i>
                        Invia un'email di test per verificare il template prima di attivarlo
                    </p>
                    <div class="flex gap-2">
                        <input type="email"
                               id="email_test"
                               placeholder="Inserisci email destinatario"
                               class="flex-1 px-4 py-2 border border-blue-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia">
                        <button type="button"
                                onclick="inviaEmailTest()"
                                class="px-6 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition">
                            <i class="fas fa-paper-plane mr-2"></i> Invia Test
                        </button>
                    </div>
                </div>
            </div>

            <!-- Sezione Stato -->
            <div class="mb-8">
                <h3 class="text-lg font-bold text-gray-800 mb-4 pb-2 border-b">
                    <i class="fas fa-toggle-on text-fucsia-magia mr-2"></i> Stato
                </h3>
                <div>
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="attivo" value="1" {{ old('attivo', $template->attivo) ? 'checked' : '' }}
                               class="rounded text-fucsia-magia focus:ring-fucsia-magia">
                        <span class="ml-2 text-sm text-gray-700">Template attivo (può essere utilizzato dal sistema)</span>
                    </label>
                </div>
            </div>

            <!-- Bottoni Azione -->
            <div class="flex items-center gap-3 pt-6 border-t">
                <button type="submit"
                        class="px-6 py-3 bg-gradient-to-r from-viola-magia to-fucsia-magia text-white font-semibold rounded-lg hover:shadow-lg transition">
                    <i class="fas fa-save mr-2"></i> Salva Modifiche
                </button>
                <a href="{{ route('admin.email-templates.index') }}"
                   class="px-6 py-3 bg-gray-200 text-gray-700 font-medium rounded-lg hover:bg-gray-300 transition">
                    <i class="fas fa-times mr-2"></i> Annulla
                </a>
            </div>

        </form>

    </div>

</div>

@push('scripts')
<script>
function inviaEmailTest() {
    const email = document.getElementById('email_test').value;

    if (!email) {
        alert('Inserisci un indirizzo email');
        return;
    }

    if (!confirm('Inviare email di test a ' + email + '?')) {
        return;
    }

    // Crea un form temporaneo per inviare la richiesta
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route('admin.email-templates.test', $template->id) }}';

    // CSRF token
    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = '_token';
    csrfInput.value = '{{ csrf_token() }}';
    form.appendChild(csrfInput);

    // Email test
    const emailInput = document.createElement('input');
    emailInput.type = 'hidden';
    emailInput.name = 'email_test';
    emailInput.value = email;
    form.appendChild(emailInput);

    document.body.appendChild(form);
    form.submit();
}
</script>
@endpush

@endsection
