<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #28a745; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; background-color: #f9f9f9; }
        .button { display: inline-block; padding: 10px 20px; background-color: #007bff; color: white; text-decoration: none; border-radius: 5px; margin-top: 15px; }
        .footer { text-align: center; padding: 20px; font-size: 12px; color: #777; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>✓ Profilo Approvato!</h1>
        </div>

        <div class="content">
            <p>Gentile {{ $professionista->nome }} {{ $professionista->cognome }},</p>

            <p>Siamo lieti di comunicarti che il tuo profilo professionista è stato <strong>approvato con successo</strong>!</p>

            <p><strong>Codice Professionista:</strong> {{ $professionista->codice_professionista }}</p>

            <p>Ora puoi accedere alla tua area riservata e iniziare a gestire le tue lezioni, visualizzare i compensi e aggiornare il tuo profilo.</p>

            <p>Per accedere, utilizza le credenziali che ti abbiamo fornito in precedenza.</p>

            <p>Se hai domande o hai bisogno di supporto, non esitare a contattarci.</p>

            <p>Benvenuto nel nostro team!</p>

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
