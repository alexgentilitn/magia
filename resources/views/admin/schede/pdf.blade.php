<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scheda Allenamento - {{ $scheda->nome_scheda }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 11pt;
            line-height: 1.4;
            color: #333;
        }

        .header {
            background: linear-gradient(135deg, #E91E8C 0%, #C41E6F 100%);
            color: white;
            padding: 25px 30px;
            margin-bottom: 25px;
        }

        .header h1 {
            font-size: 24pt;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .header .subtitle {
            font-size: 12pt;
            opacity: 0.95;
        }

        .info-box {
            background: #f8f9fa;
            border-left: 4px solid #E91E8C;
            padding: 15px 20px;
            margin-bottom: 20px;
        }

        .info-box h2 {
            font-size: 14pt;
            color: #E91E8C;
            margin-bottom: 10px;
            border-bottom: 2px solid #E91E8C;
            padding-bottom: 5px;
        }

        .info-grid {
            display: table;
            width: 100%;
            margin-top: 10px;
        }

        .info-row {
            display: table-row;
        }

        .info-label {
            display: table-cell;
            font-weight: bold;
            color: #555;
            padding: 5px 15px 5px 0;
            width: 30%;
        }

        .info-value {
            display: table-cell;
            color: #333;
            padding: 5px 0;
        }

        .day-section {
            margin-bottom: 25px;
            page-break-inside: avoid;
        }

        .day-header {
            background: #E91E8C;
            color: white;
            padding: 10px 20px;
            font-size: 14pt;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .exercise {
            background: #fff;
            border: 1px solid #ddd;
            border-left: 4px solid #E91E8C;
            padding: 15px;
            margin-bottom: 10px;
            page-break-inside: avoid;
        }

        .exercise-title {
            font-size: 12pt;
            font-weight: bold;
            color: #333;
            margin-bottom: 5px;
        }

        .exercise-category {
            display: inline-block;
            background: #E91E8C;
            color: white;
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 9pt;
            margin-left: 10px;
        }

        .exercise-description {
            color: #666;
            font-size: 10pt;
            margin: 8px 0;
            font-style: italic;
        }

        .exercise-details {
            display: table;
            width: 100%;
            margin-top: 10px;
            background: #f8f9fa;
            padding: 10px;
        }

        .detail-row {
            display: table-row;
        }

        .detail-label {
            display: table-cell;
            font-weight: bold;
            color: #555;
            padding: 3px 15px 3px 0;
            font-size: 10pt;
        }

        .detail-value {
            display: table-cell;
            color: #333;
            padding: 3px 0;
            font-size: 10pt;
        }

        .exercise-note {
            background: #fff8e1;
            border-left: 3px solid #ffc107;
            padding: 10px;
            margin-top: 10px;
            font-size: 9pt;
            color: #856404;
        }

        .note-icon {
            font-weight: bold;
            margin-right: 5px;
        }

        .notes-section {
            margin-top: 25px;
            padding: 20px;
            background: #f1f8ff;
            border-left: 4px solid #0366d6;
            page-break-inside: avoid;
        }

        .notes-section h3 {
            color: #0366d6;
            font-size: 12pt;
            margin-bottom: 10px;
        }

        .notes-section p {
            color: #333;
            font-size: 10pt;
            margin-bottom: 10px;
            line-height: 1.6;
        }

        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 9pt;
            color: #999;
            padding: 15px;
            border-top: 1px solid #ddd;
        }

        .page-break {
            page-break-after: always;
        }

        .stats-box {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }

        .stat-item {
            display: table-cell;
            text-align: center;
            padding: 15px;
            background: #f8f9fa;
            border: 1px solid #ddd;
        }

        .stat-value {
            font-size: 18pt;
            font-weight: bold;
            color: #E91E8C;
        }

        .stat-label {
            font-size: 9pt;
            color: #666;
            text-transform: uppercase;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>{{ $scheda->nome_scheda }}</h1>
        <div class="subtitle">Scheda di Allenamento Personalizzata</div>
    </div>

    <!-- Informazioni Cliente -->
    <div class="info-box">
        <h2>📋 Informazioni Cliente</h2>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Cliente:</div>
                <div class="info-value">{{ $scheda->cliente->cognome }} {{ $scheda->cliente->nome }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Email:</div>
                <div class="info-value">{{ $scheda->cliente->email }}</div>
            </div>
            @if($scheda->cliente->telefono)
            <div class="info-row">
                <div class="info-label">Telefono:</div>
                <div class="info-value">{{ $scheda->cliente->telefono }}</div>
            </div>
            @endif
            <div class="info-row">
                <div class="info-label">Professionista:</div>
                <div class="info-value">
                    @if($scheda->professionista)
                        {{ $scheda->professionista->nome }} {{ $scheda->professionista->cognome }}
                    @else
                        Non assegnato
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiche Scheda -->
    <div class="stats-box">
        <div class="stat-item">
            <div class="stat-value">{{ $scheda->numeroGiorniAllenamento() }}</div>
            <div class="stat-label">Giorni / Settimana</div>
        </div>
        <div class="stat-item">
            <div class="stat-value">{{ $scheda->numeroEserciziTotali() }}</div>
            <div class="stat-label">Esercizi Totali</div>
        </div>
        <div class="stat-item">
            <div class="stat-value">{{ $scheda->durata_settimane ?? '-' }}</div>
            <div class="stat-label">Settimane</div>
        </div>
        @if($scheda->data_inizio && $scheda->data_fine)
        <div class="stat-item">
            <div class="stat-value" style="font-size: 11pt;">{{ $scheda->data_inizio->format('d/m/Y') }}</div>
            <div class="stat-label">Data Inizio</div>
        </div>
        <div class="stat-item">
            <div class="stat-value" style="font-size: 11pt;">{{ $scheda->data_fine->format('d/m/Y') }}</div>
            <div class="stat-label">Data Fine</div>
        </div>
        @endif
    </div>

    <!-- Informazioni Scheda -->
    @if($scheda->descrizione || $scheda->obiettivi)
    <div class="info-box">
        <h2>🎯 Dettagli Programma</h2>
        @if($scheda->descrizione)
        <div class="info-row">
            <div class="info-label">Descrizione:</div>
            <div class="info-value">{{ $scheda->descrizione }}</div>
        </div>
        @endif
        @if($scheda->obiettivi)
        <div class="info-row" style="margin-top: 10px;">
            <div class="info-label">Obiettivi:</div>
            <div class="info-value">{{ $scheda->obiettivi }}</div>
        </div>
        @endif
    </div>
    @endif

    <!-- Programma Settimanale -->
    @php
        $esercizi_per_giorno = $scheda->eserciziPerGiorno();
        $giorni = ['Lunedì', 'Martedì', 'Mercoledì', 'Giovedì', 'Venerdì', 'Sabato', 'Domenica'];
    @endphp

    @if($esercizi_per_giorno->count() > 0)
        @foreach($giorni as $giorno)
            @if($esercizi_per_giorno->has($giorno))
                <div class="day-section">
                    <div class="day-header">
                        🏋️ {{ $giorno }} ({{ $esercizi_per_giorno[$giorno]->count() }} esercizi)
                    </div>

                    @foreach($esercizi_per_giorno[$giorno] as $esercizio)
                        <div class="exercise">
                            <div class="exercise-title">
                                {{ $esercizio->ordine }}. {{ $esercizio->nome_esercizio }}
                                <span class="exercise-category">{{ ucfirst($esercizio->categoria) }}</span>
                            </div>

                            @if($esercizio->descrizione)
                            <div class="exercise-description">
                                {{ $esercizio->descrizione }}
                            </div>
                            @endif

                            <div class="exercise-details">
                                @if($esercizio->serie || $esercizio->ripetizioni)
                                <div class="detail-row">
                                    <div class="detail-label">Serie x Ripetizioni:</div>
                                    <div class="detail-value">
                                        <strong>{{ $esercizio->serie ?? '-' }} x {{ $esercizio->ripetizioni ?? '-' }}</strong>
                                    </div>
                                </div>
                                @endif

                                @if($esercizio->recupero_secondi)
                                <div class="detail-row">
                                    <div class="detail-label">Tempo di Recupero:</div>
                                    <div class="detail-value">
                                        @php
                                            $minuti = floor($esercizio->recupero_secondi / 60);
                                            $secondi = $esercizio->recupero_secondi % 60;
                                        @endphp
                                        @if($minuti > 0)
                                            {{ $minuti }}min
                                        @endif
                                        @if($secondi > 0)
                                            {{ $secondi }}sec
                                        @endif
                                    </div>
                                </div>
                                @endif

                                @if($esercizio->peso_suggerito)
                                <div class="detail-row">
                                    <div class="detail-label">Peso Suggerito:</div>
                                    <div class="detail-value">{{ $esercizio->peso_suggerito }}</div>
                                </div>
                                @endif
                            </div>

                            @if($esercizio->note_esecuzione)
                            <div class="exercise-note">
                                <span class="note-icon">💡</span>
                                <strong>Nota:</strong> {{ $esercizio->note_esecuzione }}
                            </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        @endforeach
    @else
        <div class="info-box">
            <p style="text-align: center; color: #999;">Nessun esercizio inserito in questa scheda.</p>
        </div>
    @endif

    <!-- Note e Consigli -->
    @if($scheda->note_generali || $scheda->note_alimentazione || $scheda->consigli_professionista)
    <div class="page-break"></div>
    <div class="notes-section">
        <h2 style="color: #0366d6; font-size: 16pt; margin-bottom: 15px;">📝 Note e Consigli del Professionista</h2>

        @if($scheda->note_generali)
        <div style="margin-bottom: 15px;">
            <h3>Note Generali</h3>
            <p>{{ $scheda->note_generali }}</p>
        </div>
        @endif

        @if($scheda->note_alimentazione)
        <div style="margin-bottom: 15px;">
            <h3>Consigli Alimentazione</h3>
            <p>{{ $scheda->note_alimentazione }}</p>
        </div>
        @endif

        @if($scheda->consigli_professionista)
        <div style="margin-bottom: 15px;">
            <h3>Consigli del Professionista</h3>
            <p>{{ $scheda->consigli_professionista }}</p>
        </div>
        @endif
    </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        <p><strong>MA.GIA DONNA</strong> - Centro Wellness & Fitness</p>
        <p>Scheda generata il {{ now()->format('d/m/Y H:i') }}</p>
    </div>
</body>
</html>
