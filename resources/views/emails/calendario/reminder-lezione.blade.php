@extends('emails.layout')

@section('content')
<h2 class="email-title">⏰ Promemoria Lezione Domani</h2>

<p class="email-text">
    Ciao <strong>{{ $cliente->nome }}</strong>,
</p>

<p class="email-text">
    Ti ricordiamo che domani hai una lezione prenotata!
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

<div style="text-align: center;">
    <a href="{{ url('/cliente/calendario') }}" class="email-button">
        Visualizza nel Calendario
    </a>
</div>

<div class="email-divider"></div>

<p class="email-text" style="font-size: 14px; color: #999;">
    <strong>Cosa portare:</strong>
</p>
<ul style="color: #666; font-size: 14px;">
    <li>Abbigliamento comodo e scarpe da ginnastica</li>
    <li>Asciugamano personale</li>
    <li>Bottiglia d'acqua</li>
    <li>Buon umore! 😊</li>
</ul>

<div class="email-warning-box">
    <p style="margin: 0;"><strong>⚠️ Annullamento</strong></p>
    <p style="margin: 10px 0 0 0;">
        Se non puoi partecipare, ti preghiamo di annullare la prenotazione almeno <strong>3 ore prima</strong>
        dell'inizio per permettere ad altri di prendere il tuo posto.
    </p>
</div>

<p class="email-text">
    Ti aspettiamo! 💪
</p>
@endsection
