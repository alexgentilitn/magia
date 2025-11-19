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

    <!-- Tabella Anteprima Dettagliata -->
    <div class="bg-white rounded-lg shadow overflow-hidden mb-4">
        <div class="px-3 py-2 bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-bold text-gray-900 flex items-center">
                    <i class="fas fa-table text-fucsia-magia mr-2 text-xs"></i>
                    Anteprima Dettagliata
                </h3>
                <span class="text-[10px] text-gray-500 flex items-center">
                    <i class="fas fa-info-circle mr-1"></i>
                    Valori modificabili • Gestione omonimie
                </span>
            </div>
        </div>
        <div class="overflow-x-auto" style="max-height: 700px;">
            <table class="min-w-full divide-y divide-gray-200" style="font-size: 11px;">
                <thead class="bg-gray-50 sticky top-0 z-10">
                    <tr>
                        <th class="px-1 py-1.5 text-left text-[10px] font-semibold text-gray-600 uppercase tracking-tight" style="min-width: 35px;">#</th>
                        <th class="px-1 py-1.5 text-left text-[10px] font-semibold text-gray-600 uppercase tracking-tight" style="min-width: 180px;">Cliente</th>
                        <th class="px-1 py-1.5 text-left text-[10px] font-semibold text-gray-600 uppercase tracking-tight" style="min-width: 65px;">Peso<br><span class="text-[9px] text-gray-500">(kg)</span></th>
                        <th class="px-1 py-1.5 text-left text-[10px] font-semibold text-gray-600 uppercase tracking-tight" style="min-width: 55px;">BMI</th>
                        <th class="px-1 py-1.5 text-left text-[10px] font-semibold text-gray-600 uppercase tracking-tight" style="min-width: 65px;">Grasso<br><span class="text-[9px] text-gray-500">(%)</span></th>
                        <th class="px-1 py-1.5 text-left text-[10px] font-semibold text-gray-600 uppercase tracking-tight" style="min-width: 65px;">G.Visc.<br><span class="text-[9px] text-gray-500">(lv)</span></th>
                        <th class="px-1 py-1.5 text-left text-[10px] font-semibold text-gray-600 uppercase tracking-tight" style="min-width: 65px;">M.Musc.<br><span class="text-[9px] text-gray-500">(kg)</span></th>
                        <th class="px-1 py-1.5 text-left text-[10px] font-semibold text-gray-600 uppercase tracking-tight" style="min-width: 65px;">M.Schel.<br><span class="text-[9px] text-gray-500">(%)</span></th>
                        <th class="px-1 py-1.5 text-left text-[10px] font-semibold text-gray-600 uppercase tracking-tight" style="min-width: 65px;">BMR<br><span class="text-[9px] text-gray-500">(kcal)</span></th>
                        <th class="px-1 py-1.5 text-left text-[10px] font-semibold text-gray-600 uppercase tracking-tight" style="min-width: 65px;">Acqua<br><span class="text-[9px] text-gray-500">(%)</span></th>
                        <th class="px-1 py-1.5 text-left text-[10px] font-semibold text-gray-600 uppercase tracking-tight" style="min-width: 65px;">FFM<br><span class="text-[9px] text-gray-500">(kg)</span></th>
                        <th class="px-1 py-1.5 text-left text-[10px] font-semibold text-gray-600 uppercase tracking-tight" style="min-width: 65px;">Proteine<br><span class="text-[9px] text-gray-500">(%)</span></th>
                        <th class="px-1 py-1.5 text-left text-[10px] font-semibold text-gray-600 uppercase tracking-tight" style="min-width: 55px;">Età<br><span class="text-[9px] text-gray-500">Met.</span></th>
                        <th class="px-1 py-1.5 text-left text-[10px] font-semibold text-gray-600 uppercase tracking-tight" style="min-width: 90px;">Data</th>
                        <th class="px-1 py-1.5 text-center text-[10px] font-semibold text-gray-600 uppercase tracking-tight" style="min-width: 70px;">Stato</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($preview_data as $index => $row)
                        <tr class="{{ !empty($row['errors']) ? 'bg-red-50' : (!empty($row['has_omonimia']) ? 'bg-yellow-50' : 'hover:bg-gray-50') }}">
                            <!-- Hidden fields per questa riga -->
                            <td style="display:none;">
                                <input type="hidden" name="row_number_{{ $index }}" value="{{ $row['row'] }}">
                                <input type="hidden" name="nome_{{ $index }}" value="{{ $row['nome'] }}">
                                <input type="hidden" name="cognome_{{ $index }}" value="{{ $row['cognome'] }}">
                                <input type="hidden" name="codice_fiscale_{{ $index }}" value="{{ $row['codice_fiscale'] ?? '' }}">
                                <input type="hidden" name="has_errors_{{ $index }}" value="{{ !empty($row['errors']) ? '1' : '0' }}">
                                <input type="hidden" name="has_omonimia_{{ $index }}" value="{{ !empty($row['has_omonimia']) ? '1' : '0' }}">

                                @if(empty($row['has_omonimia']))
                                    {{-- Per clienti senza omonimia: invia "new" se cliente nuovo, altrimenti ID cliente esistente --}}
                                    <input type="hidden" name="cliente_id_{{ $index }}" value="{{ !empty($row['cliente_creato']) ? 'new' : ($row['cliente_id'] ?? '') }}">
                                @endif
                            </td>

                            <!-- Riga -->
                            <td class="px-1 py-1.5 font-bold text-gray-900">{{ $row['row'] }}</td>

                            <!-- Cliente con Dati Anagrafici -->
                            <td class="px-1 py-1.5">
                                <div class="space-y-0.5">
                                    <div class="font-bold text-gray-900 text-[11px] leading-tight">
                                        {{ $row['cognome'] }} {{ $row['nome'] }}
                                    </div>

                                    <!-- Badges Status -->
                                    <div class="flex flex-wrap gap-0.5">
                                        @if(!empty($row['cliente_creato']))
                                            <span class="px-1.5 py-0 bg-green-100 text-green-700 text-[9px] rounded-full font-semibold">
                                                <i class="fas fa-user-plus mr-0.5"></i>Nuovo
                                            </span>
                                        @endif

                                        @if(!empty($row['has_omonimia']))
                                            @if(!empty($row['possibili_clienti']) && count($row['possibili_clienti']) === 1)
                                                <span class="px-1.5 py-0 bg-blue-100 text-blue-800 text-[9px] rounded-full font-semibold">
                                                    <i class="fas fa-user-check mr-0.5"></i>Esistente
                                                </span>
                                            @else
                                                <span class="px-1.5 py-0 bg-yellow-100 text-yellow-800 text-[9px] rounded-full font-semibold">
                                                    <i class="fas fa-exclamation-triangle mr-0.5"></i>OMONIMIA
                                                </span>
                                            @endif
                                        @endif
                                    </div>

                                    <!-- Dati Identificativi -->
                                    <div class="text-[9px] text-gray-600 space-y-0">
                                        @if(!empty($row['codice_fiscale']))
                                            <div class="leading-tight"><strong>CF:</strong> {{ $row['codice_fiscale'] }}</div>
                                        @endif
                                    </div>

                                    <!-- Gestione Omonimia / Cliente Esistente -->
                                    @if(!empty($row['has_omonimia']) && !empty($row['possibili_clienti']))
                                        @php
                                            $numClienti = count($row['possibili_clienti']);
                                            $isOmonimia = $numClienti > 1;
                                            $bgColor = $isOmonimia ? 'bg-yellow-50 border-yellow-200' : 'bg-blue-50 border-blue-200';
                                            $textColor = $isOmonimia ? 'text-yellow-900' : 'text-blue-900';
                                        @endphp
                                        <div class="mt-1 p-1.5 {{ $bgColor }} border rounded">
                                            <div class="font-semibold {{ $textColor }} text-[9px] mb-1 leading-tight">
                                                @if($isOmonimia)
                                                    <i class="fas fa-users mr-0.5"></i>
                                                    {{ $numClienti }} clienti con stesso nome - Scegli azione:
                                                @else
                                                    <i class="fas fa-user-check mr-0.5"></i>
                                                    Cliente trovato - Conferma o crea nuovo:
                                                @endif
                                            </div>
                                            <div class="space-y-0.5">
                                                @foreach($row['possibili_clienti'] as $pIndex => $possibileCliente)
                                                    <label class="flex items-start p-1 bg-white border border-gray-300 rounded hover:bg-gray-50 cursor-pointer">
                                                        <input type="radio"
                                                               name="cliente_id_{{ $index }}"
                                                               value="{{ $possibileCliente['id'] }}"
                                                               {{ $pIndex === 0 ? 'checked' : '' }}
                                                               class="mt-0.5 mr-1 text-[9px]">
                                                        <div class="flex-1 text-[9px] leading-tight">
                                                            <div class="font-semibold text-gray-900">
                                                                {{ $possibileCliente['cognome'] }} {{ $possibileCliente['nome'] }}
                                                            </div>
                                                            @if(!empty($possibileCliente['codice_fiscale']))
                                                                <div class="text-gray-600">CF: {{ $possibileCliente['codice_fiscale'] }}</div>
                                                            @endif
                                                            @if(!empty($possibileCliente['email']))
                                                                <div class="text-gray-600">{{ $possibileCliente['email'] }}</div>
                                                            @endif
                                                            @if(!empty($possibileCliente['telefono']))
                                                                <div class="text-gray-600">{{ $possibileCliente['telefono'] }}</div>
                                                            @endif
                                                            @if(!empty($possibileCliente['data_iscrizione']))
                                                                <div class="text-gray-500">Iscr: {{ \Carbon\Carbon::parse($possibileCliente['data_iscrizione'])->format('d/m/Y') }}</div>
                                                            @endif
                                                        </div>
                                                    </label>
                                                @endforeach

                                                <!-- Opzione Crea Nuovo -->
                                                <label class="flex items-start p-1 bg-green-50 border border-green-300 rounded hover:bg-green-100 cursor-pointer">
                                                    <input type="radio"
                                                           name="cliente_id_{{ $index }}"
                                                           value="new"
                                                           class="mt-0.5 mr-1">
                                                    <div class="flex-1 text-[9px] leading-tight">
                                                        <div class="font-bold text-green-900">
                                                            <i class="fas fa-user-plus mr-0.5"></i>
                                                            Crea Nuovo
                                                        </div>
                                                        <div class="text-green-700">
                                                            Nuovo profilo stesso nome
                                                        </div>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                    @endif

                                    <!-- Errori -->
                                    @if(!empty($row['errors']))
                                        <div class="mt-1 p-1 bg-red-50 border border-red-200 rounded">
                                            @foreach($row['errors'] as $error)
                                                <div class="text-[9px] text-red-600 leading-tight">
                                                    <i class="fas fa-exclamation-triangle mr-0.5"></i>{{ $error }}
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </td>

                            <!-- Campi Misurazione Editabili -->
                            <td class="px-1 py-1.5">
                                <input type="number" name="peso_{{ $index }}" value="{{ $row['peso'] ?? '' }}" step="0.01"
                                       class="w-full px-1 py-0.5 border border-gray-300 rounded text-[11px] focus:ring-1 focus:ring-blue-400 focus:border-blue-400">
                            </td>
                            <td class="px-1 py-1.5">
                                <input type="number" name="bmi_{{ $index }}" value="{{ $row['bmi'] ?? '' }}" step="0.01"
                                       class="w-full px-1 py-0.5 border border-gray-300 rounded text-[11px] focus:ring-1 focus:ring-blue-400 focus:border-blue-400">
                            </td>
                            <td class="px-1 py-1.5">
                                <input type="number" name="grasso_corporeo_{{ $index }}" value="{{ $row['grasso_corporeo'] ?? '' }}" step="0.01"
                                       class="w-full px-1 py-0.5 border border-gray-300 rounded text-[11px] focus:ring-1 focus:ring-blue-400 focus:border-blue-400">
                            </td>
                            <td class="px-1 py-1.5">
                                <input type="number" name="grasso_viscerale_{{ $index }}" value="{{ $row['grasso_viscerale'] ?? '' }}" step="0.01"
                                       class="w-full px-1 py-0.5 border border-gray-300 rounded text-[11px] focus:ring-1 focus:ring-blue-400 focus:border-blue-400">
                            </td>
                            <td class="px-1 py-1.5">
                                <input type="number" name="massa_muscolare_{{ $index }}" value="{{ $row['massa_muscolare'] ?? '' }}" step="0.01"
                                       class="w-full px-1 py-0.5 border border-gray-300 rounded text-[11px] focus:ring-1 focus:ring-blue-400 focus:border-blue-400">
                            </td>
                            <td class="px-1 py-1.5">
                                <input type="number" name="muscolo_scheletrico_{{ $index }}" value="{{ $row['muscolo_scheletrico'] ?? '' }}" step="0.01"
                                       class="w-full px-1 py-0.5 border border-gray-300 rounded text-[11px] focus:ring-1 focus:ring-blue-400 focus:border-blue-400">
                            </td>
                            <td class="px-1 py-1.5">
                                <input type="number" name="bmr_{{ $index }}" value="{{ $row['bmr'] ?? '' }}" step="1"
                                       class="w-full px-1 py-0.5 border border-gray-300 rounded text-[11px] focus:ring-1 focus:ring-blue-400 focus:border-blue-400">
                            </td>
                            <td class="px-1 py-1.5">
                                <input type="number" name="acqua_corporea_{{ $index }}" value="{{ $row['acqua_corporea'] ?? '' }}" step="0.01"
                                       class="w-full px-1 py-0.5 border border-gray-300 rounded text-[11px] focus:ring-1 focus:ring-blue-400 focus:border-blue-400">
                            </td>
                            <td class="px-1 py-1.5">
                                <input type="number" name="peso_corporeo_senza_grassi_{{ $index }}" value="{{ $row['peso_corporeo_senza_grassi'] ?? '' }}" step="0.01"
                                       class="w-full px-1 py-0.5 border border-gray-300 rounded text-[11px] focus:ring-1 focus:ring-blue-400 focus:border-blue-400">
                            </td>
                            <td class="px-1 py-1.5">
                                <input type="number" name="proteine_{{ $index }}" value="{{ $row['proteine'] ?? '' }}" step="0.01"
                                       class="w-full px-1 py-0.5 border border-gray-300 rounded text-[11px] focus:ring-1 focus:ring-blue-400 focus:border-blue-400">
                            </td>
                            <td class="px-1 py-1.5">
                                <input type="number" name="eta_metabolica_{{ $index }}" value="{{ $row['eta_metabolica'] ?? '' }}" step="1"
                                       class="w-full px-1 py-0.5 border border-gray-300 rounded text-[11px] focus:ring-1 focus:ring-blue-400 focus:border-blue-400">
                            </td>
                            <td class="px-1 py-1.5">
                                <input type="date" name="data_rilevazione_{{ $index }}" value="{{ $row['data_rilevazione'] ?? '' }}"
                                       class="w-full px-1 py-0.5 border border-gray-300 rounded text-[11px] focus:ring-1 focus:ring-blue-400 focus:border-blue-400">
                            </td>

                            <!-- Azioni -->
                            <td class="px-1 py-1.5 text-center">
                                @if(empty($row['errors']))
                                    <span class="inline-block px-1.5 py-0.5 bg-green-100 text-green-800 text-[9px] font-semibold rounded-full">
                                        <i class="fas fa-check mr-0.5"></i>OK
                                    </span>
                                @else
                                    <span class="inline-block px-1.5 py-0.5 bg-red-100 text-red-800 text-[9px] font-semibold rounded-full">
                                        <i class="fas fa-times mr-0.5"></i>Errore
                                    </span>
                                @endif
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
