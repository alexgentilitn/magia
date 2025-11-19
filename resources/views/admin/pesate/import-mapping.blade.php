@extends('layouts.admin')

@section('titolo', 'Mappa Colonne Excel')

@section('contenuto')
<div class="p-6">

    <!-- Header -->
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Mappa Colonne Excel</h2>
        <p class="text-gray-600 mt-1">Abbina le colonne del file Excel ai campi del database</p>
    </div>

    <!-- Info -->
    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6 rounded-lg">
        <div class="flex items-start">
            <i class="fas fa-info-circle text-blue-600 text-xl mr-3 mt-1"></i>
            <div>
                <p class="font-bold text-blue-900 mb-1">Come funziona:</p>
                <ol class="list-decimal list-inside text-blue-800 text-sm space-y-1">
                    <li>Per ogni campo obbligatorio, seleziona la colonna Excel corrispondente</li>
                    <li>I campi opzionali possono essere saltati selezionando "-- Salta campo --"</li>
                    <li>Sotto ogni select vedrai un'anteprima dei dati di quella colonna</li>
                    <li>Se il cliente non esiste, verrà creato automaticamente</li>
                </ol>
            </div>
        </div>
    </div>

    <!-- Form Mapping -->
    <form action="{{ route('admin.pesate.process-mapping') }}" method="POST">
        @csrf

        <div class="bg-white rounded-lg shadow overflow-hidden mb-6">
            <div class="p-6 bg-gradient-to-r from-viola-magia to-fucsia-magia">
                <h3 class="text-xl font-bold text-white flex items-center">
                    <i class="fas fa-columns mr-3"></i>
                    Mapping Colonne
                </h3>
            </div>

            <div class="p-6 space-y-6">
                <!-- Campi Obbligatori -->
                <div class="border-b border-gray-200 pb-6">
                    <h4 class="font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-asterisk text-red-500 text-xs mr-2"></i>
                        Campi Obbligatori
                    </h4>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Cognome -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Cognome <span class="text-red-500">*</span>
                            </label>
                            <select name="mapping[cognome]" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia">
                                <option value="">-- Seleziona colonna --</option>
                                @foreach($headers as $col => $header)
                                    <option value="{{ $col }}" {{ stripos($header, 'cognome') !== false ? 'selected' : '' }}>
                                        Colonna {{ $col }}: {{ $header ?: '(vuoto)' }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="mt-2 p-2 bg-gray-50 rounded text-xs font-mono overflow-x-auto" id="preview-cognome">
                                Seleziona una colonna per vedere l'anteprima
                            </div>
                        </div>

                        <!-- Nome -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Nome <span class="text-red-500">*</span>
                            </label>
                            <select name="mapping[nome]" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia">
                                <option value="">-- Seleziona colonna --</option>
                                @foreach($headers as $col => $header)
                                    <option value="{{ $col }}" {{ stripos($header, 'nome') !== false && stripos($header, 'cognome') === false ? 'selected' : '' }}>
                                        Colonna {{ $col }}: {{ $header ?: '(vuoto)' }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="mt-2 p-2 bg-gray-50 rounded text-xs font-mono overflow-x-auto" id="preview-nome">
                                Seleziona una colonna per vedere l'anteprima
                            </div>
                        </div>

                        <!-- Peso -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Peso (kg) <span class="text-red-500">*</span>
                            </label>
                            <select name="mapping[peso]" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia">
                                <option value="">-- Seleziona colonna --</option>
                                @foreach($headers as $col => $header)
                                    <option value="{{ $col }}" {{ stripos($header, 'peso') !== false || stripos($header, 'weight') !== false ? 'selected' : '' }}>
                                        Colonna {{ $col }}: {{ $header ?: '(vuoto)' }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="mt-2 p-2 bg-gray-50 rounded text-xs font-mono overflow-x-auto" id="preview-peso">
                                Seleziona una colonna per vedere l'anteprima
                            </div>
                        </div>

                        <!-- Data Rilevazione -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Data Rilevazione <span class="text-red-500">*</span>
                            </label>
                            <select name="mapping[data_rilevazione]" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia">
                                <option value="">-- Seleziona colonna --</option>
                                @foreach($headers as $col => $header)
                                    <option value="{{ $col }}" {{ stripos($header, 'data') !== false || stripos($header, 'date') !== false ? 'selected' : '' }}>
                                        Colonna {{ $col }}: {{ $header ?: '(vuoto)' }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="mt-2 p-2 bg-gray-50 rounded text-xs font-mono overflow-x-auto" id="preview-data_rilevazione">
                                Seleziona una colonna per vedere l'anteprima
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Codice Fiscale (Opzionale ma Raccomandato) -->
                <div class="border-b border-gray-200 pb-6">
                    <h4 class="font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-id-card text-blue-500 mr-2"></i>
                        Campo Raccomandato
                    </h4>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Codice Fiscale
                            <span class="text-sm text-gray-500">(usato per trovare cliente se nome/cognome non corrispondono)</span>
                        </label>
                        <select name="mapping[codice_fiscale]"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia">
                            <option value="skip">-- Salta campo --</option>
                            @foreach($headers as $col => $header)
                                <option value="{{ $col }}" {{ stripos($header, 'fiscale') !== false || stripos($header, 'cf') !== false || stripos($header, 'tax') !== false ? 'selected' : '' }}>
                                    Colonna {{ $col }}: {{ $header ?: '(vuoto)' }}
                                </option>
                            @endforeach
                        </select>
                        <div class="mt-2 p-2 bg-gray-50 rounded text-xs font-mono overflow-x-auto" id="preview-codice_fiscale">
                            Seleziona una colonna per vedere l'anteprima
                        </div>
                    </div>
                </div>

                <!-- Campi Opzionali -->
                <div>
                    <h4 class="font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-list text-gray-500 mr-2"></i>
                        Campi Opzionali
                        <button type="button" onclick="toggleOptional()" class="ml-3 text-sm text-fucsia-magia hover:underline">
                            Mostra/Nascondi
                        </button>
                    </h4>

                    <div id="optional-fields" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4" style="display: none;">
                        @php
                            $campiOpzionali = [
                                'bmi' => 'BMI',
                                'peso_corporeo_senza_grassi' => 'Peso senza grassi (kg)',
                                'muscolo_scheletrico' => 'Muscolo scheletrico (%)',
                                'grasso_corporeo' => 'Grasso corporeo (%)',
                                'grasso_sottocutaneo' => 'Grasso sottocutaneo (%)',
                                'grasso_viscerale' => 'Grasso viscerale',
                                'acqua_corporea' => 'Acqua corporea (%)',
                                'massa_muscolare' => 'Massa muscolare (kg)',
                                'massa_ossea' => 'Massa ossea (kg)',
                                'proteine' => 'Proteine (%)',
                                'bmr' => 'BMR (kcal)',
                                'eta_metabolica' => 'Età metabolica',
                            ];
                        @endphp

                        @foreach($campiOpzionali as $field => $label)
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">{{ $label }}</label>
                                <select name="mapping[{{ $field }}]"
                                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia">
                                    <option value="skip">-- Salta --</option>
                                    @foreach($headers as $col => $header)
                                        <option value="{{ $col }}">{{ $col }}: {{ Str::limit($header, 15) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Pulsanti -->
        <div class="flex gap-4">
            <button type="submit"
                    class="flex-1 px-6 py-3 bg-gradient-to-r from-green-500 to-green-600 text-white font-semibold rounded-lg hover:shadow-lg transition transform hover:-translate-y-0.5">
                <i class="fas fa-arrow-right mr-2"></i>
                Procedi con l'Anteprima
            </button>
            <a href="{{ route('admin.pesate.import') }}"
               class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                <i class="fas fa-arrow-left mr-2"></i>
                Indietro
            </a>
        </div>
    </form>

</div>

<script>
    // Dati di esempio dal server
    const sampleData = @json($sampleRows);

    // Funzione per aggiornare anteprima
    function updatePreview(field, column) {
        const previewDiv = document.getElementById(`preview-${field}`);

        if (!column || column === 'skip') {
            previewDiv.innerHTML = '<span class="text-gray-400">Campo saltato</span>';
            return;
        }

        const samples = sampleData.map(row => row[column]).filter(v => v != null);

        if (samples.length === 0) {
            previewDiv.innerHTML = '<span class="text-gray-400">(colonna vuota)</span>';
        } else {
            previewDiv.innerHTML = samples.slice(0, 3).map(v => `<span class="text-gray-700">${v}</span>`).join(', ') +
                                  (samples.length > 3 ? ', ...' : '');
        }
    }

    // Aggiungi listener a tutti i select
    document.addEventListener('DOMContentLoaded', function() {
        const selects = document.querySelectorAll('select[name^="mapping"]');

        selects.forEach(select => {
            const field = select.name.match(/mapping\[([^\]]+)\]/)[1];

            // Aggiorna anteprima iniziale
            updatePreview(field, select.value);

            // Aggiorna quando cambia
            select.addEventListener('change', function() {
                updatePreview(field, this.value);
            });
        });
    });

    // Toggle campi opzionali
    function toggleOptional() {
        const div = document.getElementById('optional-fields');
        div.style.display = div.style.display === 'none' ? 'grid' : 'none';
    }
</script>

@endsection
