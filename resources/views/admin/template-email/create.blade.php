@extends('layouts.admin')

@section('titolo', 'Nuovo Template Email')

@section('contenuto')
<div class="p-6">

    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Nuovo Template Email</h2>
            <p class="text-gray-600 mt-1">Crea un nuovo template per l'invio automatico delle email</p>
        </div>
        <div class="mt-4 md:mt-0">
            <a href="{{ route('admin.template-email.index') }}"
               class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                <i class="fas fa-arrow-left mr-2"></i>
                Torna alla Lista
            </a>
        </div>
    </div>

    <!-- Form -->
    <form action="{{ route('admin.template-email.store') }}" method="POST">
        @csrf

        <div class="bg-white rounded-lg shadow p-6 space-y-6">

            <!-- Informazioni Base -->
            <div>
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-info-circle text-viola-magia mr-2"></i>
                    Informazioni Base
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Tipo -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Tipo Template <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               name="tipo"
                               value="{{ old('tipo') }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-viola-magia focus:border-transparent @error('tipo') border-red-500 @enderror"
                               placeholder="es: benvenuto, programma, promemoria_24h"
                               required>
                        <p class="mt-1 text-xs text-gray-500">Identificativo univoco (lowercase, no spazi)</p>
                        @error('tipo')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Nome -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Nome Template <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               name="nome"
                               value="{{ old('nome') }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-viola-magia focus:border-transparent @error('nome') border-red-500 @enderror"
                               placeholder="es: Email di Benvenuto"
                               required>
                        @error('nome')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Oggetto Email -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Oggetto Email <span class="text-red-500">*</span>
                </label>
                <input type="text"
                       name="oggetto"
                       value="{{ old('oggetto') }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-viola-magia focus:border-transparent @error('oggetto') border-red-500 @enderror"
                       placeholder="es: Benvenuta {{nome_cliente}} in MA.GIA DONNA!"
                       required>
                <p class="mt-1 text-xs text-gray-500">Puoi usare variabili come {{nome_cliente}}, {{email_cliente}}, ecc.</p>
                @error('oggetto')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Corpo HTML -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Corpo Email (HTML) <span class="text-red-500">*</span>
                </label>
                <textarea name="corpo_html"
                          rows="15"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-viola-magia focus:border-transparent font-mono text-sm @error('corpo_html') border-red-500 @enderror"
                          required>{{ old('corpo_html') }}</textarea>
                <p class="mt-1 text-xs text-gray-500">HTML completo dell'email. Usa variabili come {{nome_cliente}}</p>
                @error('corpo_html')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Corpo Testo Semplice (opzionale) -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Corpo Email (Testo Semplice) <span class="text-gray-400 text-xs">(Opzionale)</span>
                </label>
                <textarea name="corpo_text"
                          rows="8"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-viola-magia focus:border-transparent font-mono text-sm"
                          placeholder="Versione testo semplice per client email che non supportano HTML">{{ old('corpo_text') }}</textarea>
                <p class="mt-1 text-xs text-gray-500">Consigliato per compatibilità con tutti i client email</p>
            </div>

            <!-- Mittente Personalizzato -->
            <div>
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-user text-viola-magia mr-2"></i>
                    Mittente Personalizzato <span class="text-gray-400 text-xs ml-2">(Opzionale)</span>
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Nome Mittente -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Nome Mittente
                        </label>
                        <input type="text"
                               name="mittente_nome"
                               value="{{ old('mittente_nome') }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-viola-magia focus:border-transparent"
                               placeholder="es: MA.GIA DONNA Team">
                        <p class="mt-1 text-xs text-gray-500">Se vuoto, usa configurazione SMTP globale</p>
                    </div>

                    <!-- Email Mittente -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Email Mittente
                        </label>
                        <input type="email"
                               name="mittente_email"
                               value="{{ old('mittente_email') }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-viola-magia focus:border-transparent"
                               placeholder="es: info@magiadonna.it">
                        <p class="mt-1 text-xs text-gray-500">Se vuoto, usa configurazione SMTP globale</p>
                    </div>
                </div>
            </div>

            <!-- Variabili Disponibili -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Variabili Disponibili (JSON) <span class="text-gray-400 text-xs">(Opzionale)</span>
                </label>
                <textarea name="variabili_disponibili"
                          rows="4"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-viola-magia focus:border-transparent font-mono text-sm"
                          placeholder='["nome_cliente", "email_cliente", "nome_programma", "data_lezione"]'>{{ old('variabili_disponibili') }}</textarea>
                <p class="mt-1 text-xs text-gray-500">Array JSON delle variabili utilizzabili in questo template (solo documentazione)</p>
            </div>

            <!-- Note -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Note <span class="text-gray-400 text-xs">(Opzionale)</span>
                </label>
                <textarea name="note"
                          rows="3"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-viola-magia focus:border-transparent"
                          placeholder="Note interne sul template...">{{ old('note') }}</textarea>
            </div>

            <!-- Opzioni -->
            <div>
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-cog text-viola-magia mr-2"></i>
                    Opzioni
                </h3>

                <div class="space-y-3">
                    <!-- Attivo -->
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox"
                               name="attivo"
                               value="1"
                               {{ old('attivo', true) ? 'checked' : '' }}
                               class="w-5 h-5 text-viola-magia border-gray-300 rounded focus:ring-viola-magia">
                        <span class="ml-3">
                            <span class="font-medium text-gray-900">Template Attivo</span>
                            <span class="block text-sm text-gray-500">Il template può essere utilizzato per l'invio</span>
                        </span>
                    </label>

                    <!-- Invio Automatico -->
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox"
                               name="invia_automaticamente"
                               value="1"
                               {{ old('invia_automaticamente') ? 'checked' : '' }}
                               class="w-5 h-5 text-viola-magia border-gray-300 rounded focus:ring-viola-magia">
                        <span class="ml-3">
                            <span class="font-medium text-gray-900">Invio Automatico</span>
                            <span class="block text-sm text-gray-500">L'email viene inviata automaticamente al verificarsi dell'evento</span>
                        </span>
                    </label>
                </div>
            </div>

            <!-- Azioni -->
            <div class="flex justify-end gap-4 pt-6 border-t">
                <a href="{{ route('admin.template-email.index') }}"
                   class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                    <i class="fas fa-times mr-2"></i>
                    Annulla
                </a>
                <button type="submit"
                        class="px-6 py-2 bg-gradient-to-r from-viola-magia to-fucsia-magia text-white rounded-lg hover:shadow-lg transition">
                    <i class="fas fa-save mr-2"></i>
                    Salva Template
                </button>
            </div>

        </div>
    </form>

</div>
@endsection
