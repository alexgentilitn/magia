@extends('layouts.admin')

@section('titolo', 'Pagamenti Professionisti')

@section('contenuto')
<div class="container-fluid px-4">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Pagamenti Professionisti</h1>
            <p class="text-muted mb-0">Gestione compensi e storico pagamenti</p>
        </div>
        <div>
            <a href="{{ route('admin.professionisti.pagamenti.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> Nuovo Pagamento
            </a>
        </div>
    </div>

    <!-- Alert -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Statistiche -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card bg-success bg-opacity-10 border-success">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-euro-sign fa-2x text-success"></i>
                        </div>
                        <div class="ms-3">
                            <h6 class="text-muted mb-1">Totale Pagato</h6>
                            <h3 class="mb-0">€ {{ number_format($totaleCompletati, 2, ',', '.') }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-warning bg-opacity-10 border-warning">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-hourglass-half fa-2x text-warning"></i>
                        </div>
                        <div class="ms-3">
                            <h6 class="text-muted mb-1">In Attesa</h6>
                            <h3 class="mb-0">€ {{ number_format($totaleInAttesa, 2, ',', '.') }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-info bg-opacity-10 border-info">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-calendar-check fa-2x text-info"></i>
                        </div>
                        <div class="ms-3">
                            <h6 class="text-muted mb-1">Questo Mese</h6>
                            <h3 class="mb-0">{{ $pagamentiMese }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabella Pagamenti -->
    <div class="card shadow-sm">
        <div class="card-header bg-light">
            <h5 class="mb-0">
                <i class="fas fa-list me-2"></i>
                Storico Pagamenti
            </h5>
        </div>
        <div class="card-body">
            @if($pagamenti->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Professionista</th>
                                <th>Periodo</th>
                                <th>Maturato</th>
                                <th>Ritenuta</th>
                                <th>Pagato</th>
                                <th>Data Pag.</th>
                                <th>Metodo</th>
                                <th>Stato</th>
                                <th>Azioni</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pagamenti as $pagamento)
                                <tr>
                                    <td>
                                        <div class="fw-bold">{{ $pagamento->professionista->nome_completo }}</div>
                                        <small class="text-muted">{{ $pagamento->professionista->email }}</small>
                                    </td>
                                    <td>
                                        <small>{{ $pagamento->periodo_da->format('d/m/Y') }} <br> {{ $pagamento->periodo_a->format('d/m/Y') }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">€ {{ number_format($pagamento->importo_maturato, 2, ',', '.') }}</span>
                                    </td>
                                    <td>
                                        <span class="text-danger">€ {{ number_format($pagamento->ritenuta_fiscale, 2, ',', '.') }}</span>
                                    </td>
                                    <td>
                                        <strong class="text-success">€ {{ number_format($pagamento->importo_netto, 2, ',', '.') }}</strong>
                                    </td>
                                    <td>
                                        <small>{{ $pagamento->data_pagamento->format('d/m/Y') }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">{{ ucfirst($pagamento->metodo_pagamento) }}</span>
                                    </td>
                                    <td>{!! $pagamento->badge_stato !!}</td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
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
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-3">
                    {{ $pagamenti->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <p class="text-muted mb-0">Nessun pagamento registrato</p>
                    <a href="{{ route('admin.professionisti.pagamenti.create') }}" class="btn btn-primary mt-3">
                        <i class="fas fa-plus me-1"></i> Crea Primo Pagamento
                    </a>
                </div>
            @endif
        </div>
    </div>

</div>
@endsection
