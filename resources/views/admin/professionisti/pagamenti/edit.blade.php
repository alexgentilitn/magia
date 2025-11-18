@extends('layouts.admin')

@section('titolo', 'Modifica Pagamento Professionista')

@section('contenuto')
<div class="container-fluid px-4">

    <!-- Header -->
    <div class="mb-4">
        <h1 class="h3 mb-0">Modifica Pagamento</h1>
        <p class="text-muted mb-0">{{ $pagamento->professionista->nome_completo }} - {{ $pagamento->descrizione_periodo }}</p>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    <form action="{{ route('admin.professionisti.pagamenti.update', $pagamento->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Alert Info -->
                        <div class="alert alert-info mb-4">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Nota:</strong> Gli importi del pagamento non possono essere modificati.
                            Puoi aggiornare solo la data di pagamento, i dettagli bancari, le note e lo stato.
                        </div>

                        <!-- Riepilogo Importi (Read-only) -->
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label class="form-label text-muted">Importo Maturato</label>
                                <div class="input-group">
                                    <span class="input-group-text">€</span>
                                    <input type="text" class="form-control"
                                           value="{{ number_format($pagamento->importo_maturato, 2, ',', '.') }}"
                                           disabled>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label text-muted">Ritenuta 20%</label>
                                <div class="input-group">
                                    <span class="input-group-text">€</span>
                                    <input type="text" class="form-control"
                                           value="{{ number_format($pagamento->ritenuta_fiscale, 2, ',', '.') }}"
                                           disabled>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label text-muted">Importo Netto</label>
                                <div class="input-group">
                                    <span class="input-group-text">€</span>
                                    <input type="text" class="form-control"
                                           value="{{ number_format($pagamento->importo_netto, 2, ',', '.') }}"
                                           disabled>
                                </div>
                            </div>
                        </div>

                        <!-- Data Pagamento -->
                        <div class="mb-4">
                            <label for="data_pagamento" class="form-label">Data Pagamento *</label>
                            <input type="date"
                                   name="data_pagamento"
                                   id="data_pagamento"
                                   class="form-control @error('data_pagamento') is-invalid @enderror"
                                   value="{{ old('data_pagamento', $pagamento->data_pagamento->format('Y-m-d')) }}"
                                   required>
                            @error('data_pagamento')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Stato -->
                        <div class="mb-4">
                            <label for="stato" class="form-label">Stato Pagamento *</label>
                            <select name="stato"
                                    id="stato"
                                    class="form-select @error('stato') is-invalid @enderror"
                                    required>
                                <option value="completato" {{ old('stato', $pagamento->stato) === 'completato' ? 'selected' : '' }}>
                                    Completato
                                </option>
                                <option value="in_attesa" {{ old('stato', $pagamento->stato) === 'in_attesa' ? 'selected' : '' }}>
                                    In Attesa
                                </option>
                                <option value="annullato" {{ old('stato', $pagamento->stato) === 'annullato' ? 'selected' : '' }}>
                                    Annullato
                                </option>
                            </select>
                            @error('stato')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Dettagli Bonifico (se bonifico) -->
                        @if($pagamento->metodo_pagamento === 'bonifico')
                            <div class="mb-4">
                                <h6 class="mb-3"><i class="fas fa-university me-2"></i>Dettagli Bonifico</h6>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="numero_bonifico" class="form-label">Numero Bonifico</label>
                                        <input type="text"
                                               name="numero_bonifico"
                                               id="numero_bonifico"
                                               class="form-control @error('numero_bonifico') is-invalid @enderror"
                                               value="{{ old('numero_bonifico', $pagamento->numero_bonifico) }}"
                                               placeholder="Es: BN123456">
                                        @error('numero_bonifico')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="iban" class="form-label">IBAN</label>
                                        <input type="text"
                                               name="iban"
                                               id="iban"
                                               class="form-control @error('iban') is-invalid @enderror"
                                               value="{{ old('iban', $pagamento->iban) }}"
                                               placeholder="IT00A0000000000000000000000">
                                        @error('iban')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Note -->
                        <div class="mb-4">
                            <label for="note" class="form-label">Note</label>
                            <textarea name="note"
                                      id="note"
                                      class="form-control @error('note') is-invalid @enderror"
                                      rows="4"
                                      placeholder="Aggiungi eventuali note o dettagli sul pagamento...">{{ old('note', $pagamento->note) }}</textarea>
                            @error('note')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Buttons -->
                        <div class="d-flex justify-content-between pt-3 border-top">
                            <a href="{{ route('admin.professionisti.pagamenti.show', $pagamento->id) }}" class="btn btn-secondary">
                                <i class="fas fa-times me-1"></i> Annulla
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Salva Modifiche
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>

        <!-- Sidebar Info -->
        <div class="col-lg-4">
            <div class="card shadow-sm bg-light">
                <div class="card-body">
                    <h6 class="card-title mb-3"><i class="fas fa-info-circle me-2"></i>Informazioni</h6>

                    <div class="mb-3">
                        <label class="text-muted small">Professionista</label>
                        <p class="mb-0 fw-bold">{{ $pagamento->professionista->nome_completo }}</p>
                    </div>

                    <div class="mb-3">
                        <label class="text-muted small">Periodo</label>
                        <p class="mb-0">{{ $pagamento->descrizione_periodo }}</p>
                    </div>

                    <div class="mb-3">
                        <label class="text-muted small">Metodo Pagamento</label>
                        <p class="mb-0">
                            @if($pagamento->metodo_pagamento === 'bonifico')
                                <span class="badge bg-info">Bonifico Bancario</span>
                            @elseif($pagamento->metodo_pagamento === 'contante')
                                <span class="badge bg-success">Contante</span>
                            @else
                                <span class="badge bg-secondary">Assegno</span>
                            @endif
                        </p>
                    </div>

                    <hr>

                    <div class="mb-3">
                        <label class="text-muted small">Registrato da</label>
                        <p class="mb-0">{{ $pagamento->amministratore->nome_completo ?? 'N/D' }}</p>
                    </div>

                    <div>
                        <label class="text-muted small">Data Registrazione</label>
                        <p class="mb-0">{{ $pagamento->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
            </div>

            <!-- Azioni Aggiuntive -->
            <div class="card shadow-sm mt-3 border-danger">
                <div class="card-body">
                    <h6 class="card-title text-danger mb-3">
                        <i class="fas fa-exclamation-triangle me-2"></i>Zona Pericolo
                    </h6>
                    <p class="text-muted small mb-3">
                        L'eliminazione del pagamento è irreversibile e non influisce sullo stato delle lezioni.
                    </p>
                    <form action="{{ route('admin.professionisti.pagamenti.destroy', $pagamento->id) }}"
                          method="POST"
                          onsubmit="return confirm('Sei sicuro di voler eliminare questo pagamento? L\'operazione è irreversibile.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm w-100">
                            <i class="fas fa-trash me-1"></i> Elimina Pagamento
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
