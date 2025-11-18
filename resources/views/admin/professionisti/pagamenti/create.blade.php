@extends('layouts.admin')

@section('titolo', 'Nuovo Pagamento Professionista')

@section('contenuto')
<div class="container-fluid px-4">

    <!-- Header -->
    <div class="mb-4">
        <h1 class="h3 mb-0">Nuovo Pagamento Professionista</h1>
        <p class="text-muted mb-0">Registra un pagamento per un professionista</p>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    <form action="{{ route('admin.professionisti.pagamenti.store') }}" method="POST" id="formPagamento">
                        @csrf

                        <!-- Professionista -->
                        <div class="mb-4">
                            <label for="utente_id" class="form-label">Professionista *</label>
                            <select name="utente_id" id="utente_id" class="form-select" required>
                                <option value="">Seleziona professionista...</option>
                                @foreach($professionisti as $prof)
                                    <option value="{{ $prof->id }}" data-tariffa="{{ $prof->professionista->tariffa_oraria ?? 0 }}">
                                        {{ $prof->nome_completo }} - Tariffa: €{{ number_format($prof->professionista->tariffa_oraria ?? 0, 2) }}/h
                                    </option>
                                @endforeach
                            </select>
                            @error('utente_id')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Periodo -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="periodo_da" class="form-label">Periodo Da *</label>
                                <input type="date" name="periodo_da" id="periodo_da" class="form-control" required>
                                @error('periodo_da')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="periodo_a" class="form-label">Periodo A *</label>
                                <input type="date" name="periodo_a" id="periodo_a" class="form-control" required>
                                @error('periodo_a')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Bottone Calcola -->
                        <div class="mb-4">
                            <button type="button" id="btnCalcola" class="btn btn-info" disabled>
                                <i class="fas fa-calculator me-1"></i> Calcola Compenso
                            </button>
                            <small class="text-muted ms-2">Seleziona professionista e periodo</small>
                        </div>

                        <div id="risultatiCalcolo" class="d-none">
                            <!-- Importi -->
                            <div class="row mb-4">
                                <div class="col-md-4">
                                    <label for="importo_maturato" class="form-label">Importo Maturato *</label>
                                    <div class="input-group">
                                        <span class="input-group-text">€</span>
                                        <input type="number" name="importo_maturato" id="importo_maturato"
                                               class="form-control" step="0.01" min="0" required readonly>
                                    </div>
                                    @error('importo_maturato')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label for="ritenuta_fiscale" class="form-label">Ritenuta 20% *</label>
                                    <div class="input-group">
                                        <span class="input-group-text">€</span>
                                        <input type="number" name="ritenuta_fiscale" id="ritenuta_fiscale"
                                               class="form-control" step="0.01" min="0" required readonly>
                                    </div>
                                    @error('ritenuta_fiscale')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label for="importo_netto" class="form-label">Importo Netto *</label>
                                    <div class="input-group">
                                        <span class="input-group-text">€</span>
                                        <input type="number" name="importo_netto" id="importo_netto"
                                               class="form-control" step="0.01" min="0" required readonly>
                                    </div>
                                    @error('importo_netto')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Info Lezioni -->
                            <div class="alert alert-info mb-4">
                                <h6 class="alert-heading"><i class="fas fa-info-circle me-1"></i> Riepilogo Lezioni</h6>
                                <p class="mb-0">
                                    <strong id="numLezioni">0</strong> lezioni completate |
                                    <strong id="oreTotali">0</strong> ore totali
                                </p>
                            </div>

                            <!-- Metodo Pagamento -->
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label for="metodo_pagamento" class="form-label">Metodo Pagamento *</label>
                                    <select name="metodo_pagamento" id="metodo_pagamento" class="form-select" required>
                                        <option value="">Seleziona metodo...</option>
                                        <option value="bonifico">Bonifico Bancario</option>
                                        <option value="contante">Contante</option>
                                        <option value="assegno">Assegno</option>
                                    </select>
                                    @error('metodo_pagamento')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="data_pagamento" class="form-label">Data Pagamento *</label>
                                    <input type="date" name="data_pagamento" id="data_pagamento"
                                           class="form-control" value="{{ date('Y-m-d') }}" required>
                                    @error('data_pagamento')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Dettagli Bonifico -->
                            <div id="dettagliBonifico" class="d-none">
                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <label for="numero_bonifico" class="form-label">Numero Bonifico</label>
                                        <input type="text" name="numero_bonifico" id="numero_bonifico" class="form-control">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="iban" class="form-label">IBAN</label>
                                        <input type="text" name="iban" id="iban" class="form-control"
                                               placeholder="IT00A0000000000000000000000">
                                    </div>
                                </div>
                            </div>

                            <!-- Note -->
                            <div class="mb-4">
                                <label for="note" class="form-label">Note</label>
                                <textarea name="note" id="note" class="form-control" rows="3"
                                          placeholder="Note aggiuntive sul pagamento..."></textarea>
                            </div>

                            <!-- Buttons -->
                            <div class="d-flex justify-content-between pt-3 border-top">
                                <a href="{{ route('admin.professionisti.pagamenti.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times me-1"></i> Annulla
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i> Registra Pagamento
                                </button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm bg-light">
                <div class="card-body">
                    <h5 class="card-title"><i class="fas fa-lightbulb me-2"></i>Istruzioni</h5>
                    <ol class="mb-0">
                        <li class="mb-2">Seleziona il professionista</li>
                        <li class="mb-2">Imposta il periodo di riferimento</li>
                        <li class="mb-2">Clicca "Calcola Compenso" per ottenere l'importo automaticamente</li>
                        <li class="mb-2">Compila i dettagli del pagamento</li>
                        <li>Registra il pagamento</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const utenteSelect = document.getElementById('utente_id');
    const periodoDa = document.getElementById('periodo_da');
    const periodoA = document.getElementById('periodo_a');
    const btnCalcola = document.getElementById('btnCalcola');
    const risultatiDiv = document.getElementById('risultatiCalcolo');
    const metodoPagamento = document.getElementById('metodo_pagamento');
    const dettagliBonifico = document.getElementById('dettagliBonifico');

    // Abilita bottone calcola
    function checkCalcolaEnabled() {
        if (utenteSelect.value && periodoDa.value && periodoA.value) {
            btnCalcola.disabled = false;
        } else {
            btnCalcola.disabled = true;
        }
    }

    utenteSelect.addEventListener('change', checkCalcolaEnabled);
    periodoDa.addEventListener('change', checkCalcolaEnabled);
    periodoA.addEventListener('change', checkCalcolaEnabled);

    // Mostra/nascondi dettagli bonifico
    metodoPagamento.addEventListener('change', function() {
        if (this.value === 'bonifico') {
            dettagliBonifico.classList.remove('d-none');
        } else {
            dettagliBonifico.classList.add('d-none');
        }
    });

    // Calcola compenso
    btnCalcola.addEventListener('click', async function() {
        const btn = this;
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Calcolo...';

        try {
            const response = await fetch('{{ route("admin.professionisti.pagamenti.calcola") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    utente_id: utenteSelect.value,
                    periodo_da: periodoDa.value,
                    periodo_a: periodoA.value
                })
            });

            const data = await response.json();

            document.getElementById('importo_maturato').value = data.compenso_maturato;
            document.getElementById('ritenuta_fiscale').value = data.ritenuta_fiscale;
            document.getElementById('importo_netto').value = data.importo_netto;
            document.getElementById('numLezioni').textContent = data.numero_lezioni;
            document.getElementById('oreTotali').textContent = data.ore_totali.toFixed(1);

            risultatiDiv.classList.remove('d-none');

        } catch (error) {
            alert('Errore nel calcolo del compenso');
            console.error(error);
        } finally {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-calculator me-1"></i> Calcola Compenso';
        }
    });
});
</script>
@endsection
