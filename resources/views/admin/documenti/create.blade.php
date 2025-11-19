@extends('layouts.admin')

@section('titolo', 'Carica Documento')

@section('contenuto')
<div class="p-6 max-w-4xl mx-auto">

    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Carica Documento</h2>
            <p class="text-gray-600 mt-1">Cliente: {{ $cliente->nome_completo }}</p>
        </div>
        <a href="{{ route('admin.clienti.documenti.index', $cliente->id) }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
            <i class="fas fa-arrow-left mr-2"></i>Annulla
        </a>
    </div>

    <form action="{{ route('admin.clienti.documenti.store', $cliente->id) }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="bg-white rounded-lg shadow p-6 space-y-6">

            <!-- Tipo Documento -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Tipo Documento <span class="text-red-500">*</span>
                </label>
                <select name="tipo_documento" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-viola-magia @error('tipo_documento') border-red-500 @enderror">
                    <option value="">Seleziona tipo...</option>
                    <option value="certificato_medico" {{ old('tipo_documento') == 'certificato_medico' ? 'selected' : '' }}>Certificato Medico</option>
                    <option value="documento_identita" {{ old('tipo_documento') == 'documento_identita' ? 'selected' : '' }}>Documento d'Identità</option>
                    <option value="consenso_privacy" {{ old('tipo_documento') == 'consenso_privacy' ? 'selected' : '' }}>Consenso Privacy</option>
                    <option value="consenso_immagini" {{ old('tipo_documento') == 'consenso_immagini' ? 'selected' : '' }}>Consenso Uso Immagini</option>
                    <option value="contratto" {{ old('tipo_documento') == 'contratto' ? 'selected' : '' }}>Contratto</option>
                    <option value="altro" {{ old('tipo_documento') == 'altro' ? 'selected' : '' }}>Altro</option>
                </select>
                @error('tipo_documento')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <!-- File -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    File <span class="text-red-500">*</span>
                </label>
                <input type="file" name="file" required accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-viola-magia @error('file') border-red-500 @enderror">
                <p class="text-xs text-gray-500 mt-1">Formati supportati: PDF, JPG, PNG, DOC, XLS (max 10MB)</p>
                @error('file')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <!-- Descrizione -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Descrizione</label>
                <textarea name="descrizione" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-viola-magia"
                          placeholder="Aggiungi una descrizione...">{{ old('descrizione') }}</textarea>
            </div>

            <!-- Data Scadenza -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Data Scadenza</label>
                <input type="date" name="data_scadenza" value="{{ old('data_scadenza') }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-viola-magia">
                <p class="text-xs text-gray-500 mt-1">Opzionale - riceverai un avviso prima della scadenza</p>
            </div>

            <!-- Stato e Visibilità -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Stato</label>
                    <select name="stato" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-viola-magia">
                        <option value="valido" {{ old('stato', 'valido') == 'valido' ? 'selected' : '' }}>Valido</option>
                        <option value="scaduto" {{ old('stato') == 'scaduto' ? 'selected' : '' }}>Scaduto</option>
                        <option value="in_verifica" {{ old('stato') == 'in_verifica' ? 'selected' : '' }}>In Verifica</option>
                    </select>
                </div>

                <div class="flex items-center">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" name="visibile_cliente" value="1" {{ old('visibile_cliente') ? 'checked' : '' }}
                               class="w-5 h-5 text-viola-magia border-gray-300 rounded focus:ring-viola-magia">
                        <span class="ml-3">
                            <span class="font-medium text-gray-900">Visibile al cliente</span>
                            <span class="block text-sm text-gray-500">Il cliente potrà visualizzare il documento</span>
                        </span>
                    </label>
                </div>
            </div>

            <!-- Note -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Note Interne</label>
                <textarea name="note" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-viola-magia"
                          placeholder="Note visibili solo agli amministratori...">{{ old('note') }}</textarea>
            </div>

            <!-- Azioni -->
            <div class="flex justify-end gap-4 pt-6 border-t">
                <a href="{{ route('admin.clienti.documenti.index', $cliente->id) }}" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                    <i class="fas fa-times mr-2"></i>Annulla
                </a>
                <button type="submit" class="px-6 py-2 bg-gradient-to-r from-viola-magia to-fucsia-magia text-white rounded-lg hover:shadow-lg transition">
                    <i class="fas fa-upload mr-2"></i>Carica Documento
                </button>
            </div>

        </div>
    </form>

</div>
@endsection
