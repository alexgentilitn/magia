@extends('layouts.guest')

@section('title', 'Pagamento Bonifico Bancario')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-purple-50 to-pink-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-3xl mx-auto">

        <!-- Header -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-purple-100 rounded-full mb-4">
                <i class="fas fa-university text-3xl text-purple-600"></i>
            </div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Bonifico Bancario</h1>
            <p class="text-gray-600">Effettua il bonifico e carica la ricevuta</p>
        </div>

        <!-- Step Indicator -->
        <div class="mb-8">
            <div class="flex items-center justify-center">
                <div class="flex items-center">
                    <div class="flex items-center justify-center w-10 h-10 bg-purple-600 text-white rounded-full font-bold">1</div>
                    <span class="ml-3 font-medium text-gray-900">Bonifico</span>
                </div>
                <div class="w-16 h-1 bg-gray-300 mx-4"></div>
                <div class="flex items-center">
                    <div class="flex items-center justify-center w-10 h-10 bg-gray-300 text-gray-600 rounded-full font-bold">2</div>
                    <span class="ml-3 font-medium text-gray-500">Ricevuta</span>
                </div>
                <div class="w-16 h-1 bg-gray-300 mx-4"></div>
                <div class="flex items-center">
                    <div class="flex items-center justify-center w-10 h-10 bg-gray-300 text-gray-600 rounded-full font-bold">3</div>
                    <span class="ml-3 font-medium text-gray-500">Verifica</span>
                </div>
            </div>
        </div>

        <!-- Card Coordinate Bancarie -->
        <div class="bg-white rounded-lg shadow-lg p-8 mb-6">
            <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                <i class="fas fa-piggy-bank text-purple-600 mr-3"></i>
                Coordinate Bancarie
            </h2>

            <div class="space-y-4">
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-sm text-gray-500 mb-1">Intestatario</p>
                    <p class="font-bold text-gray-900 text-lg">{{ $coordinate['intestatario'] }}</p>
                </div>

                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-sm text-gray-500 mb-1">IBAN</p>
                    <div class="flex items-center justify-between">
                        <p class="font-mono font-bold text-gray-900 text-lg">{{ $coordinate['iban'] }}</p>
                        <button onclick="copyToClipboard('{{ $coordinate['iban'] }}')"
                                class="text-purple-600 hover:text-purple-700">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                </div>

                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-sm text-gray-500 mb-1">Banca</p>
                    <p class="font-semibold text-gray-900">{{ $coordinate['banca'] }}</p>
                </div>

                <div class="bg-purple-50 rounded-lg p-4 border-2 border-purple-200">
                    <p class="text-sm text-purple-700 mb-1">Causale (IMPORTANTE)</p>
                    <div class="flex items-center justify-between">
                        <p class="font-semibold text-purple-900">{{ $coordinate['causale'] }}</p>
                        <button onclick="copyToClipboard('{{ $coordinate['causale'] }}')"
                                class="text-purple-600 hover:text-purple-700">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                </div>

                <div class="bg-gradient-to-r from-pink-500 to-purple-600 rounded-lg p-4 text-white">
                    <p class="text-pink-100 text-sm mb-1">Importo da versare</p>
                    <p class="font-bold text-3xl">€ {{ $coordinate['importo'] }}</p>
                </div>
            </div>

            <!-- Alert Istruzioni -->
            <div class="mt-6 bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded">
                <div class="flex">
                    <i class="fas fa-exclamation-triangle text-yellow-400 mt-1 mr-3"></i>
                    <div>
                        <h4 class="font-semibold text-yellow-800 mb-1">Attenzione</h4>
                        <p class="text-sm text-yellow-700">Inserisci esattamente la causale indicata per velocizzare la verifica del bonifico.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card Caricamento Ricevuta -->
        <div class="bg-white rounded-lg shadow-lg p-8">
            <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                <i class="fas fa-cloud-upload-alt text-purple-600 mr-3"></i>
                Carica Ricevuta
            </h2>

            <form action="{{ route('pagamento.bonifico.salva') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="utente_id" value="{{ $utente->id }}">

                <div class="space-y-6">
                    <!-- Data Bonifico -->
                    <div>
                        <label for="data_bonifico" class="block text-sm font-medium text-gray-700 mb-2">
                            Data Bonifico *
                        </label>
                        <input type="date"
                               name="data_bonifico"
                               id="data_bonifico"
                               max="{{ date('Y-m-d') }}"
                               required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        @error('data_bonifico')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Importo Bonifico -->
                    <div>
                        <label for="importo_bonifico" class="block text-sm font-medium text-gray-700 mb-2">
                            Importo Versato (€) *
                        </label>
                        <input type="number"
                               name="importo_bonifico"
                               id="importo_bonifico"
                               step="0.01"
                               min="0.01"
                               value="50.00"
                               required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        @error('importo_bonifico')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Upload Ricevuta -->
                    <div>
                        <label for="ricevuta" class="block text-sm font-medium text-gray-700 mb-2">
                            Ricevuta Bonifico (PDF, JPG, PNG) *
                        </label>
                        <div class="relative">
                            <input type="file"
                                   name="ricevuta"
                                   id="ricevuta"
                                   accept=".pdf,.jpg,.jpeg,.png"
                                   required
                                   class="w-full px-4 py-3 border-2 border-dashed border-gray-300 rounded-lg hover:border-purple-400 focus:border-purple-500 focus:ring-2 focus:ring-purple-500 cursor-pointer"
                                   onchange="showFileName(this)">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <i class="fas fa-upload text-gray-400"></i>
                            </div>
                        </div>
                        <p class="mt-2 text-sm text-gray-500">
                            Formato: PDF, JPG, PNG (max 5MB)
                        </p>
                        <p id="file-name" class="mt-2 text-sm text-purple-600 font-medium hidden"></p>
                        @error('ricevuta')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Buttons -->
                <div class="mt-8 flex items-center justify-between pt-6 border-t border-gray-200">
                    <a href="{{ route('pagamento.scelta', $utente->id) }}"
                       class="inline-flex items-center px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Torna Indietro
                    </a>

                    <button type="submit"
                            class="inline-flex items-center px-8 py-3 bg-gradient-to-r from-pink-600 to-purple-600 text-white font-semibold rounded-lg hover:from-pink-700 hover:to-purple-700 transition shadow-lg">
                        <i class="fas fa-check mr-2"></i>
                        Carica Ricevuta
                    </button>
                </div>
            </form>
        </div>

        <!-- Info Timeline -->
        <div class="mt-6 bg-blue-50 rounded-lg p-6">
            <h3 class="font-semibold text-blue-900 mb-4 flex items-center">
                <i class="fas fa-clock mr-2"></i>
                Cosa succede dopo?
            </h3>
            <ol class="space-y-3">
                <li class="flex items-start text-sm text-blue-800">
                    <span class="flex items-center justify-center w-6 h-6 bg-blue-200 rounded-full mr-3 mt-0.5 flex-shrink-0">1</span>
                    <span>Riceverai una conferma di caricamento via email</span>
                </li>
                <li class="flex items-start text-sm text-blue-800">
                    <span class="flex items-center justify-center w-6 h-6 bg-blue-200 rounded-full mr-3 mt-0.5 flex-shrink-0">2</span>
                    <span>Il nostro team verificherà il bonifico entro 24-48 ore lavorative</span>
                </li>
                <li class="flex items-start text-sm text-blue-800">
                    <span class="flex items-center justify-center w-6 h-6 bg-blue-200 rounded-full mr-3 mt-0.5 flex-shrink-0">3</span>
                    <span>Una volta approvato, il tuo account verrà attivato e riceverai le credenziali di accesso</span>
                </li>
            </ol>
        </div>

    </div>
</div>

<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        // Show toast notification
        const toast = document.createElement('div');
        toast.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50';
        toast.innerHTML = '<i class="fas fa-check mr-2"></i> Copiato negli appunti!';
        document.body.appendChild(toast);

        setTimeout(() => {
            toast.remove();
        }, 2000);
    });
}

function showFileName(input) {
    const fileName = input.files[0]?.name;
    const fileNameEl = document.getElementById('file-name');

    if (fileName) {
        fileNameEl.textContent = `File selezionato: ${fileName}`;
        fileNameEl.classList.remove('hidden');
    } else {
        fileNameEl.classList.add('hidden');
    }
}
</script>
@endsection
