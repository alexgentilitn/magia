<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Benvenuta in MA.GIA DONNA</title>
</head>
<body style="margin: 0; padding: 0; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #f7f7f7;">
    <table role="presentation" style="width: 100%; border-collapse: collapse; background-color: #f7f7f7;">
        <tr>
            <td align="center" style="padding: 40px 0;">

                <!-- Main Container -->
                <table role="presentation" style="width: 600px; max-width: 100%; border-collapse: collapse; background-color: #ffffff; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">

                    <!-- Header con gradiente -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #e91e8c 0%, #7b2869 100%); padding: 40px 20px; text-align: center;">
                            <h1 style="margin: 0; color: #ffffff; font-size: 32px; font-weight: bold;">MA.GIA DONNA</h1>
                            <p style="margin: 10px 0 0 0; color: #fce7f3; font-size: 16px;">Il tuo viaggio inizia ora</p>
                        </td>
                    </tr>

                    <!-- Icona Success -->
                    <tr>
                        <td align="center" style="padding: 40px 20px 20px 20px;">
                            <div style="width: 80px; height: 80px; background-color: #10b981; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; margin: 0 auto;">
                                <span style="font-size: 40px; color: #ffffff;">✓</span>
                            </div>
                        </td>
                    </tr>

                    <!-- Contenuto Principale -->
                    <tr>
                        <td style="padding: 0 40px 40px 40px;">
                            <h2 style="margin: 0 0 20px 0; color: #1f2937; font-size: 24px; font-weight: bold; text-align: center;">
                                Benvenuta, {{ $nome }}!
                            </h2>

                            <p style="margin: 0 0 20px 0; color: #4b5563; font-size: 16px; line-height: 1.6;">
                                Siamo felici di darti il benvenuto nella community <strong>MA.GIA DONNA</strong>!
                                Il tuo pagamento è stato confermato e il tuo account è ora attivo.
                            </p>

                            <!-- Credenziali Box -->
                            <table role="presentation" style="width: 100%; border-collapse: collapse; background-color: #f9fafb; border-radius: 8px; margin: 30px 0;">
                                <tr>
                                    <td style="padding: 24px;">
                                        <h3 style="margin: 0 0 16px 0; color: #7b2869; font-size: 18px; font-weight: bold;">
                                            Le tue credenziali di accesso
                                        </h3>

                                        <table role="presentation" style="width: 100%;">
                                            <tr>
                                                <td style="padding: 8px 0; color: #6b7280; font-size: 14px; width: 30%;">
                                                    <strong>Email:</strong>
                                                </td>
                                                <td style="padding: 8px 0; color: #1f2937; font-size: 14px; font-family: 'Courier New', monospace;">
                                                    {{ $email }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 8px 0; color: #6b7280; font-size: 14px;">
                                                    <strong>Password:</strong>
                                                </td>
                                                <td style="padding: 8px 0; color: #1f2937; font-size: 14px;">
                                                    La password che hai creato durante la registrazione
                                                </td>
                                            </tr>
                                        </table>

                                        <div style="margin-top: 20px; padding: 12px; background-color: #fef3c7; border-left: 4px solid #f59e0b; border-radius: 4px;">
                                            <p style="margin: 0; color: #92400e; font-size: 13px;">
                                                <strong>Nota importante:</strong> Per ragioni di sicurezza, ti verrà chiesto di cambiare la password al primo accesso.
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                            </table>

                            <!-- Call to Action Button -->
                            <table role="presentation" style="width: 100%; margin: 30px 0;">
                                <tr>
                                    <td align="center">
                                        <a href="{{ $loginUrl }}" style="display: inline-block; padding: 16px 48px; background: linear-gradient(135deg, #e91e8c 0%, #7b2869 100%); color: #ffffff; text-decoration: none; border-radius: 8px; font-size: 16px; font-weight: bold; box-shadow: 0 4px 6px rgba(123, 40, 105, 0.3);">
                                            Accedi alla tua Area
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <!-- Info Aggiuntive -->
                            <div style="margin-top: 30px; padding-top: 30px; border-top: 1px solid #e5e7eb;">
                                <h3 style="margin: 0 0 16px 0; color: #1f2937; font-size: 18px; font-weight: bold;">
                                    Cosa puoi fare ora?
                                </h3>
                                <ul style="margin: 0; padding-left: 20px; color: #4b5563; line-height: 1.8;">
                                    <li>Completa il tuo profilo personale</li>
                                    <li>Esplora i nostri programmi disponibili</li>
                                    <li>Prenota la tua prima lezione</li>
                                    <li>Scopri le ricette nella sezione alimentazione</li>
                                </ul>
                            </div>

                            <!-- Supporto -->
                            <div style="margin-top: 30px; padding: 20px; background-color: #eff6ff; border-radius: 8px;">
                                <p style="margin: 0 0 10px 0; color: #1e40af; font-size: 14px; font-weight: bold;">
                                    Hai bisogno di aiuto?
                                </p>
                                <p style="margin: 0; color: #3b82f6; font-size: 14px; line-height: 1.6;">
                                    Il nostro team è a tua disposizione:<br>
                                    📧 <a href="mailto:info@magiadonna.it" style="color: #3b82f6; text-decoration: none;">info@magiadonna.it</a><br>
                                    📞 <a href="tel:+390123456789" style="color: #3b82f6; text-decoration: none;">+39 012 345 6789</a>
                                </p>
                            </div>

                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f9fafb; padding: 30px 40px; text-align: center; border-top: 1px solid #e5e7eb;">
                            <p style="margin: 0 0 10px 0; color: #6b7280; font-size: 14px;">
                                <strong>MA.GIA DONNA</strong><br>
                                Il tuo centro benessere e fitness
                            </p>
                            <p style="margin: 10px 0; color: #9ca3af; font-size: 12px;">
                                Questa email è stata inviata automaticamente, si prega di non rispondere.<br>
                                Per assistenza, contattaci a <a href="mailto:info@magiadonna.it" style="color: #e91e8c;">info@magiadonna.it</a>
                            </p>
                            <p style="margin: 10px 0; color: #9ca3af; font-size: 11px;">
                                © {{ date('Y') }} MA.GIA DONNA. Tutti i diritti riservati.
                            </p>
                        </td>
                    </tr>

                </table>

            </td>
        </tr>
    </table>
</body>
</html>
