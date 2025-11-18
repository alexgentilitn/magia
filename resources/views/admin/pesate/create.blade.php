@extends('layouts.admin')

@section('titolo', 'Nuova Pesata')

@section('contenuto')
<div class="p-6">

    <div class="mb-6">
        <a href="{{ route('admin.clienti.pesate.index', $cliente->id) }}" class="text-fucsia-magia hover:text-viola-magia mb-2 inline-block">
            <i class="fas fa-arrow-left mr-2"></i> Torna alle pesate
        </a>
        <h2 class="text-2xl font-bold text-gray-800">Nuova Pesata - {{ $cliente->nome }} {{ $cliente->cognome }}</h2>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <form method="POST" action="{{ route('admin.clienti.pesate.store', $cliente->id) }}">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <!-- Data Rilevazione -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Data Rilevazione <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="data_rilevazione" value="{{ old('data_rilevazione', date('Y-m-d')) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia @error('data_rilevazione') border-red-500 @enderror" required>
                    @error('data_rilevazione')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Sede -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Sede</label>
                    <select name="sede" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia">
                        <option value="">Seleziona sede...</option>
                        @foreach($sedi as $sede)
                            <option value="{{ $sede }}" {{ old('sede') == $sede ? 'selected' : '' }}>{{ $sede }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Peso -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Peso (kg) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" step="0.01" name="peso" value="{{ old('peso') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia @error('peso') border-red-500 @enderror" required>
                    @error('peso')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- BMI -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">BMI</label>
                    <input type="number" step="0.1" name="bmi" value="{{ old('bmi') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia @error('bmi') border-red-500 @enderror">
                    @error('bmi')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Peso Corporeo Senza Grassi -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Peso Senza Grassi (kg)</label>
                    <input type="number" step="0.01" name="peso_corporeo_senza_grassi" value="{{ old('peso_corporeo_senza_grassi') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia">
                </div>

                <!-- Muscolo Scheletrico -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Muscolo Scheletrico (%)</label>
                    <input type="number" step="0.1" name="muscolo_scheletrico" value="{{ old('muscolo_scheletrico') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia">
                </div>

                <!-- Grasso Corporeo -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Grasso Corporeo (%)</label>
                    <input type="number" step="0.1" name="grasso_corporeo" value="{{ old('grasso_corporeo') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia">
                </div>

                <!-- Grasso Sottocutaneo -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Grasso Sottocutaneo (%)</label>
                    <input type="number" step="0.1" name="grasso_sottocutaneo" value="{{ old('grasso_sottocutaneo') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia">
                </div>

                <!-- Grasso Viscerale -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Grasso Viscerale</label>
                    <input type="number" name="grasso_viscerale" value="{{ old('grasso_viscerale') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia">
                </div>

                <!-- Acqua Corporea -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Acqua Corporea (%)</label>
                    <input type="number" step="0.1" name="acqua_corporea" value="{{ old('acqua_corporea') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia">
                </div>

                <!-- Massa Muscolare -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Massa Muscolare (kg)</label>
                    <input type="number" step="0.01" name="massa_muscolare" value="{{ old('massa_muscolare') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia">
                </div>

                <!-- Massa Ossea -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Massa Ossea (kg)</label>
                    <input type="number" step="0.01" name="massa_ossea" value="{{ old('massa_ossea') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia">
                </div>

                <!-- Proteine -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Proteine (%)</label>
                    <input type="number" step="0.1" name="proteine" value="{{ old('proteine') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia">
                </div>

                <!-- BMR -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">BMR (cal)</label>
                    <input type="number" name="bmr" value="{{ old('bmr') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia">
                </div>

                <!-- Età Metabolica -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Età Metabolica</label>
                    <input type="number" name="eta_metabolica" value="{{ old('eta_metabolica') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia">
                </div>

                <!-- Note -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Note</label>
                    <textarea name="note" rows="3"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia">{{ old('note') }}</textarea>
                </div>

            </div>

            <div class="flex justify-end gap-3 mt-6">
                <a href="{{ route('admin.clienti.pesate.index', $cliente->id) }}"
                   class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                    Annulla
                </a>
                <button type="submit"
                        class="px-6 py-2 bg-gradient-to-r from-viola-magia to-fucsia-magia text-white rounded-lg hover:shadow-lg">
                    <i class="fas fa-save mr-2"></i> Salva Pesata
                </button>
            </div>

        </form>
    </div>

</div>
@endsection
