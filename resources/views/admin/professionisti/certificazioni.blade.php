@extends('layouts.admin')

@section('titolo', 'Gestione Certificazioni')

@section('contenuto')
<div class="p-6">
    <div class="mb-6">
        <a href="{{ route('admin.professionisti.show', $professionista->id) }}" class="text-fucsia-magia hover:text-viola-magia mb-2 inline-block">
            <i class="fas fa-arrow-left mr-2"></i> Torna al dettaglio
        </a>
        <h1 class="text-3xl font-bold text-gray-800">Certificazioni: {{ $professionista->nome }} {{ $professionista->cognome }}</h1>
        <p class="text-gray-600 mt-1">Gestisci certificazioni, qualifiche e titoli professionali</p>
    </div>

    <form method="POST" action="{{ route('admin.professionisti.salva-certificazioni', $professionista->id) }}" class="bg-white rounded-lg shadow p-6">
        @csrf

        <div id="certificazioni-container" class="space-y-4">
            @if($professionista->certificazioni && count($professionista->certificazioni) > 0)
                @foreach($professionista->certificazioni as $index => $cert)
                <div class="certificazione-item border rounded-lg p-4 bg-gray-50">
                    <div class="flex justify-between items-start mb-3">
                        <h3 class="font-semibold text-gray-800">Certificazione #{{ $index + 1 }}</h3>
                        <button type="button" onclick="rimuoviCertificazione(this)" class="text-red-600 hover:text-red-800">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nome Certificazione *</label>
                            <input type="text" name="certificazione_nome[]" value="{{ $cert['nome'] ?? '' }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent" placeholder="Es: Personal Trainer CONI">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Ente Certificatore</label>
                            <input type="text" name="certificazione_ente[]" value="{{ $cert['ente'] ?? '' }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent" placeholder="Es: CONI, FIF">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Data Conseguimento</label>
                            <input type="date" name="certificazione_data[]" value="{{ $cert['data_conseguimento'] ?? '' }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Data Scadenza</label>
                            <input type="date" name="certificazione_scadenza[]" value="{{ $cert['scadenza'] ?? '' }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent">
                        </div>
                    </div>
                </div>
                @endforeach
            @else
                <div class="certificazione-item border rounded-lg p-4 bg-gray-50">
                    <div class="flex justify-between items-start mb-3">
                        <h3 class="font-semibold text-gray-800">Certificazione #1</h3>
                        <button type="button" onclick="rimuoviCertificazione(this)" class="text-red-600 hover:text-red-800">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nome Certificazione *</label>
                            <input type="text" name="certificazione_nome[]" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent" placeholder="Es: Personal Trainer CONI">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Ente Certificatore</label>
                            <input type="text" name="certificazione_ente[]" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent" placeholder="Es: CONI, FIF">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Data Conseguimento</label>
                            <input type="date" name="certificazione_data[]" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Data Scadenza</label>
                            <input type="date" name="certificazione_scadenza[]" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent">
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <div class="mt-4">
            <button type="button" onclick="aggiungiCertificazione()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                <i class="fas fa-plus mr-2"></i> Aggiungi Certificazione
            </button>
        </div>

        <div class="mt-6 flex justify-end space-x-3">
            <a href="{{ route('admin.professionisti.show', $professionista->id) }}" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                Annulla
            </a>
            <button type="submit" class="px-4 py-2 bg-fucsia-magia text-white rounded-lg hover:bg-viola-magia">
                <i class="fas fa-save mr-2"></i> Salva Certificazioni
            </button>
        </div>
    </form>
</div>

<script>
let certificazioneCounter = {{ $professionista->certificazioni ? count($professionista->certificazioni) : 1 }};

function aggiungiCertificazione() {
    certificazioneCounter++;

    const template = `
        <div class="certificazione-item border rounded-lg p-4 bg-gray-50">
            <div class="flex justify-between items-start mb-3">
                <h3 class="font-semibold text-gray-800">Certificazione #${certificazioneCounter}</h3>
                <button type="button" onclick="rimuoviCertificazione(this)" class="text-red-600 hover:text-red-800">
                    <i class="fas fa-trash"></i>
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nome Certificazione *</label>
                    <input type="text" name="certificazione_nome[]" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent" placeholder="Es: Personal Trainer CONI">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Ente Certificatore</label>
                    <input type="text" name="certificazione_ente[]" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent" placeholder="Es: CONI, FIF">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Data Conseguimento</label>
                    <input type="date" name="certificazione_data[]" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Data Scadenza</label>
                    <input type="date" name="certificazione_scadenza[]" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent">
                </div>
            </div>
        </div>
    `;

    document.getElementById('certificazioni-container').insertAdjacentHTML('beforeend', template);
}

function rimuoviCertificazione(button) {
    const container = document.getElementById('certificazioni-container');
    const items = container.querySelectorAll('.certificazione-item');

    if (items.length > 1) {
        button.closest('.certificazione-item').remove();
        ricalcolaNumeri();
    } else {
        alert('Deve esserci almeno una certificazione. Lasciala vuota se non hai certificazioni.');
    }
}

function ricalcolaNumeri() {
    const items = document.querySelectorAll('.certificazione-item');
    items.forEach((item, index) => {
        item.querySelector('h3').textContent = `Certificazione #${index + 1}`;
    });
}
</script>
@endsection
