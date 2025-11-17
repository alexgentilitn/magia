@extends('layouts.admin')

@section('titolo', 'Gestione Permessi - ' . $utente->nome_completo)

@section('contenuto')
<div class="container-fluid px-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Gestione Permessi Collaboratore</h1>
            <p class="text-muted mb-0">
                <i class="fas fa-user me-1"></i> {{ $utente->nome_completo }}
                <span class="badge bg-info ms-2">{{ $utente->email }}</span>
                @if($utente->ruolo)
                    <span class="badge bg-secondary ms-2">Ruolo: {{ $utente->ruolo->nome }}</span>
                @endif
            </p>
        </div>
        <div>
            <a href="{{ route('admin.professionisti.show', $utente->professionista->id) }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Torna al Profilo
            </a>
            <a href="{{ route('admin.professionisti.index') }}" class="btn btn-outline-primary">
                <i class="fas fa-users me-1"></i> Lista Professionisti
            </a>
        </div>
    </div>

    <!-- Alert messaggi -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Info Box -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="alert alert-info">
                <h5 class="alert-heading"><i class="fas fa-info-circle me-2"></i>Come funzionano i permessi individuali</h5>
                <p class="mb-2">I permessi di un collaboratore si ottengono combinando:</p>
                <ul class="mb-2">
                    <li><strong>Permessi del Ruolo</strong>: Assegnati automaticamente in base al ruolo ({{ $utente->ruolo->nome ?? 'Nessun ruolo' }})</li>
                    <li><strong>Permessi Individuali</strong>: Assegnati specificamente a questo utente (priorità massima)</li>
                </ul>
                <p class="mb-0"><i class="fas fa-lightbulb text-warning me-1"></i> <strong>Suggerimento:</strong> Usa i permessi individuali per dare accesso a funzionalità specifiche non previste dal ruolo base.</p>
            </div>
        </div>
    </div>

    <!-- Form Gestione Permessi -->
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-shield-alt me-2"></i>Seleziona Permessi Individuali</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.professionisti.permessi.update', $utente->id) }}" method="POST">
                @csrf
                @method('PUT')

                @foreach($permessiDisponibili as $categoria => $permessi)
                    <div class="mb-4">
                        <h5 class="border-bottom pb-2 mb-3">
                            <i class="fas {{ $permessi->first()->icona_categoria ?? 'fa-circle' }} me-2"></i>
                            {{ $permessi->first()->categoria_formattata ?? ucfirst($categoria) }}
                        </h5>

                        <div class="row">
                            @foreach($permessi as $permesso)
                                <div class="col-md-6 mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input"
                                               type="checkbox"
                                               name="permessi[]"
                                               value="{{ $permesso->id }}"
                                               id="permesso_{{ $permesso->id }}"
                                               {{ in_array($permesso->id, $permessiAssegnati) ? 'checked' : '' }}
                                               {{ in_array($permesso->id, $permessiRuolo) ? 'data-from-role="true"' : '' }}>
                                        <label class="form-check-label" for="permesso_{{ $permesso->id }}">
                                            <strong>{{ $permesso->nome }}</strong>
                                            @if(in_array($permesso->id, $permessiRuolo))
                                                <span class="badge bg-secondary ms-2" title="Permesso già incluso nel ruolo">
                                                    <i class="fas fa-user-tag me-1"></i>Dal Ruolo
                                                </span>
                                            @endif
                                            <br>
                                            <small class="text-muted">{{ $permesso->descrizione }}</small>
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach

                <!-- Pulsanti Azione -->
                <div class="d-flex justify-content-between align-items-center border-top pt-3 mt-4">
                    <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#resetModal">
                        <i class="fas fa-undo me-1"></i> Resetta Permessi Individuali
                    </button>
                    <div>
                        <a href="{{ route('admin.professionisti.show', $utente->professionista->id) }}" class="btn btn-secondary me-2">
                            <i class="fas fa-times me-1"></i> Annulla
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Salva Permessi
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Statistiche Permessi -->
    <div class="row mt-4">
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <h6 class="text-muted mb-2">Permessi dal Ruolo</h6>
                    <h2 class="mb-0">{{ count($permessiRuolo) }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <h6 class="text-muted mb-2">Permessi Individuali</h6>
                    <h2 class="mb-0 text-primary">{{ count($permessiAssegnati) }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <h6 class="text-muted mb-2">Totale Permessi</h6>
                    <h2 class="mb-0 text-success">{{ count(array_unique(array_merge($permessiRuolo, $permessiAssegnati))) }}</h2>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Conferma Reset -->
<div class="modal fade" id="resetModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="fas fa-exclamation-triangle me-2"></i>Conferma Reset</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Sei sicuro di voler <strong>rimuovere tutti i permessi individuali</strong> per <strong>{{ $utente->nome_completo }}</strong>?</p>
                <p class="mb-0 text-muted">L'utente manterrà solo i permessi del suo ruolo ({{ $utente->ruolo->nome ?? 'Nessun ruolo' }}).</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
                <form action="{{ route('admin.professionisti.permessi.reset', $utente->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-undo me-1"></i> Conferma Reset
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.form-check-input[data-from-role="true"] {
    opacity: 0.5;
}

.form-check-label strong {
    font-weight: 600;
}

.card {
    border: none;
    border-radius: 8px;
}

.badge {
    font-size: 0.75rem;
}
</style>
@endsection
