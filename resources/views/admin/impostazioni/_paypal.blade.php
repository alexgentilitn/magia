<!-- Tab Content: PayPal e Pagamenti -->
<div x-show="activeTab === 'paypal'" x-cloak>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Form Configurazione PayPal -->
        <div class="lg:col-span-2">
            <form method="POST" action="{{ route('admin.impostazioni.paypal.salva') }}" class="bg-white rounded-lg shadow">
                @csrf

                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-xl font-bold text-gray-800 mb-1">
                        <i class="fab fa-paypal text-fucsia-magia mr-2"></i>
                        Configurazione Sistema Pagamenti
                    </h2>
                    <p class="text-sm text-gray-600">Gestisci PayPal, bonifici e metodi di pagamento</p>
                </div>

                <div class="p-6 space-y-8">

                    <!-- Sezione PayPal -->
                    <div class="border-l-4 border-blue-500 pl-4">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">
                            <i class="fab fa-paypal mr-2"></i>
                            PayPal
                        </h3>

                        <!-- Abilita PayPal -->
                        <div class="mb-6 p-4 bg-blue-50 rounded-lg">
                            <label class="flex items-center cursor-pointer">
                                <input
                                    type="checkbox"
                                    name="paypal_attivo"
                                    value="1"
                                    {{ (old('paypal_attivo', $impostazioniPaypal['paypal_attivo']['valore'] ?? '1') == '1') ? 'checked' : '' }}
                                    class="w-5 h-5 text-blue-600 rounded focus:ring-2 focus:ring-blue-500"
                                >
                                <span class="ml-3 text-sm font-medium text-gray-800">
                                    Abilita pagamenti con PayPal
                                </span>
                            </label>
                        </div>

                        <!-- Modalità -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Modalità Operativa *
                            </label>
                            <select
                                name="paypal_mode"
                                required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent"
                            >
                                <option value="sandbox" {{ (old('paypal_mode', $impostazioniPaypal['paypal_mode']['valore'] ?? 'sandbox') == 'sandbox') ? 'selected' : '' }}>
                                    🧪 Sandbox (Test)
                                </option>
                                <option value="live" {{ (old('paypal_mode', $impostazioniPaypal['paypal_mode']['valore'] ?? '') == 'live') ? 'selected' : '' }}>
                                    🟢 Live (Produzione)
                                </option>
                            </select>
                            <p class="mt-1 text-xs text-gray-500">
                                Usa "Sandbox" per testare senza pagamenti reali. Passa a "Live" quando sei pronto per la produzione.
                            </p>
                        </div>

                        <!-- Client ID -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                PayPal Client ID *
                                <span class="text-gray-500 font-normal">(da PayPal Developer Dashboard)</span>
                            </label>
                            <input
                                type="text"
                                name="paypal_client_id"
                                value="{{ old('paypal_client_id', $impostazioniPaypal['paypal_client_id']['valore'] ?? '') }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent font-mono text-sm"
                                placeholder="AeT7X9ZqfN..."
                            >
                            <p class="mt-1 text-xs text-gray-500">
                                Ottieni le credenziali su: <a href="https://developer.paypal.com/dashboard/" target="_blank" class="text-blue-600 hover:underline">PayPal Developer Dashboard</a>
                            </p>
                        </div>

                        <!-- Client Secret -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                PayPal Client Secret *
                                <span class="text-gray-500 font-normal">(criptato nel database)</span>
                            </label>
                            <input
                                type="password"
                                name="paypal_client_secret"
                                value="{{ old('paypal_client_secret', $impostazioniPaypal['paypal_client_secret']['valore'] ?? '') }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent font-mono text-sm"
                                placeholder="EJL9xyz..."
                            >
                            <p class="mt-1 text-xs text-gray-500">
                                Lascia vuoto per mantenere il valore esistente. Viene criptato automaticamente.
                            </p>
                        </div>
                    </div>

                    <!-- Sezione Bonifico -->
                    <div class="border-l-4 border-green-500 pl-4">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">
                            <i class="fas fa-money-check mr-2"></i>
                            Bonifico Bancario
                        </h3>

                        <!-- Abilita Bonifico -->
                        <div class="mb-6 p-4 bg-green-50 rounded-lg">
                            <label class="flex items-center cursor-pointer">
                                <input
                                    type="checkbox"
                                    name="bonifico_attivo"
                                    value="1"
                                    {{ (old('bonifico_attivo', $impostazioniPagamento['bonifico_attivo']['valore'] ?? '1') == '1') ? 'checked' : '' }}
                                    class="w-5 h-5 text-green-600 rounded focus:ring-2 focus:ring-green-500"
                                >
                                <span class="ml-3 text-sm font-medium text-gray-800">
                                    Abilita pagamenti con Bonifico Bancario
                                </span>
                            </label>
                        </div>

                        <!-- IBAN -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                IBAN *
                            </label>
                            <input
                                type="text"
                                name="bonifico_iban"
                                required
                                value="{{ old('bonifico_iban', $impostazioniPagamento['bonifico_iban']['valore'] ?? 'IT00X0000000000000000000000') }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent font-mono"
                                placeholder="IT00X0000000000000000000000"
                                maxlength="34"
                            >
                        </div>

                        <!-- Intestatario -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Intestatario Conto *
                            </label>
                            <input
                                type="text"
                                name="bonifico_intestatario"
                                required
                                value="{{ old('bonifico_intestatario', $impostazioniPagamento['bonifico_intestatario']['valore'] ?? 'MA.GIA DONNA S.R.L.') }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent"
                                placeholder="MA.GIA DONNA S.R.L."
                            >
                        </div>

                        <!-- Banca -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Nome Banca *
                            </label>
                            <input
                                type="text"
                                name="bonifico_banca"
                                required
                                value="{{ old('bonifico_banca', $impostazioniPagamento['bonifico_banca']['valore'] ?? 'Banca Esempio') }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent"
                                placeholder="Unicredit"
                            >
                        </div>
                    </div>

                    <!-- Sezione Importo -->
                    <div class="border-l-4 border-fucsia-magia pl-4">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">
                            <i class="fas fa-euro-sign mr-2"></i>
                            Importo Quota Registrazione
                        </h3>

                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Importo in Euro *
                            </label>
                            <div class="relative">
                                <span class="absolute left-3 top-2 text-gray-500">€</span>
                                <input
                                    type="number"
                                    name="paypal_importo_registrazione"
                                    required
                                    min="0"
                                    step="0.01"
                                    value="{{ old('paypal_importo_registrazione', $impostazioniPaypal['paypal_importo_registrazione']['valore'] ?? '50.00') }}"
                                    class="w-full pl-8 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent"
                                    placeholder="50.00"
                                >
                            </div>
                            <p class="mt-1 text-xs text-gray-500">
                                Questo importo verrà applicato sia per PayPal che per Bonifico
                            </p>
                        </div>
                    </div>

                </div>

                <!-- Bottoni Azione -->
                <div class="p-6 bg-gray-50 border-t border-gray-200 flex justify-between items-center">
                    <a href="{{ route('admin.dashboard') }}" class="text-gray-600 hover:text-gray-800 font-medium">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Torna alla Dashboard
                    </a>

                    <button
                        type="submit"
                        class="px-6 py-2 bg-fucsia-magia text-white font-semibold rounded-lg hover:bg-fucsia-magia-dark transition-colors"
                    >
                        <i class="fas fa-save mr-2"></i>
                        Salva Configurazione
                    </button>
                </div>
            </form>
        </div>

        <!-- Sidebar Compatta -->
        <div class="space-y-4">
            <!-- Card Test Connessione PayPal -->
            <div class="bg-white rounded-lg shadow p-4">
                <h3 class="text-sm font-bold text-gray-800 mb-3 flex items-center">
                    <i class="fas fa-vial text-fucsia-magia mr-2 text-xs"></i>
                    Test Connessione PayPal
                </h3>

                <p class="text-xs text-gray-600 mb-3">
                    Verifica credenziali PayPal
                </p>

                <div id="paypal-test-result" class="mb-3 hidden"></div>

                <button
                    type="button"
                    onclick="testPayPal()"
                    class="w-full px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors"
                >
                    <i class="fas fa-play-circle mr-2"></i>
                    Testa Connessione
                </button>

                <p class="mt-2 text-[10px] text-gray-500">
                    <i class="fas fa-info-circle mr-1"></i>
                    Salva prima, poi testa
                </p>
            </div>

            <!-- Card Stato Metodi Pagamento -->
            <div class="bg-white rounded-lg shadow p-4">
                <h3 class="text-sm font-bold text-gray-800 mb-3 flex items-center">
                    <i class="fas fa-toggle-on text-fucsia-magia mr-2 text-xs"></i>
                    Metodi Attivi
                </h3>

                <div class="space-y-2">
                    <!-- PayPal Status -->
                    <div class="flex items-center justify-between p-2 bg-blue-50 rounded">
                        <div class="flex items-center">
                            <i class="fab fa-paypal text-blue-600 mr-2 text-xs"></i>
                            <span class="text-xs font-medium">PayPal</span>
                        </div>
                        <span class="text-[10px] px-2 py-0.5 rounded font-semibold {{ (old('paypal_attivo', $impostazioniPaypal['paypal_attivo']['valore'] ?? '1') == '1') ? 'bg-green-200 text-green-800' : 'bg-gray-200 text-gray-600' }}">
                            {{ (old('paypal_attivo', $impostazioniPaypal['paypal_attivo']['valore'] ?? '1') == '1') ? 'Attivo' : 'Off' }}
                        </span>
                    </div>

                    <!-- Bonifico Status -->
                    <div class="flex items-center justify-between p-2 bg-green-50 rounded">
                        <div class="flex items-center">
                            <i class="fas fa-money-check text-green-600 mr-2 text-xs"></i>
                            <span class="text-xs font-medium">Bonifico</span>
                        </div>
                        <span class="text-[10px] px-2 py-0.5 rounded font-semibold {{ (old('bonifico_attivo', $impostazioniPagamento['bonifico_attivo']['valore'] ?? '1') == '1') ? 'bg-green-200 text-green-800' : 'bg-gray-200 text-gray-600' }}">
                            {{ (old('bonifico_attivo', $impostazioniPagamento['bonifico_attivo']['valore'] ?? '1') == '1') ? 'Attivo' : 'Off' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Script Test PayPal -->
<script>
function testPayPal() {
    const resultDiv = document.getElementById('paypal-test-result');
    const button = event.target;

    // Disabilita bottone
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Testing...';

    // Mostra messaggio caricamento
    resultDiv.className = 'p-3 bg-gray-100 border border-gray-300 rounded mb-4';
    resultDiv.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Test connessione PayPal in corso...';
    resultDiv.classList.remove('hidden');

    // Esegui test
    fetch('{{ route('admin.impostazioni.paypal.test') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            resultDiv.className = 'p-3 bg-green-100 border border-green-300 rounded mb-4';
            resultDiv.innerHTML = '<i class="fas fa-check-circle text-green-600 mr-2"></i>' + data.message;
        } else {
            resultDiv.className = 'p-3 bg-red-100 border border-red-300 rounded mb-4';
            resultDiv.innerHTML = '<i class="fas fa-times-circle text-red-600 mr-2"></i>' + data.message;
        }
    })
    .catch(error => {
        resultDiv.className = 'p-3 bg-red-100 border border-red-300 rounded mb-4';
        resultDiv.innerHTML = '<i class="fas fa-exclamation-triangle text-red-600 mr-2"></i> Errore durante il test: ' + error.message;
    })
    .finally(() => {
        // Riabilita bottone
        button.disabled = false;
        button.innerHTML = '<i class="fas fa-play-circle mr-2"></i> Testa Connessione';
    });
}
</script>
