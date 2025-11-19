<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recupero Password</title>
</head>
<body style="margin: 0; padding: 0; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #f3f4f6;">
    <table role="presentation" style="width: 100%; border-collapse: collapse; background-color: #f3f4f6;">
        <tr>
            <td align="center" style="padding: 40px 0;">
                <table role="presentation" style="width: 600px; max-width: 100%; border-collapse: collapse; background-color: #ffffff; border-radius: 8px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">

                    <!-- Header -->
                    <tr>
                        <td style="padding: 40px 40px 20px; text-align: center; background: linear-gradient(135deg, #8b5cf6 0%, #ec4899 100%); border-radius: 8px 8px 0 0;">
                            <h1 style="margin: 0; color: #ffffff; font-size: 28px; font-weight: bold;">
                                🔐 Recupero Password
                            </h1>
                        </td>
                    </tr>

                    <!-- Contenuto -->
                    <tr>
                        <td style="padding: 40px;">
                            <p style="margin: 0 0 20px; color: #374151; font-size: 16px; line-height: 1.6;">
                                Ciao <strong>{{ $utente->nome }}</strong>,
                            </p>

                            <p style="margin: 0 0 20px; color: #374151; font-size: 16px; line-height: 1.6;">
                                Abbiamo ricevuto una richiesta di reset della password per il tuo account amministratore su MA.GIA DONNA.
                            </p>

                            <p style="margin: 0 0 30px; color: #374151; font-size: 16px; line-height: 1.6;">
                                Clicca sul pulsante qui sotto per reimpostare la tua password:
                            </p>

                            <!-- Bottone CTA -->
                            <table role="presentation" style="margin: 0 auto 30px;">
                                <tr>
                                    <td style="background: linear-gradient(135deg, #8b5cf6 0%, #ec4899 100%); border-radius: 6px; text-align: center;">
                                        <a href="{{ $resetUrl }}"
                                           style="display: inline-block; padding: 16px 40px; color: #ffffff; text-decoration: none; font-weight: bold; font-size: 16px;">
                                            Reimposta Password
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <!-- Link alternativo -->
                            <p style="margin: 0 0 20px; color: #6b7280; font-size: 14px; line-height: 1.6;">
                                Se il pulsante non funziona, copia e incolla questo link nel tuo browser:
                            </p>
                            <p style="margin: 0 0 30px; color: #8b5cf6; font-size: 12px; word-break: break-all; background-color: #f3f4f6; padding: 12px; border-radius: 4px;">
                                {{ $resetUrl }}
                            </p>

                            <!-- Info Scadenza -->
                            <div style="background-color: #fef3c7; border-left: 4px solid #f59e0b; padding: 16px; border-radius: 4px; margin-bottom: 30px;">
                                <p style="margin: 0; color: #92400e; font-size: 14px; line-height: 1.6;">
                                    <strong>⏰ Importante:</strong> Questo link è valido per <strong>24 ore</strong>. Dopo questo periodo dovrai richiedere un nuovo link.
                                </p>
                            </div>

                            <!-- Sicurezza -->
                            <div style="background-color: #fee2e2; border-left: 4px solid #ef4444; padding: 16px; border-radius: 4px;">
                                <p style="margin: 0 0 10px; color: #991b1b; font-size: 14px; line-height: 1.6;">
                                    <strong>🛡️ Non hai richiesto questo reset?</strong>
                                </p>
                                <p style="margin: 0; color: #991b1b; font-size: 14px; line-height: 1.6;">
                                    Se non sei stato tu a richiedere il reset della password, ignora questa email. La tua password rimarrà invariata.
                                </p>
                            </div>

                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="padding: 30px 40px; background-color: #f9fafb; border-radius: 0 0 8px 8px; text-align: center;">
                            <p style="margin: 0 0 10px; color: #6b7280; font-size: 14px;">
                                <strong>MA.GIA DONNA</strong>
                            </p>
                            <p style="margin: 0; color: #9ca3af; font-size: 12px;">
                                Questa è un'email automatica, non rispondere a questo messaggio.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
