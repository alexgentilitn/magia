@extends('layouts.admin')

@section('titolo', 'Mapping Colonne Import')

@section('contenuto')
<div class="p-6 max-w-7xl mx-auto">

    <!-- Header Compatto -->
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Mapping Colonne Import</h2>
            <p class="text-sm text-gray-600 mt-1">Sede: <strong>{{ session('import_sede') }}</strong> | File: <strong>{{ session('import_file_name') }}</strong></p>
        </div>

        <!-- Status Badge Auto-detection -->
        <div id="auto-detection-badge" class="px-4 py-2 bg-gray-100 rounded-lg text-sm">
            <i class="fas fa-magic text-gray-600 mr-2"></i>
            <span class="text-gray-700"><span id="auto-count">0</span>/{{ count($headers) }} colonne rilevate</span>
        </div>
    </div>

    <!-- Form Mapping -->
    <form action="{{ route('admin.pesate.process-mapping') }}" method="POST">
        @csrf

        <!-- Grid Layout: Colonne Obbligatorie + Opzionali -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">

            <!-- Colonna Sinistra: Campi Obbligatori -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-red-50 to-white">
                        <h3 class="font-bold text-gray-800 flex items-center">
                            <i class="fas fa-asterisk text-red-500 text-xs mr-2"></i>
                            Campi Obbligatori
                        </h3>
                    </div>

                    <div class="p-6 space-y-4">
                        @php
                            $campiObbligatori = [
                                'cognome' => ['label' => 'Cognome', 'icon' => 'fa-user', 'terms' => ['cognome', 'surname', 'last name']],
                                'nome' => ['label' => 'Nome', 'icon' => 'fa-user', 'terms' => ['nome', 'name', 'first name']],
                                'peso' => ['label' => 'Peso (kg)', 'icon' => 'fa-weight', 'terms' => ['peso', 'weight', 'kg']],
                            ];
                        @endphp

                        @foreach($campiObbligatori as $field => $config)
                            @php
                                $autoMatched = null;
                                foreach ($headers as $col => $header) {
                                    foreach ($config['terms'] as $term) {
                                        if ($field === 'nome' && stripos(strtolower($header), 'cognome') !== false) continue;
                                        if (stripos(strtolower($header), strtolower($term)) !== false) {
                                            $autoMatched = $col;
                                            break 2;
                                        }
                                    }
                                }
                            @endphp

                            <div class="campo-mapping" data-auto-matched="{{ $autoMatched ? 'true' : 'false' }}">
                                <div class="flex items-center justify-between mb-2">
                                    <label class="text-sm font-medium text-gray-700 flex items-center">
                                        <i class="fas {{ $config['icon'] }} text-gray-400 mr-2"></i>
                                        {{ $config['label'] }} <span class="text-red-500 ml-1">*</span>
                                    </label>
                                    @if($autoMatched)
                                        <span class="px-2 py-0.5 bg-green-100 text-green-700 text-xs rounded-full font-semibold">
                                            <i class="fas fa-check-circle mr-1"></i>Auto
                                        </span>
                                    @endif
                                </div>

                                <select name="mapping[{{ $field }}]" required
                                        class="w-full px-3 py-2 text-sm border {{ $autoMatched ? 'border-green-300 bg-green-50' : 'border-gray-300' }} rounded-lg focus:ring-2 focus:ring-fucsia-magia mapping-select"
                                        data-preview-target="preview-{{ $field }}">
                                    <option value="">-- Seleziona --</option>
                                    @foreach($headers as $col => $header)
                                        <option value="{{ $col }}" {{ $autoMatched === $col ? 'selected' : '' }}>
                                            {{ $col }}: {{ Str::limit($header ?: '(vuoto)', 40) }}
                                        </option>
                                    @endforeach
                                </select>

                                <div class="mt-2 px-3 py-2 bg-gray-50 border border-gray-200 rounded text-xs font-mono overflow-x-auto" id="preview-{{ $field }}">
                                    <span class="text-gray-400">Nessuna selezione</span>
                                </div>
                            </div>
                        @endforeach

                        <!-- Data Info -->
                        <div class="mt-4 px-4 py-3 bg-blue-50 border-l-4 border-blue-500 rounded">
                            <div class="flex items-center text-sm">
                                <i class="fas fa-calendar-check text-blue-600 mr-2"></i>
                                <p class="text-blue-900">
                                    <strong>Data:</strong> Impostata automaticamente a <strong>{{ date('d/m/Y') }}</strong>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Codice Fiscale (Raccomandato) -->
                <div class="bg-white rounded-lg shadow mt-6">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-white">
                        <h3 class="font-bold text-gray-800 flex items-center">
                            <i class="fas fa-id-card text-blue-500 mr-2"></i>
                            Campo Raccomandato
                        </h3>
                    </div>

                    <div class="p-6">
                        @php
                            $cfAutoMatched = null;
                            foreach ($headers as $col => $header) {
                                if (preg_match('/(fiscale|cf|tax|codice)/i', $header)) {
                                    $cfAutoMatched = $col;
                                    break;
                                }
                            }
                        @endphp

                        <div class="campo-mapping" data-auto-matched="{{ $cfAutoMatched ? 'true' : 'false' }}">
                            <div class="flex items-center justify-between mb-2">
                                <label class="text-sm font-medium text-gray-700 flex items-center">
                                    <i class="fas fa-id-card text-gray-400 mr-2"></i>
                                    Codice Fiscale
                                </label>
                                @if($cfAutoMatched)
                                    <span class="px-2 py-0.5 bg-green-100 text-green-700 text-xs rounded-full font-semibold">
                                        <i class="fas fa-check-circle mr-1"></i>Auto
                                    </span>
                                @endif
                            </div>

                            <select name="mapping[codice_fiscale]"
                                    class="w-full px-3 py-2 text-sm border {{ $cfAutoMatched ? 'border-green-300 bg-green-50' : 'border-gray-300' }} rounded-lg focus:ring-2 focus:ring-fucsia-magia mapping-select"
                                    data-preview-target="preview-codice_fiscale">
                                <option value="skip">-- Salta (opzionale) --</option>
                                @foreach($headers as $col => $header)
                                    <option value="{{ $col }}" {{ $cfAutoMatched === $col ? 'selected' : '' }}>
                                        {{ $col }}: {{ Str::limit($header ?: '(vuoto)', 40) }}
                                    </option>
                                @endforeach
                            </select>

                            <div class="mt-2 px-3 py-2 bg-gray-50 border border-gray-200 rounded text-xs font-mono overflow-x-auto" id="preview-codice_fiscale">
                                <span class="text-gray-400">Campo opzionale</span>
                            </div>
                        </div>

                        <div class="mt-3 px-3 py-2 bg-amber-50 border border-amber-200 rounded">
                            <p class="text-xs text-amber-900">
                                <i class="fas fa-info-circle mr-1"></i>
                                Il Codice Fiscale aiuta a identificare univocamente i clienti ed evitare duplicati.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Colonna Destra: Campi Opzionali (Compatti) -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow sticky top-6">
                    <div class="px-4 py-3 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white">
                        <h3 class="text-sm font-bold text-gray-800 flex items-center">
                            <i class="fas fa-sliders-h text-gray-500 mr-2 text-xs"></i>
                            Misurazioni Opzionali
                        </h3>
                    </div>

                    <div class="p-4 space-y-3 max-h-[600px] overflow-y-auto">
                        @php
                            $campiOpzionali = [
                                'bmi' => ['label' => 'BMI', 'terms' => ['bmi', 'body mass index', 'indice']],
                                'grasso_corporeo' => ['label' => 'Grasso %', 'terms' => ['grasso', 'fat', 'bf']],
                                'grasso_viscerale' => ['label' => 'Grasso Visc.', 'terms' => ['viscerale', 'visceral']],
                                'massa_muscolare' => ['label' => 'Massa Musc.', 'terms' => ['massa muscolare', 'muscle mass']],
                                'muscolo_scheletrico' => ['label' => 'M. Scheletrico', 'terms' => ['muscolo', 'skeletal', 'smm']],
                                'bmr' => ['label' => 'BMR', 'terms' => ['bmr', 'metabolismo', 'basal']],
                                'acqua_corporea' => ['label' => 'Acqua %', 'terms' => ['acqua', 'water', 'tbw']],
                                'peso_corporeo_senza_grassi' => ['label' => 'FFM', 'terms' => ['senza grassi', 'ffm', 'lean']],
                                'proteine' => ['label' => 'Proteine %', 'terms' => ['proteine', 'protein']],
                                'eta_metabolica' => ['label' => 'Età Metab.', 'terms' => ['età', 'metabolica', 'age']],
                            ];
                        @endphp

                        @foreach($campiOpzionali as $field => $config)
                            @php
                                $autoMatched = null;
                                foreach ($headers as $col => $header) {
                                    foreach ($config['terms'] as $term) {
                                        if (stripos(strtolower($header), strtolower($term)) !== false) {
                                            $autoMatched = $col;
                                            break 2;
                                        }
                                    }
                                }
                            @endphp

                            <div class="campo-mapping-compact" data-auto-matched="{{ $autoMatched ? 'true' : 'false' }}">
                                <label class="block text-xs font-medium text-gray-700 mb-1">
                                    {{ $config['label'] }}
                                    @if($autoMatched)
                                        <i class="fas fa-check text-green-600 ml-1" title="Auto-rilevato"></i>
                                    @endif
                                </label>

                                <select name="mapping[{{ $field }}]"
                                        class="w-full px-2 py-1.5 text-xs border {{ $autoMatched ? 'border-green-300 bg-green-50' : 'border-gray-300' }} rounded focus:ring-1 focus:ring-fucsia-magia">
                                    <option value="skip">-- Salta --</option>
                                    @foreach($headers as $col => $header)
                                        <option value="{{ $col }}" {{ $autoMatched === $col ? 'selected' : '' }}>
                                            {{ $col }}: {{ Str::limit($header, 25) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endforeach
                    </div>

                    <!-- Footer Info Compact -->
                    <div class="px-4 py-3 bg-gray-50 border-t border-gray-200 rounded-b-lg">
                        <p class="text-xs text-gray-600">
                            <i class="fas fa-info-circle mr-1"></i>
                            I campi non selezionati verranno ignorati durante l'importazione.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center justify-between bg-white rounded-lg shadow px-6 py-4">
            <a href="{{ route('admin.pesate.import') }}" class="px-5 py-2.5 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>
                Indietro
            </a>

            <div class="flex items-center space-x-4">
                <div class="text-sm text-gray-600">
                    <i class="fas fa-database mr-2"></i>
                    Righe da importare: <strong>{{ session('import_highest_row') - session('import_start_row') + 1 }}</strong>
                </div>

                <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-fucsia-magia to-viola-magia text-white font-semibold rounded-lg hover:shadow-lg transition-all">
                    <i class="fas fa-arrow-right mr-2"></i>
                    Continua con Anteprima
                </button>
            </div>
        </div>
    </form>

</div>

<!-- JavaScript per Preview Dati -->
<script>
    const sampleData = @json($sampleRows);

    document.addEventListener('DOMContentLoaded', function() {
        // Gestione preview per ogni select
        document.querySelectorAll('.mapping-select').forEach(select => {
            select.addEventListener('change', function() {
                const previewId = this.getAttribute('data-preview-target');
                const previewDiv = document.getElementById(previewId);
                const col = this.value;

                if (!col || col === 'skip') {
                    previewDiv.innerHTML = '<span class="text-gray-400">Nessuna selezione</span>';
                    return;
                }

                // Mostra primi 5 valori della colonna selezionata
                const values = sampleData[col] || [];
                if (values.length > 0) {
                    const preview = values.slice(0, 5).map(v => `<span class="text-gray-700">${v || '<i class="text-gray-400">vuoto</i>'}</span>`).join(' • ');
                    previewDiv.innerHTML = preview;
                } else {
                    previewDiv.innerHTML = '<span class="text-gray-400">Nessun dato disponibile</span>';
                }
            });

            // Trigger change iniziale per campi auto-matched
            if (select.value && select.value !== 'skip') {
                select.dispatchEvent(new Event('change'));
            }
        });

        // Conta campi auto-rilevati
        const autoCount = document.querySelectorAll('[data-auto-matched="true"]').length;
        document.getElementById('auto-count').textContent = autoCount;

        // Aggiorna badge
        const badge = document.getElementById('auto-detection-badge');
        if (autoCount >= 4) {
            badge.classList.remove('bg-gray-100');
            badge.classList.add('bg-green-100');
            badge.querySelector('span').classList.add('text-green-700');
            badge.querySelector('i').classList.remove('text-gray-600');
            badge.querySelector('i').classList.add('text-green-600');
        }
    });
</script>

@endsection
