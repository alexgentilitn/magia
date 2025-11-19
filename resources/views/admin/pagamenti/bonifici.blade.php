@extends('layouts.admin')

@section('titolo', 'Verifica Bonifici Registrazione')

@section('contenuto')
<div class="container-fluid px-4">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Verifica Bonifici</h1>
            <p class="text-muted mb-0">Gestione bonifici per registrazioni clienti</p>
        </div>
        <div>
            <a href="{{ route('admin.pagamenti.index') }}" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left me-1"></i> Tutti i Pagamenti
            </a>
        </div>
    </div>

    <!-- Alert Success -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Alert Error -->
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Statistiche Rapide -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card bg-warning bg-opacity-10 border-warning">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-clock fa-2x text-warning"></i>
                        </div>
                        <div class="ms-3">
                            <h6 class="text-muted mb-1">Da Verificare</h6>
                            <h3 class="mb-0">{{ $bonificiDaVerificare->count() }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-success bg-opacity-10 border-success">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-check-circle fa-2x text-success"></i>
                        </div>
                        <div class="ms-3">
                            <h6 class="text-muted mb-1">Approvati Oggi</h6>
                            <h3 class="mb-0">{{ $bonificiVerificati->where('stato_pagamento', 'completato')->where('data_pagamento', '>=', now()->startOfDay())->count() }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-danger bg-opacity-10 border-danger">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-times-circle fa-2x text-danger"></i>
                        </div>
                        <div class="ms-3">
                            <h6 class="text-muted mb-1">Rifiutati</h6>
                            <h3 class="mb-0">{{ $bonificiVerificati->where('stato_pagamento', 'rifiutato')->count() }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bonifici Da Verificare -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-warning bg-opacity-10">
            <h5 class="mb-0">
                <i class="fas fa-hourglass-half me-2"></i>
                Bonifici in Attesa di Verifica ({{ $bonificiDaVerificare->count() }})
            </h5>
        </div>
        <div class="card-body">
            @if($bonificiDaVerificare->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Cliente</th>
                                <th>Email</th>
                                <th>Data Bonifico</th>
                                <th>Importo</th>
                                <th>Caricato il</th>
                                <th>Ricevuta</th>
                                <th>Azioni</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($bonificiDaVerificare as $utente)
                                <tr>
                                    <td>
                                        <div class="fw-bold">{{ $utente->nome_completo }}</div>
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $utente->email }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">
                                            {{ \Carbon\Carbon::parse($utente->data_bonifico)->format('d/m/Y') }}
                                        </span>
                                    </td>
                                    <td>
                                        <strong>€ {{ number_format($utente->importo_pagamento, 2, ',', '.') }}</strong>
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $utente->created_at->format('d/m/Y H:i') }}</small>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.pagamenti.bonifici.ricevuta', $utente->id) }}"
                                           class="btn btn-sm btn-outline-primary"
                                           target="_blank">
                                            <i class="fas fa-file-pdf"></i> Visualizza
                                        </a>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button type="button"
                                                    class="btn btn-sm btn-success"
                                                    onclick="approvaBonifico({{ $utente->id }}, '{{ $utente->nome_completo }}')">
                                                <i class="fas fa-check"></i> Approva
                                            </button>
                                            <button type="button"
                                                    class="btn btn-sm btn-danger"
                                                    onclick="rifiutaBonifico({{ $utente->id }}, '{{ $utente->nome_completo }}')">
                                                <i class="fas fa-times"></i> Rifiuta
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                    <p class="text-muted mb-0">Nessun bonifico da verificare al momento</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Bonifici Già Verificati -->
    <div class="card shadow-sm">
        <div class="card-header bg-light">
            <h5 class="mb-0">
                <i class="fas fa-history me-2"></i>
                Storico Verifiche (Ultimi 50)
            </h5>
        </div>
        <div class="card-body">
            @if($bonificiVerificati->count() > 0)
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Cliente</th>
                                <th>Email</th>
                                <th>Importo</th>
                                <th>Stato</th>
                                <th>Verificato il</th>
                                <th>Note</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($bonificiVerificati as $utente)
                                <tr>
                                    <td>{{ $utente->nome_completo }}</td>
                                    <td><small class="text-muted">{{ $utente->email }}</small></td>
                                    <td>€ {{ number_format($utente->importo_pagamento, 2, ',', '.') }}</td>
                                    <td>
                                        @if($utente->stato_pagamento === 'completato')
                                            <span class="badge bg-success">Approvato</span>
                                        @else
                                            <span class="badge bg-danger">Rifiutato</span>
                                        @endif
                                    </td>
                                    <td>
                                        <small>{{ $utente->data_pagamento ? $utente->data_pagamento->format('d/m/Y H:i') : '-' }}</small>
                                    </td>
                                    <td>
                                        @if($utente->note_verifica_bonifico)
                                            <small class="text-muted">{{ Str::limit($utente->note_verifica_bonifico, 50) }}</small>
                                        @else
                                            <small class="text-muted">-</small>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-4">
                    <p class="text-muted mb-0">Nessuna verifica effettuata</p>
                </div>
            @endif
        </div>
    </div>

</div>

<!-- Modal Approva Bonifico -->
<div class="modal fade" id="modalApprova" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="fas fa-check-circle me-2"></i>
                    Approva Bonifico
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="formApprova" method="POST">
                @csrf
                <div class="modal-body">
                    <p>Sei sicuro di voler <strong>approvare</strong> il bonifico per:</p>
                    <p class="fw-bold fs-5" id="nomeClienteApprova"></p>
                    <p class="text-muted">L'account del cliente verrà attivato immediatamente e riceverà un'email con le credenziali di accesso.</p>

                    <div class="mb-3">
                        <label for="noteApprova" class="form-label">Note (facoltativo)</label>
                        <textarea class="form-control" id="noteApprova" name="note" rows="2" placeholder="Aggiungi eventuali note..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check me-1"></i> Conferma Approvazione
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Rifiuta Bonifico -->
<div class="modal fade" id="modalRifiuta" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-times-circle me-2"></i>
                    Rifiuta Bonifico
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="formRifiuta" method="POST">
                @csrf
                <div class="modal-body">
                    <p>Sei sicuro di voler <strong>rifiutare</strong> il bonifico per:</p>
                    <p class="fw-bold fs-5" id="nomeClienteRifiuta"></p>
                    <p class="text-muted">Il cliente riceverà una notifica via email con il motivo del rifiuto.</p>

                    <div class="mb-3">
                        <label for="motivoRifiuto" class="form-label">Motivo del rifiuto *</label>
                        <textarea class="form-control" id="motivoRifiuto" name="motivo" rows="3" required placeholder="Es: Importo non corretto, causale errata, ricevuta non leggibile..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-times me-1"></i> Conferma Rifiuto
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function approvaBonifico(utenteId, nomeCliente) {
    document.getElementById('nomeClienteApprova').textContent = nomeCliente;
    document.getElementById('formApprova').action = '{{ route("admin.pagamenti.bonifici.approva", ":id") }}'.replace(':id', utenteId);
    new bootstrap.Modal(document.getElementById('modalApprova')).show();
}

function rifiutaBonifico(utenteId, nomeCliente) {
    document.getElementById('nomeClienteRifiuta').textContent = nomeCliente;
    document.getElementById('formRifiuta').action = '{{ route("admin.pagamenti.bonifici.rifiuta", ":id") }}'.replace(':id', utenteId);
    new bootstrap.Modal(document.getElementById('modalRifiuta')).show();
}
</script>
@endsection
