@extends('layouts.admin')

@section('titolo', 'Storico Pagamenti Professionista')

@section('contenuto')
<div class="container-fluid px-4">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Storico Pagamenti</h1>
            <p class="text-muted mb-0">{{ $utente->nome_completo }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.professionisti.pagamenti.create') }}?utente_id={{ $utente->id }}" class="btn btn-success">
                <i class="fas fa-plus me-1"></i> Nuovo Pagamento
            </a>
            <a href="{{ route('admin.professionisti.pagamenti.index') }}" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left me-1"></i> Tutti i Pagamenti
            </a>
        </div>
    </div>

    <!-- Statistiche Professionista -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm bg-success bg-opacity-10 border-success">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-euro-sign fa-2x text-success"></i>
                        </div>
                        <div class="ms-3">
                            <h6 class="text-muted mb-1">Totale Versato</h6>
                            <h3 class="mb-0">€ {{ number_format($totaleVersato, 2, ',', '.') }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm bg-info bg-opacity-10 border-info">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-receipt fa-2x text-info"></i>
                        </div>
                        <div class="ms-3">
                            <h6 class="text-muted mb-1">Numero Pagamenti</h6>
                            <h3 class="mb-0">{{ $pagamenti->where('stato', 'completato')->count() }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm bg-primary bg-opacity-10 border-primary">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-coins fa-2x text-primary"></i>
                        </div>
                        <div class="ms-3">
                            <h6 class="text-muted mb-1">Tariffa Oraria</h6>
                            <h3 class="mb-0">€ {{ number_format($utente->professionista->tariffa_oraria ?? 0, 2, ',', '.') }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm bg-warning bg-opacity-10 border-warning">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-clock fa-2x text-warning"></i>
                        </div>
                        <div class="ms-3">
                            <h6 class="text-muted mb-1">In Attesa</h6>
                            <h3 class="mb-0">{{ $pagamenti->where('stato', 'in_attesa')->count() }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabella Storico -->
    <div class="card shadow-sm">
        <div class="card-header bg-light">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-history me-2"></i>
                    Storico Pagamenti ({{ $pagamenti->count() }})
                </h5>
            </div>
        </div>
        <div class="card-body">
            @if($pagamenti->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Periodo</th>
                                <th>Data Pagamento</th>
                                <th>Metodo</th>
                                <th class="text-end">Maturato</th>
                                <th class="text-end">Ritenuta</th>
                                <th class="text-end">Netto Pagato</th>
                                <th>Stato</th>
                                <th class="text-center">Azioni</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pagamenti as $pagamento)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-calendar-alt text-muted me-2"></i>
                                            <div>
                                                <div class="fw-bold">{{ $pagamento->periodo_da->format('d/m/Y') }}</div>
                                                <small class="text-muted">{{ $pagamento->periodo_a->format('d/m/Y') }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">
                                            {{ $pagamento->data_pagamento->format('d/m/Y') }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($pagamento->metodo_pagamento === 'bonifico')
                                            <i class="fas fa-university text-info me-1"></i> Bonifico
                                        @elseif($pagamento->metodo_pagamento === 'contante')
                                            <i class="fas fa-money-bill text-success me-1"></i> Contante
                                        @else
                                            <i class="fas fa-file-invoice text-secondary me-1"></i> Assegno
                                        @endif
                                    </td>
                                    <td class="text-end">€ {{ number_format($pagamento->importo_maturato, 2, ',', '.') }}</td>
                                    <td class="text-end text-danger">€ {{ number_format($pagamento->ritenuta_fiscale, 2, ',', '.') }}</td>
                                    <td class="text-end">
                                        <strong class="text-success">€ {{ number_format($pagamento->importo_netto, 2, ',', '.') }}</strong>
                                    </td>
                                    <td>{!! $pagamento->badge_stato !!}</td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('admin.professionisti.pagamenti.show', $pagamento->id) }}"
                                               class="btn btn-outline-primary"
                                               title="Visualizza">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.professionisti.pagamenti.edit', $pagamento->id) }}"
                                               class="btn btn-outline-warning"
                                               title="Modifica">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <th colspan="3" class="text-end">Totali:</th>
                                <th class="text-end">€ {{ number_format($pagamenti->sum('importo_maturato'), 2, ',', '.') }}</th>
                                <th class="text-end text-danger">€ {{ number_format($pagamenti->sum('ritenuta_fiscale'), 2, ',', '.') }}</th>
                                <th class="text-end text-success">€ {{ number_format($pagamenti->sum('importo_netto'), 2, ',', '.') }}</th>
                                <th colspan="2"></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- Timeline Grafica (Opzionale) -->
                <div class="mt-4">
                    <h6 class="mb-3"><i class="fas fa-chart-line me-2"></i>Andamento Pagamenti</h6>
                    <div class="row">
                        @php
                            $maxImporto = $pagamenti->max('importo_netto') ?: 1;
                        @endphp
                        @foreach($pagamenti->take(12) as $pag)
                            <div class="col-md-1 text-center mb-3">
                                <div class="position-relative" style="height: 100px;">
                                    <div class="position-absolute bottom-0 w-100 bg-success rounded"
                                         style="height: {{ ($pag->importo_netto / $maxImporto) * 100 }}%;"
                                         title="€ {{ number_format($pag->importo_netto, 2) }}">
                                    </div>
                                </div>
                                <small class="text-muted">{{ $pag->data_pagamento->format('m/y') }}</small>
                            </div>
                        @endforeach
                    </div>
                </div>

            @else
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Nessun Pagamento Registrato</h5>
                    <p class="text-muted mb-4">
                        Non ci sono ancora pagamenti registrati per questo professionista.
                    </p>
                    <a href="{{ route('admin.professionisti.pagamenti.create') }}?utente_id={{ $utente->id }}"
                       class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i> Registra Primo Pagamento
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Info Professionista -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card shadow-sm bg-light">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <label class="text-muted small">Nome Completo</label>
                            <p class="mb-0 fw-bold">{{ $utente->nome_completo }}</p>
                        </div>
                        <div class="col-md-3">
                            <label class="text-muted small">Email</label>
                            <p class="mb-0">{{ $utente->email }}</p>
                        </div>
                        <div class="col-md-3">
                            <label class="text-muted small">Tariffa Oraria</label>
                            <p class="mb-0">€ {{ number_format($utente->professionista->tariffa_oraria ?? 0, 2, ',', '.') }}/h</p>
                        </div>
                        <div class="col-md-3">
                            <label class="text-muted small">Azioni</label>
                            <p class="mb-0">
                                <a href="{{ route('admin.professionisti.show', $utente->id) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-user me-1"></i> Vedi Profilo
                                </a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
