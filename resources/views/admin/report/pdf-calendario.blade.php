<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendario Lezioni</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
            color: #333;
        }
        .header {
            background: linear-gradient(90deg, #7B2869, #E91E8C);
            color: white;
            padding: 20px;
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            font-size: 24px;
            margin-bottom: 5px;
        }
        .header p {
            font-size: 12px;
        }
        .stats {
            margin-bottom: 20px;
            padding: 10px;
            background: #f5f5f5;
            border-radius: 5px;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
        }
        .stat-box {
            text-align: center;
            padding: 10px;
        }
        .stat-box .label {
            font-size: 9px;
            color: #666;
            margin-bottom: 5px;
        }
        .stat-box .value {
            font-size: 18px;
            font-weight: bold;
            color: #7B2869;
        }
        .giorno {
            margin-bottom: 20px;
            page-break-inside: avoid;
        }
        .giorno-header {
            background: #7B2869;
            color: white;
            padding: 8px 15px;
            font-weight: bold;
            font-size: 12px;
            margin-bottom: 10px;
        }
        .lezione {
            border: 1px solid #ddd;
            padding: 10px;
            margin-bottom: 8px;
            background: white;
        }
        .lezione-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            padding-bottom: 8px;
            border-bottom: 1px solid #eee;
        }
        .lezione-title {
            font-weight: bold;
            font-size: 11px;
            color: #333;
        }
        .lezione-orario {
            color: #7B2869;
            font-weight: bold;
            font-size: 11px;
        }
        .lezione-info {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 5px;
            font-size: 9px;
        }
        .info-item {
            color: #666;
        }
        .info-item strong {
            color: #333;
        }
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: bold;
        }
        .badge-gruppo { background: #9c27b0; color: white; }
        .badge-individuale { background: #e91e63; color: white; }
        .badge-online { background: #2196f3; color: white; }
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 8px;
            color: #999;
            padding: 10px;
            border-top: 1px solid #ddd;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🎯 Calendario Lezioni MA.GIA DONNA</h1>
        <p>Periodo: {{ \Carbon\Carbon::parse($dataInizio)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($dataFine)->format('d/m/Y') }}</p>
    </div>

    <div class="stats">
        <div class="stats-grid">
            <div class="stat-box">
                <div class="label">Totale Lezioni</div>
                <div class="value">{{ $statistiche['totale_lezioni'] }}</div>
            </div>
            <div class="stat-box">
                <div class="label">Posti Totali</div>
                <div class="value">{{ $statistiche['totale_posti'] }}</div>
            </div>
            <div class="stat-box">
                <div class="label">Posti Occupati</div>
                <div class="value">{{ $statistiche['posti_occupati'] }}</div>
            </div>
            <div class="stat-box">
                <div class="label">Tasso Occupazione</div>
                <div class="value">
                    {{ $statistiche['totale_posti'] > 0 ? round(($statistiche['posti_occupati'] / $statistiche['totale_posti']) * 100, 1) : 0 }}%
                </div>
            </div>
        </div>
    </div>

    @foreach($lezioniPerData as $data => $lezioniGiorno)
        <div class="giorno">
            <div class="giorno-header">
                {{ \Carbon\Carbon::parse($data)->isoFormat('dddd, D MMMM YYYY') }}
                ({{ $lezioniGiorno->count() }} {{ $lezioniGiorno->count() === 1 ? 'lezione' : 'lezioni' }})
            </div>

            @foreach($lezioniGiorno as $lezione)
                <div class="lezione">
                    <div class="lezione-header">
                        <div class="lezione-title">{{ $lezione->titolo }}</div>
                        <div class="lezione-orario">
                            {{ \Carbon\Carbon::parse($lezione->ora_inizio)->format('H:i') }} -
                            {{ \Carbon\Carbon::parse($lezione->ora_fine)->format('H:i') }}
                        </div>
                    </div>

                    <div class="lezione-info">
                        <div class="info-item">
                            <strong>Professionista:</strong>
                            {{ $lezione->professionista ? $lezione->professionista->nome . ' ' . $lezione->professionista->cognome : 'Non assegnato' }}
                        </div>
                        <div class="info-item">
                            <strong>Sede:</strong> {{ $lezione->sede ? $lezione->sede->nome : 'Online' }}
                        </div>
                        <div class="info-item">
                            <strong>Tipologia:</strong>
                            <span class="badge badge-{{ $lezione->tipologia }}">{{ ucfirst($lezione->tipologia) }}</span>
                        </div>
                        <div class="info-item">
                            <strong>Posti:</strong> {{ $lezione->posti_occupati }}/{{ $lezione->posti_totali }}
                        </div>
                        @if($lezione->programma)
                        <div class="info-item">
                            <strong>Programma:</strong> {{ $lezione->programma->nome }}
                        </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endforeach

    <div class="footer">
        Generato il {{ now()->format('d/m/Y H:i') }} - MA.GIA DONNA Centro Wellness
    </div>
</body>
</html>
