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

            <!-- DATI BASE -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">
                    <i class="fas fa-info-circle text-fucsia-magia mr-2"></i>Dati Base
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Data <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="data_rilevazione" value="{{ old('data_rilevazione', date('Y-m-d')) }}"
                               class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia @error('data_rilevazione') border-red-500 @enderror" required>
                        @error('data_rilevazione')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Sede</label>
                        <select name="sede" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia">
                            <option value="">Seleziona...</option>
                            @foreach($sedi as $sede)
                                <option value="{{ $sede }}" {{ old('sede') == $sede ? 'selected' : '' }}>{{ $sede }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Peso (kg) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" step="0.01" name="peso" value="{{ old('peso') }}"
                               placeholder="es. 65.5"
                               class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia @error('peso') border-red-500 @enderror" required>
                        @error('peso')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">BMI</label>
                        <input type="number" step="0.1" name="bmi" value="{{ old('bmi') }}"
                               placeholder="es. 22.5"
                               class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia">
                    </div>
                </div>
            </div>

            <!-- COMPOSIZIONE CORPOREA -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">
                    <i class="fas fa-chart-pie text-fucsia-magia mr-2"></i>Composizione Corporea
                </h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Peso Senza Grassi (kg)</label>
                        <input type="number" step="0.01" name="peso_corporeo_senza_grassi" value="{{ old('peso_corporeo_senza_grassi') }}"
                               placeholder="es. 45.2"
                               class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Muscolo Scheletrico (%)</label>
                        <input type="number" step="0.1" name="muscolo_scheletrico" value="{{ old('muscolo_scheletrico') }}"
                               placeholder="es. 35.5"
                               class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Grasso Corporeo (%)</label>
                        <input type="number" step="0.1" name="grasso_corporeo" value="{{ old('grasso_corporeo') }}"
                               placeholder="es. 28.3"
                               class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Grasso Sottocutaneo (%)</label>
                        <input type="number" step="0.1" name="grasso_sottocutaneo" value="{{ old('grasso_sottocutaneo') }}"
                               placeholder="es. 24.1"
                               class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Grasso Viscerale</label>
                        <input type="number" name="grasso_viscerale" value="{{ old('grasso_viscerale') }}"
                               placeholder="es. 5"
                               class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Acqua Corporea (%)</label>
                        <input type="number" step="0.1" name="acqua_corporea" value="{{ old('acqua_corporea') }}"
                               placeholder="es. 52.8"
                               class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Proteine (%)</label>
                        <input type="number" step="0.1" name="proteine" value="{{ old('proteine') }}"
                               placeholder="es. 18.2"
                               class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia">
                    </div>
                </div>
            </div>

            <!-- MASSA CORPOREA -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">
                    <i class="fas fa-dumbbell text-fucsia-magia mr-2"></i>Massa Corporea
                </h3>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Massa Muscolare (kg)</label>
                        <input type="number" step="0.01" name="massa_muscolare" value="{{ old('massa_muscolare') }}"
                               placeholder="es. 42.3"
                               class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Massa Ossea (kg)</label>
                        <input type="number" step="0.01" name="massa_ossea" value="{{ old('massa_ossea') }}"
                               placeholder="es. 2.5"
                               class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia">
                    </div>
                </div>
            </div>

            <!-- METABOLISMO -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">
                    <i class="fas fa-fire text-fucsia-magia mr-2"></i>Metabolismo
                </h3>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">BMR (kcal)</label>
                        <input type="number" name="bmr" value="{{ old('bmr') }}"
                               placeholder="es. 1450"
                               class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Età Metabolica (anni)</label>
                        <input type="number" name="eta_metabolica" value="{{ old('eta_metabolica') }}"
                               placeholder="es. 28"
                               class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia">
                    </div>
                </div>
            </div>

            <!-- NOTE -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">
                    <i class="fas fa-sticky-note text-fucsia-magia mr-2"></i>Note
                </h3>
                <textarea name="note" rows="3"
                          placeholder="Aggiungi eventuali note o osservazioni..."
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia">{{ old('note') }}</textarea>
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
