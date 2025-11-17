@extends('layouts.admin')

@section('titolo', 'Super Admin - Pannello Controllo')

@section('contenuto')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">
            <i class="bi bi-shield-check text-danger"></i> Super Admin
        </h1>
        <div>
            <span class="badge bg-danger me-2">
                <i class="bi bi-clock"></i> Sessione attiva
            </span>
            <a href="{{ route('admin.super-admin.logout') }}" class="btn btn-sm btn-outline-danger">
                <i class="bi bi-box-arrow-right"></i> Logout
            </a>
        </div>
    </div>

    <!-- Info Sistema -->
    <div class="row mb-4">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-info-circle"></i> Informazioni Sistema</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center p-3 bg-light rounded">
                                <i class="bi bi-cpu fs-3 text-primary me-3"></i>
                                <div>
                                    <small class="text-muted">PHP Version</small>
                                    <div class="fw-bold">{{ $systemInfo['php_version'] }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center p-3 bg-light rounded">
                                <i class="bi bi-box fs-3 text-success me-3"></i>
                                <div>
                                    <small class="text-muted">Laravel Version</small>
                                    <div class="fw-bold">{{ $systemInfo['laravel_version'] }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center p-3 bg-light rounded">
                                <i class="bi bi-server fs-3 text-info me-3"></i>
                                <div>
                                    <small class="text-muted">Server</small>
                                    <div class="fw-bold">{{ $systemInfo['server_software'] }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center p-3 bg-light rounded">
                                <i class="bi bi-gear fs-3 text-warning me-3"></i>
                                <div>
                                    <small class="text-muted">Environment</small>
                                    <div class="fw-bold">{{ ucfirst($systemInfo['environment']) }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-3">

                    <div class="row g-2">
                        <div class="col-md-4">
                            <small class="text-muted">Memory Limit:</small>
                            <span class="fw-bold">{{ $systemInfo['memory_limit'] }}</span>
                        </div>
                        <div class="col-md-4">
                            <small class="text-muted">Max Upload:</small>
                            <span class="fw-bold">{{ $systemInfo['max_upload'] }}</span>
                        </div>
                        <div class="col-md-4">
                            <small class="text-muted">Max POST:</small>
                            <span class="fw-bold">{{ $systemInfo['max_post'] }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Debug Mode Control -->
        <div class="col-lg-4">
            <div class="card shadow-sm border-{{ $systemInfo['debug_mode'] ? 'danger' : 'success' }}">
                <div class="card-header bg-{{ $systemInfo['debug_mode'] ? 'danger' : 'success' }} text-white">
                    <h5 class="mb-0"><i class="bi bi-bug"></i> Debug Mode</h5>
                </div>
                <div class="card-body text-center">
                    <div class="mb-3">
                        <div class="fs-1">
                            @if($systemInfo['debug_mode'])
                                <i class="bi bi-toggle-on text-danger"></i>
                            @else
                                <i class="bi bi-toggle-off text-muted"></i>
                            @endif
                        </div>
                        <div class="mt-2">
                            <span class="badge bg-{{ $systemInfo['debug_mode'] ? 'danger' : 'success' }} fs-6">
                                {{ $systemInfo['debug_mode'] ? 'ABILITATO' : 'DISABILITATO' }}
                            </span>
                        </div>
                    </div>

                    @if($systemInfo['debug_mode'])
                        <div class="alert alert-warning py-2 mb-3">
                            <small><i class="bi bi-exclamation-triangle"></i> Disabilita in produzione!</small>
                        </div>
                    @endif

                    <button type="button" class="btn btn-{{ $systemInfo['debug_mode'] ? 'danger' : 'success' }} w-100" id="toggleDebugBtn">
                        <i class="bi bi-arrow-repeat"></i>
                        {{ $systemInfo['debug_mode'] ? 'Disabilita' : 'Abilita' }} Debug
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Funzioni Manutenzione -->
    <div class="row mb-4">
        <!-- Cache Management -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="bi bi-database"></i> Gestione Cache</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h6>Cache File Bootstrap:</h6>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($cacheInfo['files'] as $file => $exists)
                                <span class="badge bg-{{ $exists ? 'warning' : 'secondary' }}">
                                    {{ $file }}: {{ $exists ? 'Presente' : 'Assente' }}
                                </span>
                            @endforeach
                        </div>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted">Views in cache:</small>
                        <strong>{{ $cacheInfo['views_cached'] }}</strong> file
                    </div>

                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-warning" id="clearAllCacheBtn">
                            <i class="bi bi-trash"></i> Pulisci Tutta la Cache
                        </button>
                        <button type="button" class="btn btn-outline-warning" id="clearConfigCacheBtn">
                            <i class="bi bi-gear"></i> Pulisci Solo Cache Config
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Log Management -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="bi bi-file-text"></i> Gestione Log</h5>
                </div>
                <div class="card-body">
                    @if($logInfo['exists'])
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-muted">Dimensione:</span>
                                <span class="badge bg-info">{{ $logInfo['size_human'] }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted">Ultimo aggiornamento:</span>
                                <small>{{ $logInfo['modified_human'] }}</small>
                            </div>
                        </div>

                        @if($logInfo['size'] > 1048576)
                            <div class="alert alert-warning py-2">
                                <small><i class="bi bi-exclamation-triangle"></i> Log molto grande, considera di pulirlo</small>
                            </div>
                        @endif

                        <div class="d-grid gap-2">
                            <button type="button" class="btn btn-primary" id="viewLogsBtn">
                                <i class="bi bi-eye"></i> Visualizza Log
                            </button>
                            <button type="button" class="btn btn-danger" id="clearLogsBtn">
                                <i class="bi bi-trash"></i> Pulisci Log
                            </button>
                        </div>
                    @else
                        <div class="alert alert-info mb-0">
                            <i class="bi bi-info-circle"></i> Nessun file di log presente
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Database Tools -->
    <div class="row mb-4">
        <div class="col-lg-12">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="bi bi-hdd"></i> Strumenti Database</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <button type="button" class="btn btn-outline-success w-100" id="databaseInfoBtn">
                                <i class="bi bi-info-circle"></i> Info Database
                            </button>
                        </div>
                        <div class="col-md-3">
                            <button type="button" class="btn btn-outline-success w-100" id="optimizeDatabaseBtn">
                                <i class="bi bi-speedometer"></i> Ottimizza Database
                            </button>
                        </div>
                        <div class="col-md-3">
                            <button type="button" class="btn btn-outline-success w-100" id="runMigrationsBtn">
                                <i class="bi bi-arrow-repeat"></i> Esegui Migrations
                            </button>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('admin.impostazioni-sistema.index') }}" class="btn btn-outline-primary w-100">
                                <i class="bi bi-gear"></i> Impostazioni Sistema
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Log -->
    <div class="modal fade" id="logModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title"><i class="bi bi-file-text"></i> Laravel Log</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <pre id="logContent" style="background: #1e1e1e; color: #dcdcdc; padding: 15px; border-radius: 4px; max-height: 500px; overflow-y: auto;"></pre>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Database -->
    <div class="modal fade" id="databaseModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title"><i class="bi bi-hdd"></i> Informazioni Database</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="databaseContent">
                    <div class="text-center">
                        <div class="spinner-border"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="{{ asset('js/admin/super-admin.js') }}"></script>
@endpush
@endsection
