<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #dc3545; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; background-color: #f9f9f9; }
        .motivo { background-color: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin: 15px 0; }
        .footer { text-align: center; padding: 20px; font-size: 12px; color: #777; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Aggiornamento Richiesta Professionista</h1>
        </div>

        <div class="content">
            <p>Gentile {{ $professionista->nome }} {{ $professionista->cognome }},</p>

            <p>Grazie per il tuo interesse a collaborare con noi come professionista.</p>

            <p>Dopo un'attenta valutazione, ci dispiace informarti che al momento <strong>non possiamo procedere</strong> con l'approvazione del tuo profilo.</p>

            @if($motivo)
            <div class="motivo">
                <strong>Motivazione:</strong><br>
                {{ $motivo }}
            </div>
            @endif

            <p>Ti invitiamo a ricontattarci se desideri ricevere maggiori informazioni o se le tue credenziali dovessero cambiare in futuro.</p>

            <p>Ti ringraziamo per la comprensione.</p>

            <p style="margin-top: 30px;">
                Cordiali saluti,<br>
                <strong>Il Team di Gestione</strong>
            </p>
        </div>

        <div class="footer">
            <p>Questa è una email automatica, si prega di non rispondere.</p>
        </div>
    </div>
</body>
</html>
