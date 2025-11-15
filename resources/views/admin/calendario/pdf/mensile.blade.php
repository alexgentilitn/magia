<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendario {{ $nomeMese }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
            line-height: 1.4;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 3px solid #e91e63;
        }
        .header h1 {
            color: #9c27b0;
            font-size: 24px;
            margin-bottom: 5px;
        }
        .header h2 {
            color: #e91e63;
            font-size: 18px;
            margin-bottom: 10px;
        }
        .header .info {
            font-size: 11px;
            color: #666;
        }
        .statistiche {
            display: flex;
            justify-content: space-around;
            margin-bottom: 20px;
            padding: 15px;
            background: #f5f5f5;
            border-radius: 8px;
        }
        .stat-box {
            text-align: center;
        }
        .stat-box .numero {
            font-size: 20px;
            font-weight: bold;
            color: #9c27b0;
        }
        .stat-box .label {
            font-size: 9px;
            color: #666;
            text-transform: uppercase;
        }
        .lezione {
            margin-bottom: 15px;
            padding: 12px;
            border-left: 4px solid #e91e63;
            background: #fff;
            page-break-inside: avoid;
        }
        .lezione-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
        }
        .lezione-data {
            font-weight: bold;
            color: #9c27b0;
            font-size: 12px;
        }
        .lezione-orario {
            color: #666;
            font-size: 10px;
        }
        .lezione-titolo {
            font-size: 13px;
            font-weight: bold;
            color: #333;
            margin-bottom: 5px;
        }
        .lezione-dettagli {
            font-size: 9px;
            color: #666;
            margin-bottom: 3px;
        }
        .lezione-dettagli i {
            font-style: normal;
            color: #9c27b0;
        }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .badge-gruppo { background: #e1bee7; color: #6a1b9a; }
        .badge-individuale { background: #f8bbd0; color: #c2185b; }
        .badge-online { background: #bbdefb; color: #1565c0; }
        .badge-ibrida { background: #ffe0b2; color: #e65100; }
        .partecipanti {
            margin-top: 8px;
            padding-top: 8px;
            border-top: 1px dashed #ddd;
        }
        .partecipanti-header {
            font-size: 9px;
            font-weight: bold;
            color: #9c27b0;
            margin-bottom: 4px;
        }
        .partecipanti-lista {
            font-size: 8px;
            color: #666;
        }
        .giorno-separator {
            margin-top: 20px;
            margin-bottom: 10px;
            padding: 8px 12px;
            background: linear-gradient(135deg, #9c27b0 0%, #e91e63 100%);
            color: white;
            font-weight: bold;
            font-size: 12px;
            border-radius: 4px;
        }
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
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    {{-- Header --}}
    <div class="header">
        <h1>MA.GIA DONNA</h1>
        <h2>Calendario Lezioni - {{ $nomeMese }}</h2>
        <div class="info">
            Generato il {{ now()->locale('it')->isoFormat('D MMMM YYYY [alle] HH:mm') }}
        </div>
    </div>

    {{-- Statistiche Mensili --}}
    <div class="statistiche">
        <div class="stat-box">
            <div class="numero">{{ $statistiche['totale_lezioni'] }}</div>
            <div class="label">Lezioni</div>
        </div>
        <div class="stat-box">
            <div class="numero">{{ $statistiche['totale_partecipanti'] }}</div>
            <div class="label">Partecipanti</div>
        </div>
        <div class="stat-box">
            <div class="numero">{{ $statistiche['posti_occupati'] }} / {{ $statistiche['posti_disponibili'] }}</div>
            <div class="label">Posti (Occ./Tot.)</div>
        </div>
        <div class="stat-box">
            <div class="numero">
                {{ $statistiche['posti_disponibili'] > 0 ? round(($statistiche['posti_occupati'] / $statistiche['posti_disponibili']) * 100, 1) : 0 }}%
            </div>
            <div class="label">Tasso Occupazione</div>
        </div>
    </div>

    {{-- Lezioni raggruppate per giorno --}}
    @if($lezioniPerGiorno->count() > 0)
        @php
            $contatoreGiorni = 0;
        @endphp

        @foreach($lezioniPerGiorno as $data => $lezioniGiorno)
            {{-- Separator giorno --}}
            <div class="giorno-separator">
                {{ \Carbon\Carbon::parse($data)->locale('it')->isoFormat('dddd D MMMM YYYY') }}
                ({{ $lezioniGiorno->count() }} {{ $lezioniGiorno->count() === 1 ? 'lezione' : 'lezioni' }})
            </div>

            {{-- Lezioni del giorno --}}
            @foreach($lezioniGiorno as $lezione)
                <div class="lezione">
                    {{-- Header lezione --}}
                    <div class="lezione-header">
                        <div class="lezione-orario">
                            {{ $lezione->ora_inizio->format('H:i') }} - {{ $lezione->ora_fine->format('H:i') }}
                        </div>
                        <div>
                            @if($lezione->tipologia === 'gruppo')
                                <span class="badge badge-gruppo">Gruppo</span>
                            @elseif($lezione->tipologia === 'individuale')
                                <span class="badge badge-individuale">Individuale</span>
                            @elseif($lezione->tipologia === 'online')
                                <span class="badge badge-online">Online</span>
                            @elseif($lezione->tipologia === 'ibrida')
                                <span class="badge badge-ibrida">Ibrida</span>
                            @endif
                        </div>
                    </div>

                    {{-- Titolo --}}
                    <div class="lezione-titolo">
                        {{ $lezione->titolo }}
                    </div>

                    {{-- Dettagli --}}
                    @if($lezione->professionista)
                        <div class="lezione-dettagli">
                            <i>👤</i> {{ $lezione->professionista->nome }} {{ $lezione->professionista->cognome }}
                        </div>
                    @endif

                    @if($lezione->sede)
                        <div class="lezione-dettagli">
                            <i>📍</i> {{ $lezione->sede->nome }} - {{ $lezione->sede->indirizzo_citta }}
                        </div>
                    @endif

                    @if($lezione->programma)
                        <div class="lezione-dettagli">
                            <i>📋</i> {{ $lezione->programma->nome }}
                        </div>
                    @endif

                    <div class="lezione-dettagli">
                        <i>👥</i> Posti: {{ $lezione->posti_occupati }} / {{ $lezione->posti_totali }}
                        ({{ $lezione->posti_totali - $lezione->posti_occupati }} disponibili)
                    </div>

                    {{-- Partecipanti --}}
                    @if($lezione->clienti && $lezione->clienti->count() > 0)
                        <div class="partecipanti">
                            <div class="partecipanti-header">
                                Partecipanti ({{ $lezione->clienti->count() }}):
                            </div>
                            <div class="partecipanti-lista">
                                @foreach($lezione->clienti as $cliente)
                                    @php
                                        $pivot = $cliente->pivot;
                                        $stato = $pivot->stato_partecipazione ?? 'prenotato';
                                        $iconaStato = $stato === 'presente' ? '✓' : ($stato === 'assente' ? '✗' : '○');
                                    @endphp
                                    {{ $iconaStato }} {{ $cliente->nome }} {{ $cliente->cognome }}@if(!$loop->last), @endif
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            @endforeach

            {{-- Page break ogni 2 giorni per evitare PDF troppo lunghi --}}
            @php
                $contatoreGiorni++;
            @endphp
            @if($contatoreGiorni % 2 === 0 && !$loop->last)
                <div class="page-break"></div>
            @endif
        @endforeach
    @else
        <div style="text-align: center; padding: 40px; color: #999;">
            <p style="font-size: 14px;">Nessuna lezione programmata per questo mese.</p>
        </div>
    @endif

    {{-- Footer --}}
    <div class="footer">
        MA.GIA DONNA - Centro Wellness &amp; Fitness | www.agstudio.digital/magia
    </div>
</body>
</html>
