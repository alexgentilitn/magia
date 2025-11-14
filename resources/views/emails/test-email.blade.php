@extends('emails.layout')

@section('content')
    <h2 class="email-title">✅ Test Email Riuscito!</h2>

    <p class="email-text">
        Congratulazioni! La configurazione SMTP del tuo sistema <strong>MA.GIA DONNA</strong> funziona correttamente.
    </p>

    <p class="email-text">
        Questa è un'email di test inviata il <strong>{{ $dataOra }}</strong> per verificare il corretto funzionamento del sistema di invio email.
    </p>

    <div class="email-info-box">
        <p style="margin: 0 0 10px 0; font-weight: bold; color: #e91e63;">📧 Configurazione SMTP Utilizzata:</p>
        <table style="width: 100%; font-size: 14px; color: #666;">
            <tr>
                <td style="padding: 5px 0;"><strong>Host:</strong></td>
                <td style="padding: 5px 0;">{{ $config['host'] }}</td>
            </tr>
            <tr>
                <td style="padding: 5px 0;"><strong>Porta:</strong></td>
                <td style="padding: 5px 0;">{{ $config['port'] }}</td>
            </tr>
            <tr>
                <td style="padding: 5px 0;"><strong>Encryption:</strong></td>
                <td style="padding: 5px 0;">{{ strtoupper($config['encryption']) }}</td>
            </tr>
            <tr>
                <td style="padding: 5px 0;"><strong>Username:</strong></td>
                <td style="padding: 5px 0;">{{ $config['username'] }}</td>
            </tr>
            <tr>
                <td style="padding: 5px 0;"><strong>From Address:</strong></td>
                <td style="padding: 5px 0;">{{ $config['from_address'] }}</td>
            </tr>
            <tr>
                <td style="padding: 5px 0;"><strong>From Name:</strong></td>
                <td style="padding: 5px 0;">{{ $config['from_name'] }}</td>
            </tr>
        </table>
    </div>

    <div class="email-warning-box">
        <p style="margin: 0; font-size: 14px;">
            <strong>📋 Nota:</strong> Se ricevi questa email, significa che il sistema è configurato correttamente e può inviare:
        </p>
        <ul style="margin: 10px 0 0 20px; font-size: 14px;">
            <li>Password temporanee ai professionisti</li>
            <li>Notifiche agli utenti</li>
            <li>Report e comunicazioni automatiche</li>
        </ul>
    </div>

    <p class="email-text" style="margin-top: 30px;">
        Se non hai richiesto questo test, ignora questa email.
    </p>

    <p class="email-text" style="color: #999; font-size: 14px; margin-top: 20px;">
        Sistema di gestione fitness & wellness<br>
        <strong>MA.GIA DONNA</strong>
    </p>
@endsection
