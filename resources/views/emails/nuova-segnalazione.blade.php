<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuova Segnalazione</title>
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
        .tipo-bug { background: #fee2e2; color: #991b1b; }
        .tipo-suggerimento { background: #dbeafe; color: #1e40af; }
        .tipo-altro { background: #e5e7eb; color: #374151; }
        .priorita-alta { background: #fee2e2; color: #991b1b; }
        .priorita-media { background: #fef3c7; color: #92400e; }
        .priorita-bassa { background: #dbeafe; color: #1e40af; }
        .info-box {
            background: #f9fafb;
            border-left: 4px solid #667eea;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .success-box {
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
        .description-box {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            padding: 15px;
            margin: 15px 0;
            border-radius: 6px;
            white-space: pre-wrap;
            font-size: 14px;
            line-height: 1.6;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>📝 Nuova Segnalazione Ricevuta</h1>
            <p style="margin: 5px 0 0; opacity: 0.9;">MA.GIA DONNA - Sistema Gestione</p>
        </div>

        <!-- Content -->
        <div class="content">
            <div class="success-box">
                <h3 style="margin-top: 0; color: #10b981;">✅ Segnalazione Ricevuta con Successo!</h3>
                <p style="margin: 0;">Grazie per la tua segnalazione. Il team è stato notificato e la prenderà in carico al più presto.</p>
            </div>

            <p>Ciao,</p>

            <p>Abbiamo ricevuto la tua segnalazione <strong>#{{ $segnalazione->id }}</strong>.</p>

            <div style="text-align: center; margin: 20px 0;">
                @php
                    $tipoClass = 'tipo-' . $segnalazione->tipo;
                    $prioritaClass = 'priorita-' . $segnalazione->priorita;

                    $tipoIcon = match($segnalazione->tipo) {
                        'bug' => '🐛',
                        'suggerimento' => '💡',
                        'altro' => '📌',
                        default => '📋',
                    };

                    $prioritaIcon = match($segnalazione->priorita) {
                        'alta' => '🔴',
                        'media' => '🟡',
                        'bassa' => '🔵',
                        default => '⚪',
                    };
                @endphp

                <span class="status-badge {{ $tipoClass }}">{{ $tipoIcon }} {{ $segnalazione->tipo_label }}</span>
                <span class="status-badge {{ $prioritaClass }}">{{ $prioritaIcon }} Priorità {{ $segnalazione->priorita_label }}</span>
            </div>

            <!-- Dettagli Segnalazione -->
            <div class="info-box">
                <h3 style="margin-top: 0; color: #667eea;">📋 Dettagli Segnalazione</h3>
                <table>
                    <tr>
                        <td>Numero:</td>
                        <td><strong>#{{ $segnalazione->id }}</strong></td>
                    </tr>
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
                        <td>Stato:</td>
                        <td>🔴 Aperta</td>
                    </tr>
                    <tr>
                        <td>Data Creazione:</td>
                        <td>{{ $segnalazione->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                    @if($segnalazione->utente)
                    <tr>
                        <td>Segnalata da:</td>
                        <td>{{ $segnalazione->utente->nome }} {{ $segnalazione->utente->cognome }}</td>
                    </tr>
                    @endif
                </table>
            </div>

            <!-- Descrizione -->
            <h3 style="color: #667eea; margin-bottom: 10px;">📄 Descrizione</h3>
            <div class="description-box">{{ $segnalazione->descrizione }}</div>

            <!-- Prossimi Passi -->
            <div style="background: #fffbeb; border-left: 4px solid #f59e0b; padding: 15px; margin: 20px 0; border-radius: 4px;">
                <h4 style="margin-top: 0; color: #92400e;">⏳ Prossimi Passi</h4>
                <ul style="margin: 10px 0; padding-left: 20px; color: #78350f;">
                    <li>Il team esaminerà la tua segnalazione</li>
                    <li>Riceverai aggiornamenti via email quando cambierà lo stato</li>
                    <li>Puoi visualizzare lo stato in qualsiasi momento dal pannello</li>
                </ul>
            </div>

            <!-- Pulsante -->
            <div style="text-align: center;">
                <a href="{{ route('admin.impostazioni.segnalazioni.show', $segnalazione) }}" class="button">
                    👁️ Visualizza Segnalazione Completa
                </a>
            </div>

            <p style="margin-top: 30px; font-size: 14px; color: #6b7280;">
                Ti invieremo un'email quando ci saranno aggiornamenti sulla tua segnalazione. Grazie per aiutarci a migliorare il sistema!
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
