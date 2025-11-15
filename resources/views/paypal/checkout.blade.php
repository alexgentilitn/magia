<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout PayPal | MA.GIA DONNA</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-purple-50 via-pink-50 to-orange-50 min-h-screen">

    <!-- Header -->
    <div class="bg-white shadow-md">
        <div class="container mx-auto px-4 py-4">
            <a href="{{ route('home') }}" class="text-2xl font-bold bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent">
                MA.GIA DONNA
            </a>
        </div>
    </div>

    <div class="container mx-auto px-4 py-12">
        <div class="max-w-2xl mx-auto">

            <!-- Breadcrumb -->
            <div class="mb-8 text-sm text-gray-600">
                <a href="{{ route('home') }}" class="hover:text-purple-600">Home</a>
                <i class="fas fa-chevron-right mx-2"></i>
                <span class="text-purple-600 font-semibold">Checkout</span>
            </div>

            <!-- Titolo -->
            <div class="text-center mb-8">
                <h1 class="text-4xl font-bold text-gray-800 mb-2">
                    <i class="fas fa-shopping-cart text-purple-600 mr-3"></i>Checkout
                </h1>
                <p class="text-gray-600">Completa il tuo ordine con PayPal</p>
            </div>

            <!-- Riepilogo Ordine -->
            <div class="bg-white rounded-2xl shadow-xl p-8 mb-6">
                <h2 class="text-2xl font-bold text-gray-800 mb-6 border-b pb-4">
                    <i class="fas fa-file-invoice text-purple-600 mr-2"></i>Riepilogo Ordine
                </h2>

                <div class="space-y-4">
                    <div class="flex justify-between py-3 border-b">
                        <span class="text-gray-600">Numero Ordine:</span>
                        <span class="font-bold text-gray-800">{{ $ordine->numero_ordine ?? '#' . $ordine->id }}</span>
                    </div>

                    <div class="flex justify-between py-3 border-b">
                        <span class="text-gray-600">Descrizione:</span>
                        <span class="font-semibold text-gray-800">{{ $ordine->descrizione ?? 'Servizi MA.GIA DONNA' }}</span>
                    </div>

                    @if(isset($ordine->dettagli))
                        <div class="py-3 border-b">
                            <p class="text-gray-600 mb-2">Dettagli:</p>
                            <p class="text-gray-700 text-sm">{{ $ordine->dettagli }}</p>
                        </div>
                    @endif

                    <div class="flex justify-between py-3 border-b">
                        <span class="text-gray-600">Subtotale:</span>
                        <span class="font-semibold text-gray-800">€ {{ number_format($ordine->importo ?? 0, 2, ',', '.') }}</span>
                    </div>

                    @if(isset($ordine->sconto) && $ordine->sconto > 0)
                        <div class="flex justify-between py-3 border-b text-green-600">
                            <span>Sconto:</span>
                            <span class="font-semibold">- € {{ number_format($ordine->sconto, 2, ',', '.') }}</span>
                        </div>
                    @endif

                    <div class="flex justify-between py-4 text-xl font-bold text-purple-600">
                        <span>Totale:</span>
                        <span>€ {{ number_format($ordine->importo ?? 0, 2, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <!-- Metodo di Pagamento -->
            <div class="bg-white rounded-2xl shadow-xl p-8 mb-6">
                <h2 class="text-2xl font-bold text-gray-800 mb-6 border-b pb-4">
                    <i class="fab fa-paypal text-blue-600 mr-2"></i>Metodo di Pagamento
                </h2>

                <div class="bg-blue-50 rounded-xl p-6 mb-6 border-2 border-blue-200">
                    <div class="flex items-center mb-4">
                        <i class="fab fa-paypal text-4xl text-blue-600 mr-4"></i>
                        <div>
                            <h3 class="font-bold text-gray-800">PayPal</h3>
                            <p class="text-sm text-gray-600">Pagamento sicuro con PayPal</p>
                        </div>
                    </div>
                    <ul class="text-sm text-gray-700 space-y-2">
                        <li><i class="fas fa-check text-green-600 mr-2"></i>Protezione acquisti PayPal</li>
                        <li><i class="fas fa-check text-green-600 mr-2"></i>Pagamento con carta o conto PayPal</li>
                        <li><i class="fas fa-check text-green-600 mr-2"></i>Transazione crittografata e sicura</li>
                    </ul>
                </div>

                <!-- Pulsante Paga con PayPal -->
                <button id="paypal-button"
                        class="w-full bg-gradient-to-r from-blue-500 to-blue-600 text-white py-4 rounded-xl text-lg font-bold hover:from-blue-600 hover:to-blue-700 transition transform hover:scale-105 shadow-lg flex items-center justify-center"
                        onclick="initiatePayPalPayment()">
                    <i class="fab fa-paypal mr-3 text-2xl"></i>
                    Paga con PayPal
                </button>

                <p class="text-center text-sm text-gray-500 mt-4">
                    <i class="fas fa-lock mr-1"></i>
                    Transazione protetta SSL
                </p>
            </div>

            <!-- Info Sicurezza -->
            <div class="bg-green-50 border-l-4 border-green-500 rounded-lg p-6">
                <h3 class="font-bold text-green-800 mb-2">
                    <i class="fas fa-shield-alt mr-2"></i>Pagamento Sicuro
                </h3>
                <p class="text-green-700 text-sm">
                    I tuoi dati di pagamento sono protetti con crittografia SSL a 256 bit.
                    Non memorizziamo mai i dati della tua carta di credito.
                </p>
            </div>

        </div>
    </div>

    <!-- Loading Overlay -->
    <div id="loading-overlay" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-2xl p-8 text-center">
            <div class="animate-spin rounded-full h-16 w-16 border-b-4 border-purple-600 mx-auto mb-4"></div>
            <p class="text-gray-700 font-semibold">Elaborazione in corso...</p>
            <p class="text-sm text-gray-500 mt-2">Verrai reindirizzato a PayPal</p>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-8 mt-16">
        <div class="container mx-auto px-4 text-center">
            <p>&copy; {{ date('Y') }} MA.GIA DONNA - Tutti i diritti riservati</p>
        </div>
    </footer>

    <script>
        function initiatePayPalPayment() {
            // Mostra loading
            document.getElementById('loading-overlay').classList.remove('hidden');

            // Disabilita pulsante
            const button = document.getElementById('paypal-button');
            button.disabled = true;
            button.classList.add('opacity-50', 'cursor-not-allowed');

            // Dati ordine
            const ordineData = {
                ordine_id: {{ $ordine->id }},
                importo: {{ $ordine->importo ?? 0 }},
                descrizione: '{{ $ordine->descrizione ?? "Servizi MA.GIA DONNA" }}',
                _token: '{{ csrf_token() }}'
            };

            // Chiama API per creare il pagamento
            fetch('{{ route("paypal.create") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(ordineData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.approval_url) {
                    // Reindirizza a PayPal
                    window.location.href = data.approval_url;
                } else {
                    // Errore
                    alert('Errore: ' + (data.error || 'Impossibile creare il pagamento'));
                    document.getElementById('loading-overlay').classList.add('hidden');
                    button.disabled = false;
                    button.classList.remove('opacity-50', 'cursor-not-allowed');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Errore di connessione. Riprova più tardi.');
                document.getElementById('loading-overlay').classList.add('hidden');
                button.disabled = false;
                button.classList.remove('opacity-50', 'cursor-not-allowed');
            });
        }
    </script>

</body>
</html>
