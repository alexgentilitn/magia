<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Report Professionisti</title>
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
            font-size: 22px;
            margin-bottom: 5px;
        }
        .header p {
            font-size: 11px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        thead {
            background: #7B2869;
            color: white;
        }
        th {
            padding: 10px;
            text-align: left;
            font-size: 10px;
            font-weight: bold;
        }
        td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
            font-size: 9px;
        }
        tbody tr:nth-child(even) {
            background: #f9f9f9;
        }
        tbody tr:hover {
            background: #f0f0f0;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .badge {
            display: inline-block;
            padding: 3px 6px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: bold;
        }
        .badge-high { background: #4caf50; color: white; }
        .badge-medium { background: #ff9800; color: white; }
        .badge-low { background: #f44336; color: white; }
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
        .summary {
            margin-bottom: 20px;
            padding: 15px;
            background: #f5f5f5;
            border-left: 4px solid #7B2869;
        }
        .summary h3 {
            font-size: 12px;
            margin-bottom: 10px;
            color: #7B2869;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>📊 Report Performance Professionisti</h1>
        <p>Periodo: {{ \Carbon\Carbon::parse($dataInizio)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($dataFine)->format('d/m/Y') }}</p>
    </div>

    <div class="summary">
        <h3>Riepilogo</h3>
        <p><strong>Totale Professionisti:</strong> {{ $performance->count() }}</p>
        <p><strong>Totale Lezioni Erogate:</strong> {{ $performance->sum('totale_lezioni') }}</p>
        <p><strong>Tasso Occupazione Medio:</strong> {{ $performance->count() > 0 ? round($performance->avg('tasso_occupazione'), 1) : 0 }}%</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Professionista</th>
                <th class="text-center">Lezioni</th>
                <th class="text-center">Posti Totali</th>
                <th class="text-center">Posti Occupati</th>
                <th class="text-center">Tasso Occupazione</th>
                <th class="text-center">Performance</th>
            </tr>
        </thead>
        <tbody>
            @foreach($performance as $index => $perf)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td><strong>{{ $perf->professionista_nome }} {{ $perf->professionista_cognome }}</strong></td>
                <td class="text-center">{{ $perf->totale_lezioni }}</td>
                <td class="text-center">{{ $perf->posti_totali }}</td>
                <td class="text-center">{{ $perf->posti_occupati }}</td>
                <td class="text-center"><strong>{{ $perf->tasso_occupazione }}%</strong></td>
                <td class="text-center">
                    @if($perf->tasso_occupazione >= 80)
                        <span class="badge badge-high">Eccellente</span>
                    @elseif($perf->tasso_occupazione >= 60)
                        <span class="badge badge-medium">Buona</span>
                    @else
                        <span class="badge badge-low">Da migliorare</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Generato il {{ now()->format('d/m/Y H:i') }} - MA.GIA DONNA Centro Wellness
    </div>
</body>
</html>
