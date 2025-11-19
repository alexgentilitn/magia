@extends('layouts.admin')

@section('titolo', 'Dettaglio Pagamento Professionista')

@section('contenuto')
<div class="container-fluid px-4">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Dettaglio Pagamento</h1>
            <p class="text-muted mb-0">{{ $pagamento->professionista->nome_completo }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.professionisti.pagamenti.edit', $pagamento->id) }}" class="btn btn-warning">
                <i class="fas fa-edit me-1"></i> Modifica
            </a>
            <a href="{{ route('admin.professionisti.pagamenti.index') }}" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left me-1"></i> Torna alla Lista
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Colonna Principale -->
        <div class="col-lg-8">
            <!-- Info Pagamento -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informazioni Pagamento</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="text-muted small">Periodo di Riferimento</label>
                            <p class="fw-bold mb-0">
                                <i class="fas fa-calendar me-1"></i>
                                {{ $pagamento->descrizione_periodo }}
                            </p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Data Pagamento</label>
                            <p class="fw-bold mb-0">
                                <i class="fas fa-calendar-check me-1"></i>
                                {{ $pagamento->data_pagamento->format('d/m/Y') }}
                            </p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="text-muted small">Metodo Pagamento</label>
                            <p class="mb-0">
                                @if($pagamento->metodo_pagamento === 'bonifico')
                                    <span class="badge bg-info"><i class="fas fa-university me-1"></i> Bonifico Bancario</span>
                                @elseif($pagamento->metodo_pagamento === 'contante')
                                    <span class="badge bg-success"><i class="fas fa-money-bill me-1"></i> Contante</span>
                                @else
                                    <span class="badge bg-secondary"><i class="fas fa-file-invoice me-1"></i> Assegno</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Stato</label>
                            <p class="mb-0">{!! $pagamento->badge_stato !!}</p>
                        </div>
                    </div>

                    @if($pagamento->metodo_pagamento === 'bonifico')
                        <div class="row mb-3">
                            @if($pagamento->numero_bonifico)
                                <div class="col-md-6">
                                    <label class="text-muted small">Numero Bonifico</label>
                                    <p class="fw-bold mb-0">{{ $pagamento->numero_bonifico }}</p>
                                </div>
                            @endif
                            @if($pagamento->iban)
                                <div class="col-md-6">
                                    <label class="text-muted small">IBAN</label>
                                    <p class="fw-bold mb-0 font-monospace">{{ $pagamento->iban }}</p>
                                </div>
                            @endif
                        </div>
                    @endif

                    @if($pagamento->note)
                        <div class="mt-3 p-3 bg-light rounded">
                            <label class="text-muted small">Note</label>
                            <p class="mb-0">{{ $pagamento->note }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Lezioni del Periodo -->
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-chalkboard-teacher me-2"></i>
                        Lezioni del Periodo ({{ $lezioni->count() }})
                    </h5>
                </div>
                <div class="card-body">
                    @if($lezioni->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Data</th>
                                        <th>Programma</th>
                                        <th>Sede</th>
                                        <th>Durata</th>
                                        <th class="text-end">Compenso</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $tariffaOraria = $pagamento->professionista->professionista->tariffa_oraria ?? 0;
                                        $totaleDurata = 0;
                                        $totaleCompenso = 0;
                                    @endphp
                                    @foreach($lezioni as $lezione)
                                        @php
                                            $ore = ($lezione->durata_minuti ?? 60) / 60;
                                            $compenso = $ore * $tariffaOraria;
                                            $totaleDurata += $lezione->durata_minuti ?? 60;
                                            $totaleCompenso += $compenso;
                                        @endphp
                                        <tr>
                                            <td>
                                                <span class="badge bg-info">
                                                    {{ \Carbon\Carbon::parse($lezione->data)->format('d/m/Y') }}
                                                </span>
                                            </td>
                                            <td>{{ $lezione->programma->nome ?? '-' }}</td>
                                            <td>
                                                <small class="text-muted">{{ $lezione->sede->nome ?? '-' }}</small>
                                            </td>
                                            <td>{{ $lezione->durata_minuti ?? 60 }} min</td>
                                            <td class="text-end">€ {{ number_format($compenso, 2, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <th colspan="3">Totale</th>
                                        <th>{{ number_format($totaleDurata / 60, 1) }} ore</th>
                                        <th class="text-end">€ {{ number_format($totaleCompenso, 2, ',', '.') }}</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-info-circle fa-2x text-muted mb-3"></i>
                            <p class="text-muted mb-0">Nessuna lezione trovata per questo periodo</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar Importi -->
        <div class="col-lg-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-euro-sign me-2"></i>Riepilogo Importi</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3 pb-3 border-bottom">
                        <label class="text-muted small">Importo Maturato</label>
                        <h4 class="mb-0 text-primary">€ {{ number_format($pagamento->importo_maturato, 2, ',', '.') }}</h4>
                    </div>

                    <div class="mb-3 pb-3 border-bottom">
                        <label class="text-muted small">Ritenuta Fiscale (20%)</label>
                        <h5 class="mb-0 text-danger">- € {{ number_format($pagamento->ritenuta_fiscale, 2, ',', '.') }}</h5>
                    </div>

                    <div class="mb-0">
                        <label class="text-muted small">Importo Netto Pagato</label>
                        <h3 class="mb-0 text-success">€ {{ number_format($pagamento->importo_netto, 2, ',', '.') }}</h3>
                    </div>
                </div>
            </div>

            <!-- Info Amministrative -->
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-user-shield me-2"></i>Info Amministrative</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="text-muted small">Registrato da</label>
                        <p class="mb-0 fw-bold">{{ $pagamento->amministratore->nome_completo ?? 'N/D' }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted small">Data Registrazione</label>
                        <p class="mb-0">
                            <i class="fas fa-clock me-1"></i>
                            {{ $pagamento->created_at->format('d/m/Y H:i') }}
                        </p>
                    </div>
                    @if($pagamento->created_at != $pagamento->updated_at)
                        <div>
                            <label class="text-muted small">Ultima Modifica</label>
                            <p class="mb-0">
                                <i class="fas fa-clock me-1"></i>
                                {{ $pagamento->updated_at->format('d/m/Y H:i') }}
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
