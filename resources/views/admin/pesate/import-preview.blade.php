@extends('layouts.admin')

@section('titolo', 'Anteprima Importazione Pesate')

@section('contenuto')
<div class="p-6 max-w-[1800px] mx-auto">

    @php
        $totale = count($preview_data);
        $valide = count(array_filter($preview_data, fn($r) => empty($r['errors'])));
        $errori = $totale - $valide;
        $dataOggi = now()->format('d/m/Y');
    @endphp

    <!-- Header Compatto con Statistiche Inline -->
    <div class="bg-white rounded-lg shadow mb-6 p-4">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-800 mb-1">Anteprima Importazione Pesate</h2>
                <p class="text-xs text-gray-500">Verifica i dati e conferma l'importazione</p>
            </div>

            <!-- Statistiche Inline -->
            <div class="flex items-center gap-3">
                <div class="px-3 py-1.5 bg-blue-50 border border-blue-200 rounded-lg">
                    <p class="text-[10px] text-blue-600 font-medium uppercase tracking-wide">Righe</p>
                    <p class="text-lg font-bold text-blue-900">{{ $totale }}</p>
                </div>
                <div class="px-3 py-1.5 bg-green-50 border border-green-200 rounded-lg">
                    <p class="text-[10px] text-green-600 font-medium uppercase tracking-wide">Valide</p>
                    <p class="text-lg font-bold text-green-900">{{ $valide }}</p>
                </div>
                @if($errori > 0)
                <div class="px-3 py-1.5 bg-red-50 border border-red-200 rounded-lg">
                    <p class="text-[10px] text-red-600 font-medium uppercase tracking-wide">Errori</p>
                    <p class="text-lg font-bold text-red-900">{{ $errori }}</p>
                </div>
                @endif
                <div class="px-3 py-1.5 bg-purple-50 border border-purple-200 rounded-lg">
                    <p class="text-[10px] text-purple-600 font-medium uppercase tracking-wide">Sede</p>
                    <p class="text-sm font-bold text-purple-900">{{ $sede }}</p>
                </div>
                <div class="px-3 py-1.5 bg-orange-50 border border-orange-200 rounded-lg">
                    <p class="text-[10px] text-orange-600 font-medium uppercase tracking-wide">Data</p>
                    <p class="text-xs font-bold text-orange-900">{{ $dataOggi }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Riepilogo Clienti Compatto -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
        <!-- Clienti Esistenti -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-3 py-2 bg-blue-50 border-b border-blue-200 flex items-center justify-between">
                <h3 class="text-sm font-bold text-blue-900 flex items-center">
                    <i class="fas fa-users text-blue-600 mr-1.5 text-xs"></i>
                    Clienti Esistenti
                </h3>
                <span class="px-2 py-0.5 bg-blue-200 text-blue-800 text-xs rounded-full font-bold">
                    {{ count($clientiTrovati) }}
                </span>
            </div>
            <div class="p-3">
                @if(count($clientiTrovati) > 0)
                    <div class="space-y-1.5 max-h-52 overflow-y-auto">
                        @foreach($clientiTrovati as $cliente)
                            <div class="flex items-center justify-between p-2 bg-blue-50 rounded border border-blue-200 hover:bg-blue-100 transition">
                                <div class="flex items-center min-w-0 flex-1">
                                    <div class="h-7 w-7 bg-blue-500 rounded-full flex items-center justify-center text-white text-xs font-bold mr-2 flex-shrink-0">
                                        {{ strtoupper(substr($cliente['nome'], 0, 1) . substr($cliente['cognome'], 0, 1)) }}
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-xs font-semibold text-gray-900 truncate">{{ $cliente['cognome'] }} {{ $cliente['nome'] }}</p>
                                        @if($cliente['codice_fiscale'])
                                            <p class="text-[10px] text-gray-500">CF: {{ Str::limit($cliente['codice_fiscale'], 16) }}</p>
                                        @endif
                                    </div>
                                </div>
                                <span class="px-2 py-0.5 bg-blue-200 text-blue-800 text-[10px] font-semibold rounded-full ml-2 flex-shrink-0">
                                    {{ $cliente['pesate_count'] }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-6 text-gray-400">
                        <i class="fas fa-user-slash text-2xl mb-2"></i>
                        <p class="text-xs">Nessuno</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Clienti Nuovi -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-3 py-2 bg-green-50 border-b border-green-200 flex items-center justify-between">
                <h3 class="text-sm font-bold text-green-900 flex items-center">
                    <i class="fas fa-user-plus text-green-600 mr-1.5 text-xs"></i>
                    Nuovi Clienti
                </h3>
                <span class="px-2 py-0.5 bg-green-200 text-green-800 text-xs rounded-full font-bold">
                    {{ count($clientiCreati) }}
                </span>
            </div>
            <div class="p-3">
                @if(count($clientiCreati) > 0)
                    <div class="space-y-1.5 max-h-52 overflow-y-auto">
                        @foreach($clientiCreati as $cliente)
                            <div class="flex items-center justify-between p-2 bg-green-50 rounded border border-green-200 hover:bg-green-100 transition">
                                <div class="flex items-center min-w-0 flex-1">
                                    <div class="h-7 w-7 bg-green-500 rounded-full flex items-center justify-center text-white text-xs font-bold mr-2 flex-shrink-0">
                                        {{ strtoupper(substr($cliente['nome'], 0, 1) . substr($cliente['cognome'], 0, 1)) }}
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-xs font-semibold text-gray-900 truncate">{{ $cliente['cognome'] }} {{ $cliente['nome'] }}</p>
                                        @if($cliente['codice_fiscale'])
                                            <p class="text-[10px] text-gray-500">CF: {{ Str::limit($cliente['codice_fiscale'], 16) }}</p>
                                        @else
                                            <p class="text-[10px] text-gray-400 italic">Senza CF</p>
                                        @endif
                                    </div>
                                </div>
                                <span class="px-2 py-0.5 bg-green-200 text-green-800 text-[10px] font-semibold rounded-full ml-2 flex-shrink-0">
                                    <i class="fas fa-star mr-0.5"></i>Nuovo
                                </span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-6 text-gray-400">
                        <i class="fas fa-check-circle text-2xl mb-2"></i>
                        <p class="text-xs">Tutti esistenti</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Alert Compatto -->
    @if($valide > 0)
        <div class="bg-green-50 border-l-4 border-green-500 px-4 py-2.5 mb-4 rounded-lg">
            <div class="flex items-center">
                <i class="fas fa-check-circle text-green-600 text-sm mr-2"></i>
                <p class="text-sm text-green-800">
                    <strong>{{ $valide }}</strong> pesate pronte per importazione.
                    @if($errori > 0)
                        <span class="text-red-700">{{ $errori }} righe con errori verranno saltate.</span>
                    @endif
                </p>
            </div>
        </div>
    @else
        <div class="bg-red-50 border-l-4 border-red-500 px-4 py-2.5 mb-4 rounded-lg">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle text-red-600 text-sm mr-2"></i>
                <p class="text-sm text-red-800 font-medium">
                    Impossibile procedere: tutte le righe contengono errori.
                </p>
            </div>
        </div>
    @endif

    <!-- Form Conferma -->
    @if($valide > 0)
        <form action="{{ route('admin.pesate.confirm-import') }}" method="POST" id="importForm">
            @csrf
            <input type="hidden" name="sede" value="{{ $sede }}">
    @endif

    <!-- Tabella Anteprima con Righe Espandibili -->
    <div class="bg-white rounded-lg shadow overflow-hidden mb-4" x-data="{ expandedRows: {} }">
        <div class="px-4 py-3 bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-bold text-gray-900 flex items-center">
                    <i class="fas fa-table text-fucsia-magia mr-2"></i>
                    Anteprima Dati
                </h3>
                <div class="flex items-center gap-3">
                    <button type="button" @click="Object.keys(expandedRows).length ? expandedRows = {} : expandedRows = Object.fromEntries([...Array({{ count($preview_data) }})].map((_, i) => [i, true]))"
                            class="text-xs text-gray-600 hover:text-fucsia-magia">
                        <i class="fas fa-expand-arrows-alt mr-1"></i>
                        Espandi/Comprimi Tutti
                    </button>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wide w-12">#</th>
                        <th class="px-3 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wide">Cliente</th>
                        <th class="px-3 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wide w-24">Peso (kg)</th>
                        <th class="px-3 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wide w-20">BMI</th>
                        <th class="px-3 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wide w-28">Data</th>
                        <th class="px-3 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wide w-20">Stato</th>
                        <th class="px-3 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wide w-16"></th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($preview_data as $index => $row)
                        @php
                            // Calcola warnings per campi vuoti o valori anomali
                            $warnings = [];

                            // Helper per verificare se un campo è vuoto (null o stringa vuota, ma NON 0)
                            $isFieldEmpty = function($value) {
                                return $value === null || $value === '' || $value === 'null';
                            };

                            // Campi vuoti - TUTTI i 12 campi misurazione
                            if ($isFieldEmpty($row['bmi'] ?? null)) $warnings[] = 'BMI mancante';
                            if ($isFieldEmpty($row['grasso_corporeo'] ?? null)) $warnings[] = 'Grasso corporeo mancante';
                            if ($isFieldEmpty($row['grasso_sottocutaneo'] ?? null)) $warnings[] = 'Grasso sottocutaneo mancante';
                            if ($isFieldEmpty($row['grasso_viscerale'] ?? null)) $warnings[] = 'Grasso viscerale mancante';
                            if ($isFieldEmpty($row['massa_muscolare'] ?? null)) $warnings[] = 'Massa muscolare mancante';
                            if ($isFieldEmpty($row['muscolo_scheletrico'] ?? null)) $warnings[] = 'Muscolo scheletrico mancante';
                            if ($isFieldEmpty($row['bmr'] ?? null)) $warnings[] = 'BMR mancante';
                            if ($isFieldEmpty($row['acqua_corporea'] ?? null)) $warnings[] = 'Acqua corporea mancante';
                            if ($isFieldEmpty($row['peso_corporeo_senza_grassi'] ?? null)) $warnings[] = 'FFM mancante';
                            if ($isFieldEmpty($row['proteine'] ?? null)) $warnings[] = 'Proteine mancante';
                            if ($isFieldEmpty($row['eta_metabolica'] ?? null)) $warnings[] = 'Età metabolica mancante';
                            if ($isFieldEmpty($row['massa_ossea'] ?? null)) $warnings[] = 'Massa ossea mancante';

                            // Valori anomali (solo se il campo ha un valore)
                            $peso = $row['peso'] ?? null;
                            $bmi = $row['bmi'] ?? null;
                            $grasso = $row['grasso_corporeo'] ?? null;
                            $viscerale = $row['grasso_viscerale'] ?? null;
                            $acqua = $row['acqua_corporea'] ?? null;
                            $bmr = $row['bmr'] ?? null;
                            $eta = $row['eta_metabolica'] ?? null;

                            if ($peso !== null && $peso !== '' && ($peso < 30 || $peso > 200)) {
                                $warnings[] = 'Peso anomalo: ' . $peso . ' kg (normale: 30-200)';
                            }
                            if ($bmi !== null && $bmi !== '' && ($bmi < 15 || $bmi > 50)) {
                                $warnings[] = 'BMI anomalo: ' . $bmi . ' (normale: 15-50)';
                            }
                            if ($grasso !== null && $grasso !== '' && ($grasso < 5 || $grasso > 60)) {
                                $warnings[] = 'Grasso corporeo anomalo: ' . $grasso . '% (normale: 5-60)';
                            }
                            if ($viscerale !== null && $viscerale !== '' && ($viscerale < 1 || $viscerale > 30)) {
                                $warnings[] = 'Grasso viscerale anomalo: ' . $viscerale . ' (normale: 1-30)';
                            }
                            if ($acqua !== null && $acqua !== '' && ($acqua < 30 || $acqua > 80)) {
                                $warnings[] = 'Acqua corporea anomala: ' . $acqua . '% (normale: 30-80)';
                            }
                            if ($bmr !== null && $bmr !== '' && ($bmr < 800 || $bmr > 3500)) {
                                $warnings[] = 'BMR anomalo: ' . $bmr . ' kcal (normale: 800-3500)';
                            }
                            if ($eta !== null && $eta !== '' && ($eta < 10 || $eta > 100)) {
                                $warnings[] = 'Età metabolica anomala: ' . $eta . ' (normale: 10-100)';
                            }

                            $hasWarnings = count($warnings) > 0;

                            // Determina colore riga: errore > warning > omonimia > normale
                            if (!empty($row['errors'])) {
                                $rowClass = 'bg-red-50';
                            } elseif ($hasWarnings) {
                                $rowClass = 'bg-orange-50';
                            } elseif (!empty($row['has_omonimia'])) {
                                $rowClass = 'bg-yellow-50';
                            } else {
                                $rowClass = 'hover:bg-gray-50';
                            }
                        @endphp

                        <!-- Riga Principale -->
                        <tr class="{{ $rowClass }} cursor-pointer"
                            @click="expandedRows[{{ $index }}] = !expandedRows[{{ $index }}]">

                            <!-- Hidden fields -->
                            <td style="display:none;">
                                <input type="hidden" name="row_number_{{ $index }}" value="{{ $row['row'] }}">
                                <input type="hidden" name="nome_{{ $index }}" value="{{ $row['nome'] }}">
                                <input type="hidden" name="cognome_{{ $index }}" value="{{ $row['cognome'] }}">
                                <input type="hidden" name="codice_fiscale_{{ $index }}" value="{{ $row['codice_fiscale'] ?? '' }}">
                                <input type="hidden" name="has_errors_{{ $index }}" value="{{ !empty($row['errors']) ? '1' : '0' }}">
                                <input type="hidden" name="has_omonimia_{{ $index }}" value="{{ !empty($row['has_omonimia']) ? '1' : '0' }}">
                                @if(empty($row['has_omonimia']))
                                    <input type="hidden" name="cliente_id_{{ $index }}" value="{{ !empty($row['cliente_creato']) ? 'new' : ($row['cliente_id'] ?? '') }}">
                                @endif
                            </td>

                            <!-- # Riga -->
                            <td class="px-3 py-3 text-sm font-bold text-gray-900">{{ $row['row'] }}</td>

                            <!-- Cliente -->
                            <td class="px-3 py-3">
                                <div class="flex items-center">
                                    <div class="flex-1">
                                        <div class="text-sm font-semibold text-gray-900">{{ $row['cognome'] }} {{ $row['nome'] }}</div>
                                        @if(!empty($row['codice_fiscale']))
                                            <div class="text-xs text-gray-500">CF: {{ $row['codice_fiscale'] }}</div>
                                        @endif
                                    </div>
                                    <div class="flex gap-1 ml-2">
                                        @if(!empty($row['cliente_creato']))
                                            <span class="px-2 py-0.5 bg-green-100 text-green-700 text-xs rounded-full font-medium">Nuovo</span>
                                        @endif
                                        @if(!empty($row['has_omonimia']))
                                            @if(!empty($row['possibili_clienti']) && count($row['possibili_clienti']) === 1)
                                                <span class="px-2 py-0.5 bg-blue-100 text-blue-700 text-xs rounded-full font-medium">Esistente</span>
                                            @else
                                                <span class="px-2 py-0.5 bg-yellow-100 text-yellow-700 text-xs rounded-full font-medium">Omonimia</span>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <!-- Peso -->
                            <td class="px-3 py-3 text-sm font-medium {{ !empty($row['peso']) && ($row['peso'] < 30 || $row['peso'] > 200) ? 'text-orange-600' : 'text-gray-900' }}">
                                {{ $row['peso'] ?? '-' }}
                            </td>

                            <!-- BMI -->
                            <td class="px-3 py-3 text-sm {{ !empty($row['bmi']) && ($row['bmi'] < 15 || $row['bmi'] > 50) ? 'text-orange-600' : 'text-gray-900' }}">
                                {{ $row['bmi'] ?? '-' }}
                            </td>

                            <!-- Data -->
                            <td class="px-3 py-3 text-sm text-gray-900">
                                {{ $row['data_rilevazione'] ? \Carbon\Carbon::parse($row['data_rilevazione'])->format('d/m/Y') : '-' }}
                            </td>

                            <!-- Stato -->
                            <td class="px-3 py-3 text-center">
                                @if(!empty($row['errors']))
                                    <span class="inline-flex px-2 py-1 bg-red-100 text-red-800 text-xs font-semibold rounded-full">
                                        <i class="fas fa-times mr-1"></i>Errore
                                    </span>
                                @elseif($hasWarnings)
                                    <span class="inline-flex px-2 py-1 bg-orange-100 text-orange-800 text-xs font-semibold rounded-full">
                                        <i class="fas fa-exclamation mr-1"></i>{{ count($warnings) }}
                                    </span>
                                @else
                                    <span class="inline-flex px-2 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">
                                        <i class="fas fa-check mr-1"></i>OK
                                    </span>
                                @endif
                            </td>

                            <!-- Toggle -->
                            <td class="px-3 py-3 text-center" @click.stop>
                                <button type="button" @click="expandedRows[{{ $index }}] = !expandedRows[{{ $index }}]"
                                        class="text-gray-400 hover:text-fucsia-magia transition-transform"
                                        :class="expandedRows[{{ $index }}] ? 'rotate-180' : ''">
                                    <i class="fas fa-chevron-down"></i>
                                </button>
                            </td>
                        </tr>

                        <!-- Riga Espandibile con Dettagli -->
                        <tr x-show="expandedRows[{{ $index }}]" x-collapse class="bg-gray-50">
                            <td colspan="7" class="px-4 py-4">
                                <div class="bg-white rounded-lg border border-gray-200 p-4">

                                    <!-- Errori in evidenza -->
                                    @if(!empty($row['errors']))
                                        <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg">
                                            <div class="text-sm font-semibold text-red-800 mb-1">
                                                <i class="fas fa-times-circle mr-1"></i>Errori:
                                            </div>
                                            @foreach($row['errors'] as $error)
                                                <div class="text-xs text-red-700">• {{ $error }}</div>
                                            @endforeach
                                        </div>
                                    @endif

                                    <!-- Warnings in evidenza -->
                                    @if($hasWarnings)
                                        <div class="mb-4 p-3 bg-orange-50 border border-orange-200 rounded-lg">
                                            <div class="text-sm font-semibold text-orange-800 mb-1">
                                                <i class="fas fa-exclamation-triangle mr-1"></i>Attenzione ({{ count($warnings) }}):
                                            </div>
                                            @foreach($warnings as $warning)
                                                <div class="text-xs text-orange-700">• {{ $warning }}</div>
                                            @endforeach
                                        </div>
                                    @endif

                                    <!-- Gestione Omonimia -->
                                    @if(!empty($row['has_omonimia']) && !empty($row['possibili_clienti']))
                                        @php
                                            $numClienti = count($row['possibili_clienti']);
                                            $isOmonimia = $numClienti > 1;
                                        @endphp
                                        <div class="mb-4 p-3 {{ $isOmonimia ? 'bg-yellow-50 border-yellow-200' : 'bg-blue-50 border-blue-200' }} border rounded-lg">
                                            <div class="text-sm font-semibold {{ $isOmonimia ? 'text-yellow-800' : 'text-blue-800' }} mb-2">
                                                @if($isOmonimia)
                                                    <i class="fas fa-users mr-1"></i>{{ $numClienti }} clienti con stesso nome - Scegli:
                                                @else
                                                    <i class="fas fa-user-check mr-1"></i>Cliente trovato - Conferma o crea nuovo:
                                                @endif
                                            </div>
                                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2">
                                                @foreach($row['possibili_clienti'] as $pIndex => $possibileCliente)
                                                    <label class="flex items-start p-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 cursor-pointer">
                                                        <input type="radio" name="cliente_id_{{ $index }}" value="{{ $possibileCliente['id'] }}"
                                                               {{ $pIndex === 0 ? 'checked' : '' }} class="mt-1 mr-2">
                                                        <div class="text-xs">
                                                            <div class="font-semibold text-gray-900">
                                                                {{ $possibileCliente['cognome'] }} {{ $possibileCliente['nome'] }}
                                                                @if(!empty($possibileCliente['data_iscrizione']))
                                                                    <span class="text-gray-500 font-normal ml-1">({{ \Carbon\Carbon::parse($possibileCliente['data_iscrizione'])->format('d/m/Y') }})</span>
                                                                @endif
                                                            </div>
                                                            @if(!empty($possibileCliente['codice_fiscale']))
                                                                <div class="text-gray-600">CF: {{ $possibileCliente['codice_fiscale'] }}</div>
                                                            @endif
                                                            @if(!empty($possibileCliente['email']))
                                                                <div class="text-gray-600">{{ $possibileCliente['email'] }}</div>
                                                            @endif
                                                        </div>
                                                    </label>
                                                @endforeach
                                                <!-- Opzione Crea Nuovo -->
                                                <label class="flex items-start p-2 bg-green-50 border border-green-300 rounded-lg hover:bg-green-100 cursor-pointer">
                                                    <input type="radio" name="cliente_id_{{ $index }}" value="new" class="mt-1 mr-2">
                                                    <div class="text-xs">
                                                        <div class="font-bold text-green-800"><i class="fas fa-user-plus mr-1"></i>Crea Nuovo Cliente</div>
                                                        <div class="text-green-700">Nuovo profilo con stesso nome</div>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                    @endif

                                    <!-- Griglia Campi Modificabili -->
                                    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-3">
                                        <!-- Peso -->
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 mb-1">Peso (kg) *</label>
                                            <input type="number" name="peso_{{ $index }}" value="{{ $row['peso'] ?? '' }}" step="0.01"
                                                   class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded focus:ring-1 focus:ring-fucsia-magia focus:border-fucsia-magia">
                                        </div>
                                        <!-- BMI -->
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 mb-1">BMI</label>
                                            <input type="number" name="bmi_{{ $index }}" value="{{ $row['bmi'] ?? '' }}" step="0.01"
                                                   class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded focus:ring-1 focus:ring-fucsia-magia focus:border-fucsia-magia">
                                        </div>
                                        <!-- Grasso Corporeo -->
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 mb-1">Grasso (%)</label>
                                            <input type="number" name="grasso_corporeo_{{ $index }}" value="{{ $row['grasso_corporeo'] ?? '' }}" step="0.01"
                                                   class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded focus:ring-1 focus:ring-fucsia-magia focus:border-fucsia-magia">
                                        </div>
                                        <!-- Grasso Viscerale -->
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 mb-1">G. Viscerale</label>
                                            <input type="number" name="grasso_viscerale_{{ $index }}" value="{{ $row['grasso_viscerale'] ?? '' }}" step="0.01"
                                                   class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded focus:ring-1 focus:ring-fucsia-magia focus:border-fucsia-magia">
                                        </div>
                                        <!-- Grasso Sottocutaneo -->
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 mb-1">G. Sottoc. (%)</label>
                                            <input type="number" name="grasso_sottocutaneo_{{ $index }}" value="{{ $row['grasso_sottocutaneo'] ?? '' }}" step="0.01"
                                                   class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded focus:ring-1 focus:ring-fucsia-magia focus:border-fucsia-magia">
                                        </div>
                                        <!-- Massa Muscolare -->
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 mb-1">M. Muscolare (kg)</label>
                                            <input type="number" name="massa_muscolare_{{ $index }}" value="{{ $row['massa_muscolare'] ?? '' }}" step="0.01"
                                                   class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded focus:ring-1 focus:ring-fucsia-magia focus:border-fucsia-magia">
                                        </div>
                                        <!-- Muscolo Scheletrico -->
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 mb-1">M. Scheletrico (%)</label>
                                            <input type="number" name="muscolo_scheletrico_{{ $index }}" value="{{ $row['muscolo_scheletrico'] ?? '' }}" step="0.01"
                                                   class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded focus:ring-1 focus:ring-fucsia-magia focus:border-fucsia-magia">
                                        </div>
                                        <!-- BMR -->
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 mb-1">BMR (kcal)</label>
                                            <input type="number" name="bmr_{{ $index }}" value="{{ $row['bmr'] ?? '' }}" step="1"
                                                   class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded focus:ring-1 focus:ring-fucsia-magia focus:border-fucsia-magia">
                                        </div>
                                        <!-- Acqua Corporea -->
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 mb-1">Acqua (%)</label>
                                            <input type="number" name="acqua_corporea_{{ $index }}" value="{{ $row['acqua_corporea'] ?? '' }}" step="0.01"
                                                   class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded focus:ring-1 focus:ring-fucsia-magia focus:border-fucsia-magia">
                                        </div>
                                        <!-- Peso senza Grassi -->
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 mb-1">FFM (kg)</label>
                                            <input type="number" name="peso_corporeo_senza_grassi_{{ $index }}" value="{{ $row['peso_corporeo_senza_grassi'] ?? '' }}" step="0.01"
                                                   class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded focus:ring-1 focus:ring-fucsia-magia focus:border-fucsia-magia">
                                        </div>
                                        <!-- Proteine -->
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 mb-1">Proteine (%)</label>
                                            <input type="number" name="proteine_{{ $index }}" value="{{ $row['proteine'] ?? '' }}" step="0.01"
                                                   class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded focus:ring-1 focus:ring-fucsia-magia focus:border-fucsia-magia">
                                        </div>
                                        <!-- Massa Ossea -->
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 mb-1">M. Ossea (kg)</label>
                                            <input type="number" name="massa_ossea_{{ $index }}" value="{{ $row['massa_ossea'] ?? '' }}" step="0.01"
                                                   class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded focus:ring-1 focus:ring-fucsia-magia focus:border-fucsia-magia">
                                        </div>
                                        <!-- Età Metabolica -->
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 mb-1">Età Met.</label>
                                            <input type="number" name="eta_metabolica_{{ $index }}" value="{{ $row['eta_metabolica'] ?? '' }}" step="1"
                                                   class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded focus:ring-1 focus:ring-fucsia-magia focus:border-fucsia-magia">
                                        </div>
                                        <!-- Data Rilevazione -->
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 mb-1">Data *</label>
                                            <input type="date" name="data_rilevazione_{{ $index }}" value="{{ $row['data_rilevazione'] ?? '' }}"
                                                   class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded focus:ring-1 focus:ring-fucsia-magia focus:border-fucsia-magia">
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pulsanti Azione -->
    @if($valide > 0)
            <input type="hidden" name="total_rows" value="{{ count($preview_data) }}">

            <div class="flex items-center justify-between bg-white rounded-lg shadow px-5 py-3">
                <a href="{{ route('admin.pesate.import') }}"
                   class="px-5 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition text-sm font-medium">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Indietro
                </a>
                <button type="submit"
                        class="px-8 py-2.5 bg-gradient-to-r from-green-500 to-green-600 text-white font-semibold rounded-lg hover:shadow-lg transition text-sm">
                    <i class="fas fa-check-circle mr-2"></i>
                    Conferma Importazione ({{ $valide }} pesate)
                </button>
            </div>
        </form>
    @else
        <div class="bg-white rounded-lg shadow px-5 py-3">
            <a href="{{ route('admin.pesate.import') }}"
               class="inline-block px-6 py-2.5 bg-gray-200 text-gray-700 text-center rounded-lg hover:bg-gray-300 transition text-sm font-medium">
                <i class="fas fa-arrow-left mr-2"></i>
                Torna al Form di Upload
            </a>
        </div>
    @endif

</div>
@endsection
