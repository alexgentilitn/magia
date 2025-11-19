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
                <p class="font-bold text-blue-900 mb-2">Come funziona:</p>
                <ol class="list-decimal list-inside text-blue-800 text-sm space-y-1">
                    <li>Il sistema ha <strong>rilevato automaticamente</strong> le colonne in base ai nomi degli header</li>
                    <li>I campi con <span class="inline-flex items-center px-2 py-0.5 bg-green-100 text-green-700 text-xs rounded"><i class="fas fa-check-circle mr-1"></i> Auto</span> sono stati abbinati automaticamente</li>
                    <li><strong>Verifica</strong> che l'abbinamento sia corretto guardando l'anteprima sotto ogni campo</li>
                    <li>Puoi <strong>modificare</strong> qualsiasi abbinamento selezionando una colonna diversa</li>
                    <li>I campi opzionali possono essere saltati selezionando "-- Salta --"</li>
                </ol>
            </div>
        </div>
    </div>

    <!-- Form Mapping -->
    <form action="{{ route('admin.pesate.process-mapping') }}" method="POST">
        @csrf

        <div class="bg-white rounded-lg shadow overflow-hidden mb-6">
            <div class="p-6 bg-gradient-to-r from-viola-magia to-fucsia-magia">
                <div class="flex items-center justify-between">
                    <h3 class="text-xl font-bold text-white flex items-center">
                        <i class="fas fa-columns mr-3"></i>
                        Mapping Colonne
                    </h3>
                    <span class="px-4 py-2 bg-white bg-opacity-20 rounded-lg text-white text-sm">
                        <i class="fas fa-magic mr-2"></i>
                        <span id="auto-count">0</span> campi auto-rilevati
                    </span>
                </div>
            </div>

            <div class="p-6 space-y-6">
                <!-- Campi Obbligatori -->
                <div class="border-b border-gray-200 pb-6">
                    <h4 class="font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-asterisk text-red-500 text-xs mr-2"></i>
                        Campi Obbligatori
                    </h4>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @php
                            $campiObbligatori = [
                                'cognome' => [
                                    'label' => 'Cognome',
                                    'terms' => ['cognome', 'surname', 'last name', 'lastname']
                                ],
                                'nome' => [
                                    'label' => 'Nome',
                                    'terms' => ['nome', 'name', 'first name', 'firstname']
                                ],
                                'peso' => [
                                    'label' => 'Peso (kg)',
                                    'terms' => ['peso', 'weight', 'kg']
                                ],
                            ];
                        @endphp

                        <!-- Info Data Automatica -->
                        <div class="col-span-2 mb-4 p-4 bg-green-50 border border-green-200 rounded-lg">
                            <div class="flex items-center">
                                <i class="fas fa-calendar-check text-green-600 text-xl mr-3"></i>
                                <div>
                                    <p class="font-medium text-green-900">Data Rilevazione Automatica</p>
                                    <p class="text-sm text-green-700">La data di tutte le pesate verrà impostata automaticamente a <strong>{{ date('d/m/Y') }}</strong> (oggi)</p>
                                </div>
                            </div>
                        </div>

                        @foreach($campiObbligatori as $field => $config)
                            @php
                                // Auto-rilevazione campo obbligatorio
                                $autoMatched = null;
                                foreach ($headers as $col => $header) {
                                    $headerLower = strtolower($header);
                                    foreach ($config['terms'] as $term) {
                                        // Per nome, escludiamo "cognome" per evitare conflitti
                                        if ($field === 'nome' && stripos($headerLower, 'cognome') !== false) {
                                            continue;
                                        }
                                        if (stripos($headerLower, strtolower($term)) !== false) {
                                            $autoMatched = $col;
                                            break 2;
                                        }
                                    }
                                }
                            @endphp
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ $config['label'] }} <span class="text-red-500">*</span>
                                    @if($autoMatched)
                                        <span class="ml-1 text-xs text-green-600">
                                            <i class="fas fa-check-circle"></i> Auto
                                        </span>
                                    @endif
                                </label>
                                <select name="mapping[{{ $field }}]" required
                                        class="w-full px-4 py-2 border {{ $autoMatched ? 'border-green-300 bg-green-50' : 'border-gray-300' }} rounded-lg focus:ring-2 focus:ring-fucsia-magia">
                                    <option value="">-- Seleziona colonna --</option>
                                    @foreach($headers as $col => $header)
                                        <option value="{{ $col }}" {{ $autoMatched === $col ? 'selected' : '' }}>
                                            Colonna {{ $col }}: {{ $header ?: '(vuoto)' }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="mt-2 p-2 bg-gray-50 rounded text-xs font-mono overflow-x-auto" id="preview-{{ $field }}">
                                    Seleziona una colonna per vedere l'anteprima
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Codice Fiscale (Opzionale ma Raccomandato) -->
                <div class="border-b border-gray-200 pb-6">
                    <h4 class="font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-id-card text-blue-500 mr-2"></i>
                        Campo Raccomandato
                    </h4>

                    @php
                        // Auto-rilevazione codice fiscale
                        $cfAutoMatched = null;
                        $cfTerms = ['fiscale', 'cf', 'tax', 'codice', 'fiscal'];
                        foreach ($headers as $col => $header) {
                            $headerLower = strtolower($header);
                            foreach ($cfTerms as $term) {
                                if (stripos($headerLower, $term) !== false) {
                                    $cfAutoMatched = $col;
                                    break 2;
                                }
                            }
                        }
                    @endphp
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Codice Fiscale
                            @if($cfAutoMatched)
                                <span class="ml-1 text-xs text-green-600">
                                    <i class="fas fa-check-circle"></i> Auto
                                </span>
                            @endif
                            <br>
                            <span class="text-xs text-gray-500">(usato per trovare cliente se nome/cognome non corrispondono)</span>
                        </label>
                        <select name="mapping[codice_fiscale]"
                                class="w-full px-4 py-2 border {{ $cfAutoMatched ? 'border-green-300 bg-green-50' : 'border-gray-300' }} rounded-lg focus:ring-2 focus:ring-fucsia-magia">
                            <option value="skip">-- Salta campo --</option>
                            @foreach($headers as $col => $header)
                                <option value="{{ $col }}" {{ $cfAutoMatched === $col ? 'selected' : '' }}>
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

                    <div id="optional-fields" class="grid grid-cols-1 md:grid-cols-2 gap-6" style="display: none;">
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
                            @php
                                // Auto-rilevazione campo con matching intelligente
                                $autoMatched = null;

                                // Termini di ricerca per ogni campo (ordine di priorità)
                                $searchTerms = match($field) {
                                    'bmi' => ['bmi', 'body mass', 'indice massa', 'b.m.i'],
                                    'peso_corporeo_senza_grassi' => [
                                        'peso corporeo senza grassi',  // Match esatto
                                        'peso senza grassi',           // Abbreviato
                                        'ffm',                         // Acronimo
                                        'fat free mass',               // Inglese
                                        'fat free',                    // Inglese abbreviato
                                        'massa magra',                 // Sinonimo italiano
                                        'lean body mass',              // Inglese alternativo
                                        'lean mass',                   // Breve
                                    ],
                                    'muscolo_scheletrico' => [
                                        'muscolo scheletrico',
                                        'skeletal muscle',
                                        'musc schel',
                                        'musc skel',
                                        'smm',
                                    ],
                                    'grasso_corporeo' => [
                                        'grasso corporeo',
                                        'body fat',
                                        'bf%',
                                        'massa grassa',
                                        'fat%',
                                        'bodyfat',
                                    ],
                                    'grasso_sottocutaneo' => [
                                        'grasso sottocutaneo',
                                        'subcutaneous fat',
                                        'subcutaneous',
                                        'sottocut',
                                        'subcut',
                                    ],
                                    'grasso_viscerale' => [
                                        'grasso viscerale',
                                        'visceral fat',
                                        'visceral',
                                        'visc',
                                    ],
                                    'acqua_corporea' => [
                                        'acqua corporea',
                                        'body water',
                                        'total body water',
                                        'acqua',
                                        'water',
                                        'tbw',
                                        'idratazione',
                                    ],
                                    'massa_muscolare' => [
                                        'massa muscolare',
                                        'muscle mass',
                                        'massa musc',
                                        'musc mass',
                                    ],
                                    'massa_ossea' => [
                                        'massa ossea',
                                        'bone mass',
                                        'bone',
                                        'ossa',
                                    ],
                                    'proteine' => [
                                        'proteine',
                                        'protein',
                                        'prot',
                                    ],
                                    'bmr' => [
                                        'bmr',
                                        'metabolismo basale',
                                        'basal metabolic',
                                        'basal',
                                        'kcal',
                                    ],
                                    'eta_metabolica' => [
                                        'età metabolica',
                                        'eta metabolica',
                                        'metabolic age',
                                        'eta metab',
                                        'age metab',
                                    ],
                                    default => []
                                };

                                // Prova matching esatto o parziale (priorità ai match più completi)
                                $bestMatch = null;
                                $bestScore = 0;

                                foreach ($headers as $col => $header) {
                                    $headerLower = strtolower(trim($header));

                                    foreach ($searchTerms as $priority => $term) {
                                        $termLower = strtolower($term);

                                        // Match esatto (massimo punteggio)
                                        if ($headerLower === $termLower) {
                                            $score = 1000 - $priority; // Più alta priorità = più punti
                                            if ($score > $bestScore) {
                                                $bestScore = $score;
                                                $bestMatch = $col;
                                            }
                                        }
                                        // Match contenuto (punteggio medio)
                                        elseif (stripos($headerLower, $termLower) !== false) {
                                            $score = 500 - $priority;
                                            if ($score > $bestScore) {
                                                $bestScore = $score;
                                                $bestMatch = $col;
                                            }
                                        }
                                        // Match parole separate (punteggio basso ma comunque utile)
                                        else {
                                            $termWords = explode(' ', $termLower);
                                            $matchedWords = 0;
                                            foreach ($termWords as $word) {
                                                if (strlen($word) > 2 && stripos($headerLower, $word) !== false) {
                                                    $matchedWords++;
                                                }
                                            }
                                            if ($matchedWords > 0 && $matchedWords === count($termWords)) {
                                                $score = 100 + ($matchedWords * 10) - $priority;
                                                if ($score > $bestScore) {
                                                    $bestScore = $score;
                                                    $bestMatch = $col;
                                                }
                                            }
                                        }
                                    }
                                }

                                $autoMatched = $bestMatch;
                            @endphp
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    {{ $label }}
                                    @if($autoMatched)
                                        <span class="ml-1 text-xs text-green-600">
                                            <i class="fas fa-check-circle"></i> Auto
                                        </span>
                                    @endif
                                </label>
                                <select name="mapping[{{ $field }}]"
                                        class="w-full px-3 py-2 text-sm border {{ $autoMatched ? 'border-green-300 bg-green-50' : 'border-gray-300' }} rounded-lg focus:ring-2 focus:ring-fucsia-magia">
                                    <option value="skip">-- Salta --</option>
                                    @foreach($headers as $col => $header)
                                        <option value="{{ $col }}" {{ $autoMatched === $col ? 'selected' : '' }}>
                                            {{ $col }}: {{ $header }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="mt-2 p-2 bg-gray-50 rounded text-xs font-mono overflow-x-auto" id="preview-{{ $field }}">
                                    Seleziona una colonna per vedere l'anteprima
                                </div>
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
            const displayCount = 5; // Mostra 5 valori invece di 3
            previewDiv.innerHTML = '<strong class="text-gray-600">Anteprima:</strong> ' +
                                  samples.slice(0, displayCount).map(v => `<span class="text-gray-700 font-semibold">${v}</span>`).join(', ') +
                                  (samples.length > displayCount ? ` <span class="text-gray-500">(+${samples.length - displayCount} righe)</span>` : '');
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

    // Conta campi auto-rilevati
    function countAutoMatched() {
        const selects = document.querySelectorAll('select[name^="mapping"]');
        let count = 0;

        selects.forEach(select => {
            // Controlla se il campo ha un valore selezionato (non vuoto e non "skip")
            const selectedValue = select.value;
            const hasGreenBorder = select.classList.contains('border-green-300');

            if (hasGreenBorder && selectedValue && selectedValue !== 'skip' && selectedValue !== '') {
                count++;
            }
        });

        // Aggiorna il contatore
        const counterElement = document.getElementById('auto-count');
        if (counterElement) {
            counterElement.textContent = count;

            // Cambia colore se tutti i campi obbligatori sono rilevati (minimo 4)
            const badge = counterElement.parentElement;
            if (count >= 4) {
                badge.classList.add('bg-green-500', 'bg-opacity-100');
                badge.classList.remove('bg-white', 'bg-opacity-20');
            }
        }

        return count;
    }

    // Esegui al caricamento
    document.addEventListener('DOMContentLoaded', function() {
        // Conta e mostra numero di campi auto-rilevati
        const autoCount = countAutoMatched();

        // Mostra messaggio se tutti i campi obbligatori sono stati trovati
        if (autoCount >= 4) {
            console.log(`✓ Auto-rilevati ${autoCount} campi - tutti i campi obbligatori trovati!`);
        }
    });
</script>

@endsection
