@extends('emails.layout')

@section('content')
<h2 class="email-title">✅ Prenotazione Confermata!</h2>

<p class="email-text">
    Ciao <strong>{{ $cliente->nome }}</strong>,
</p>

<p class="email-text">
    La tua prenotazione è stata confermata con successo!
</p>

<div class="email-info-box">
    <p style="margin: 0 0 10px 0;"><strong>📅 Dettagli Lezione:</strong></p>
    <p style="margin: 5px 0;"><strong>Lezione:</strong> {{ $lezione->titolo }}</p>
    <p style="margin: 5px 0;"><strong>Data:</strong> {{ $lezione->data->format('d/m/Y') }}</p>
    <p style="margin: 5px 0;"><strong>Orario:</strong> {{ \Carbon\Carbon::parse($lezione->ora_inizio)->format('H:i') }} - {{ \Carbon\Carbon::parse($lezione->ora_fine)->format('H:i') }}</p>
    <p style="margin: 5px 0;"><strong>Durata:</strong> {{ $lezione->durata_minuti }} minuti</p>
    @if($lezione->professionista)
    <p style="margin: 5px 0;"><strong>Istruttore:</strong> {{ $lezione->professionista->nome }} {{ $lezione->professionista->cognome }}</p>
    @endif
    @if($lezione->sede)
    <p style="margin: 5px 0;"><strong>Sede:</strong> {{ $lezione->sede->nome }}</p>
    <p style="margin: 5px 0;"><strong>Indirizzo:</strong> {{ $lezione->sede->indirizzo }}, {{ $lezione->sede->citta }}</p>
    @endif
</div>

@if($listaAttesa ?? false)
<div class="email-warning-box">
    <p style="margin: 0;"><strong>⏳ Lista d'Attesa</strong></p>
    <p style="margin: 10px 0 0 0;">
        La lezione è al completo. Sei stato aggiunto alla lista d'attesa e riceverai una notifica
        se si liberasse un posto.
    </p>
</div>
@else
<div style="text-align: center;">
    <a href="{{ url('/cliente/calendario') }}" class="email-button">
        Visualizza nel Calendario
    </a>
</div>
@endif

<div class="email-divider"></div>

<p class="email-text" style="font-size: 14px; color: #999;">
    <strong>Cosa fare ora?</strong>
</p>
<ul style="color: #666; font-size: 14px;">
    <li>Riceverai un promemoria 24 ore prima della lezione</li>
    <li>Presentati 10 minuti prima per il check-in</li>
    <li>Porta con te un asciugamano e abbigliamento comodo</li>
</ul>

<p class="email-text" style="font-size: 14px; color: #999;">
    Se hai bisogno di annullare, accedi alla tua area personale almeno 3 ore prima dell'inizio.
</p>
@endsection
