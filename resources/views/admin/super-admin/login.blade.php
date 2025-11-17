@extends('layouts.admin')

@section('titolo', 'Super Admin - Autenticazione')

@section('contenuto')
<div class="container-fluid">
    <div class="row justify-content-center align-items-center" style="min-height: 70vh;">
        <div class="col-md-6 col-lg-4">
            <div class="card shadow">
                <div class="card-header bg-danger text-white text-center">
                    <h4 class="mb-0">
                        <i class="bi bi-shield-lock"></i> Super Admin
                    </h4>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle"></i>
                        <strong>Attenzione:</strong> Stai per accedere a funzionalità avanzate di sistema.
                        L'accesso è riservato al Super Admin.
                    </div>

                    <form method="POST" id="superAdminLoginForm">
                        @csrf
                        <div class="mb-3">
                            <label for="super_admin_password" class="form-label">Password Super Admin</label>
                            <input 
                                type="password" 
                                class="form-control @error('super_admin_password') is-invalid @enderror" 
                                id="super_admin_password" 
                                name="super_admin_password" 
                                required 
                                autofocus
                            >
                            @error('super_admin_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-danger">
                                <i class="bi bi-key"></i> Accedi
                            </button>
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left"></i> Annulla
                            </a>
                        </div>
                    </form>
                </div>
                <div class="card-footer text-muted text-center">
                    <small>
                        <i class="bi bi-info-circle"></i> 
                        La sessione scade dopo 30 minuti di inattività
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
