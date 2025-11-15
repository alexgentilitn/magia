<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagamento Annullato | MA.GIA DONNA</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-orange-50 via-yellow-50 to-red-50 min-h-screen">

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

            <!-- Cancel Icon -->
            <div class="text-center mb-8">
                <div class="inline-block bg-orange-100 rounded-full p-8 mb-6">
                    <i class="fas fa-times-circle text-8xl text-orange-600"></i>
                </div>
                <h1 class="text-5xl font-bold text-gray-800 mb-4">Pagamento Annullato</h1>
                <p class="text-xl text-gray-600">Il processo di pagamento è stato interrotto</p>
            </div>

            <!-- Info Card -->
            <div class="bg-white rounded-2xl shadow-xl p-8 mb-6">
                <div class="bg-orange-50 border-l-4 border-orange-500 rounded-lg p-6 mb-6">
                    <h3 class="font-bold text-orange-800 mb-2">
                        <i class="fas fa-info-circle mr-2"></i>Cosa è Successo?
                    </h3>
                    <p class="text-orange-700">
                        Hai annullato il pagamento o si è verificato un problema durante la transazione con PayPal.
                        Il tuo ordine non è stato completato e non è stato effettuato alcun addebito.
                    </p>
                </div>

                @if(isset($ordineId))
                    <div class="mb-6">
                        <p class="text-gray-600 mb-4">Dettagli ordine:</p>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <p class="text-sm text-gray-700">
                                <span class="font-semibold">ID Ordine:</span> #{{ $ordineId }}
                            </p>
                            <p class="text-sm text-gray-700 mt-2">
                                <span class="font-semibold">Stato:</span>
                                <span class="text-orange-600 font-semibold">Annullato</span>
                            </p>
                        </div>
                    </div>
                @endif

                <h3 class="text-xl font-bold text-gray-800 mb-4">Cosa Puoi Fare Ora?</h3>

                <div class="space-y-4">
                    <div class="flex items-start">
                        <div class="bg-purple-100 rounded-full p-3 mr-4 flex-shrink-0">
                            <i class="fas fa-redo text-purple-600 text-lg"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-800 mb-1">Riprova il Pagamento</h4>
                            <p class="text-gray-600 text-sm">Puoi ritentare il pagamento in qualsiasi momento. Il tuo ordine è ancora disponibile.</p>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <div class="bg-pink-100 rounded-full p-3 mr-4 flex-shrink-0">
                            <i class="fas fa-phone text-pink-600 text-lg"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-800 mb-1">Contatta il Supporto</h4>
                            <p class="text-gray-600 text-sm">Hai bisogno di aiuto? Il nostro team è a tua disposizione per assisterti.</p>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <div class="bg-blue-100 rounded-full p-3 mr-4 flex-shrink-0">
                            <i class="fas fa-credit-card text-blue-600 text-lg"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-800 mb-1">Metodo di Pagamento Alternativo</h4>
                            <p class="text-gray-600 text-sm">Puoi contattarci per valutare altri metodi di pagamento disponibili.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- FAQ Rapide -->
            <div class="bg-white rounded-2xl shadow-xl p-8 mb-6">
                <h3 class="text-xl font-bold text-gray-800 mb-4">
                    <i class="fas fa-question-circle text-purple-600 mr-2"></i>Domande Frequenti
                </h3>

                <div class="space-y-4">
                    <div class="border-b pb-3">
                        <h4 class="font-semibold text-gray-800 mb-2">È stato addebitato qualcosa sul mio conto?</h4>
                        <p class="text-sm text-gray-600">No, il pagamento è stato annullato prima del completamento. Non è stato effettuato alcun addebito.</p>
                    </div>

                    <div class="border-b pb-3">
                        <h4 class="font-semibold text-gray-800 mb-2">Posso riprovare a pagare?</h4>
                        <p class="text-sm text-gray-600">Sì, puoi ritentare il pagamento in qualsiasi momento. L'ordine rimane disponibile.</p>
                    </div>

                    <div class="pb-3">
                        <h4 class="font-semibold text-gray-800 mb-2">Per quanto tempo resta attivo il mio ordine?</h4>
                        <p class="text-sm text-gray-600">Il tuo ordine rimane attivo per 7 giorni. Dopo questo periodo, sarà necessario crearne uno nuovo.</p>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @if(isset($ordineId))
                    <a href="{{ route('paypal.checkout', ['ordine_id' => $ordineId]) }}"
                       class="bg-gradient-to-r from-purple-600 to-pink-600 text-white text-center px-8 py-4 rounded-xl font-bold hover:shadow-lg transition transform hover:scale-105">
                        <i class="fas fa-redo mr-2"></i>Riprova il Pagamento
                    </a>
                @else
                    <a href="{{ route('home') }}"
                       class="bg-gradient-to-r from-purple-600 to-pink-600 text-white text-center px-8 py-4 rounded-xl font-bold hover:shadow-lg transition transform hover:scale-105">
                        <i class="fas fa-home mr-2"></i>Torna alla Home
                    </a>
                @endif

                <a href="mailto:info@magiadonna.it"
                   class="bg-white border-2 border-purple-600 text-purple-600 text-center px-8 py-4 rounded-xl font-bold hover:bg-purple-50 transition">
                    <i class="fas fa-envelope mr-2"></i>Contatta il Supporto
                </a>
            </div>

            <!-- Info Contatti -->
            <div class="mt-8 text-center">
                <p class="text-gray-600 mb-4">Hai bisogno di assistenza immediata?</p>
                <div class="flex justify-center space-x-6">
                    <a href="tel:+393471234567" class="text-purple-600 hover:text-purple-800 font-semibold">
                        <i class="fas fa-phone mr-2"></i>+39 347 123 4567
                    </a>
                    <a href="mailto:info@magiadonna.it" class="text-purple-600 hover:text-purple-800 font-semibold">
                        <i class="fas fa-envelope mr-2"></i>info@magiadonna.it
                    </a>
                </div>
            </div>

        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-8 mt-16">
        <div class="container mx-auto px-4 text-center">
            <p>&copy; {{ date('Y') }} MA.GIA DONNA - Tutti i diritti riservati</p>
        </div>
    </footer>

</body>
</html>
