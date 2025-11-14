@extends('emails.layout')

@section('content')
    <h2 class="email-title">Ciao {{ $professionista->nome }}!</h2>

    <p class="email-text">
        Abbiamo generato una nuova password temporanea per il tuo account MA.GIA DONNA.
    </p>

    <div class="email-info-box">
        <p style="margin: 0; font-size: 14px;"><strong>Email:</strong> {{ $professionista->email }}</p>
        <p style="margin: 10px 0 0 0; font-size: 18px;">
            <strong>Password Temporanea:</strong>
            <span style="font-size: 24px; color: #e91e63; font-family: 'Courier New', monospace; letter-spacing: 2px;">{{ $passwordTemporanea }}</span>
        </p>
    </div>

    <div class="email-warning-box">
        <p style="margin: 0; font-size: 14px;">
            <strong>⚠️ Importante:</strong> Questa password è valida per <strong>24 ore</strong>
            (scade il {{ $scadenza->format('d/m/Y') }} alle {{ $scadenza->format('H:i') }}).
        </p>
        <p style="margin: 10px 0 0 0; font-size: 14px;">
            Dopo la scadenza dovrai richiedere una nuova password temporanea all'amministratore.
        </p>
    </div>

    <p class="email-text">
        <strong>Per accedere alla piattaforma:</strong>
    </p>

    <ol style="font-size: 16px; color: #666666; line-height: 1.8;">
        <li>Vai alla pagina di login</li>
        <li>Inserisci la tua email e la password temporanea</li>
        <li>Cambia immediatamente la password dal tuo profilo</li>
    </ol>

    <div style="text-align: center; margin: 30px 0;">
        <a href="{{ $loginUrl }}" class="email-button">
            Accedi alla Piattaforma
        </a>
    </div>

    <div class="email-divider"></div>

    <p class="email-text" style="font-size: 14px; color: #999999;">
        <strong>Perché ho ricevuto questa email?</strong><br>
        Un amministratore ha resettato la tua password. Se non hai richiesto questo reset,
        contatta immediatamente l'amministratore.
    </p>

    <p class="email-text" style="font-size: 14px; color: #999999;">
        <strong>Suggerimenti per la sicurezza:</strong><br>
        • Usa una password lunga (almeno 8 caratteri)<br>
        • Combina lettere maiuscole, minuscole, numeri e simboli<br>
        • Non condividere mai la tua password con nessuno<br>
        • Cambia la password regolarmente
    </p>
@endsection
