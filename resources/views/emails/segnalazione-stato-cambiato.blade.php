<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aggiornamento Segnalazione</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            padding: 30px;
        }
        .status-badge {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 14px;
            margin: 10px 0;
        }
        .status-aperta { background: #fee2e2; color: #991b1b; }
        .status-in_lavorazione { background: #fef3c7; color: #92400e; }
        .status-risolta { background: #d1fae5; color: #065f46; }
        .info-box {
            background: #f9fafb;
            border-left: 4px solid #667eea;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .response-box {
            background: #ecfdf5;
            border-left: 4px solid #10b981;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .button {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white !important;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
            margin: 20px 0;
        }
        .footer {
            background: #f9fafb;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #6b7280;
            border-top: 1px solid #e5e7eb;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        td {
            padding: 8px;
            border-bottom: 1px solid #e5e7eb;
        }
        td:first-child {
            font-weight: bold;
            color: #6b7280;
            width: 150px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>📩 Aggiornamento Segnalazione</h1>
            <p style="margin: 5px 0 0; opacity: 0.9;">MA.GIA DONNA - Sistema Gestione</p>
        </div>

        <!-- Content -->
        <div class="content">
            <p>Ciao,</p>

            <p>La tua segnalazione <strong>#{{ $segnalazione->id }}</strong> ha cambiato stato:</p>

            <div style="text-align: center; margin: 20px 0;">
                @php
                    $vecchioStatoClass = 'status-' . str_replace(' ', '_', $vecchioStato);
                    $nuovoStatoClass = 'status-' . str_replace(' ', '_', $nuovoStato);

                    $vecchioStatoLabel = match($vecchioStato) {
                        'aperta' => '🔴 Aperta',
                        'in_lavorazione' => '🟡 In Lavorazione',
                        'risolta' => '🟢 Risolta',
                        default => ucfirst($vecchioStato),
                    };

                    $nuovoStatoLabel = match($nuovoStato) {
                        'aperta' => '🔴 Aperta',
                        'in_lavorazione' => '🟡 In Lavorazione',
                        'risolta' => '🟢 Risolta',
                        default => ucfirst($nuovoStato),
                    };
                @endphp

                <span class="status-badge {{ $vecchioStatoClass }}">{{ $vecchioStatoLabel }}</span>
                <span style="font-size: 20px; margin: 0 10px;">→</span>
                <span class="status-badge {{ $nuovoStatoClass }}">{{ $nuovoStatoLabel }}</span>
            </div>

            <!-- Dettagli Segnalazione -->
            <div class="info-box">
                <h3 style="margin-top: 0; color: #667eea;">📋 Dettagli Segnalazione</h3>
                <table>
                    <tr>
                        <td>Titolo:</td>
                        <td>{{ $segnalazione->titolo }}</td>
                    </tr>
                    <tr>
                        <td>Tipo:</td>
                        <td>{{ $segnalazione->tipo_label }}</td>
                    </tr>
                    <tr>
                        <td>Priorità:</td>
                        <td>{{ $segnalazione->priorita_label }}</td>
                    </tr>
                    <tr>
                        <td>Data Creazione:</td>
                        <td>{{ $segnalazione->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                </table>
            </div>

            <!-- Risposta/Soluzione -->
            @if($segnalazione->risposta)
            <div class="response-box">
                <h3 style="margin-top: 0; color: #10b981;">✅ Risposta / Soluzione</h3>
                <p style="white-space: pre-wrap; margin: 10px 0;">{{ $segnalazione->risposta }}</p>

                @if($segnalazione->risolutore)
                <p style="font-size: 12px; color: #6b7280; margin-top: 15px; padding-top: 15px; border-top: 1px solid #d1fae5;">
                    <strong>Gestita da:</strong> {{ $segnalazione->risolutore->nome }} {{ $segnalazione->risolutore->cognome }}
                    @if($segnalazione->risolto_il)
                        <br><strong>Data Risoluzione:</strong> {{ $segnalazione->risolto_il->format('d/m/Y H:i') }}
                    @endif
                </p>
                @endif
            </div>
            @endif

            <!-- Pulsante -->
            <div style="text-align: center;">
                <a href="{{ route('admin.impostazioni.segnalazioni.show', $segnalazione) }}" class="button">
                    👁️ Visualizza Segnalazione Completa
                </a>
            </div>

            <p style="margin-top: 30px; font-size: 14px; color: #6b7280;">
                Questa è una notifica automatica. Per visualizzare tutti i dettagli e la cronologia completa, accedi al pannello di amministrazione.
            </p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p><strong>MA.GIA DONNA</strong> - Sistema di Gestione</p>
            <p style="margin: 5px 0 0;">Questa email è stata inviata automaticamente. Non rispondere a questo messaggio.</p>
        </div>
    </div>
</body>
</html>
