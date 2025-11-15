<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagamento Completato | MA.GIA DONNA</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        @keyframes checkmark {
            0% {
                transform: scale(0) rotate(45deg);
                opacity: 0;
            }
            50% {
                transform: scale(1.2) rotate(45deg);
                opacity: 1;
            }
            100% {
                transform: scale(1) rotate(45deg);
                opacity: 1;
            }
        }

        .checkmark {
            animation: checkmark 0.6s ease-in-out;
        }

        @keyframes confetti {
            0% {
                transform: translateY(0) rotate(0deg);
                opacity: 1;
            }
            100% {
                transform: translateY(100vh) rotate(720deg);
                opacity: 0;
            }
        }
    </style>
</head>
<body class="bg-gradient-to-br from-green-50 via-emerald-50 to-teal-50 min-h-screen">

    <!-- Header -->
    <div class="bg-white shadow-md">
        <div class="container mx-auto px-4 py-4">
            <a href="{{ route('home') }}" class="text-2xl font-bold bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent">
                MA.GIA DONNA
            </a>
        </div>
    </div>

    <div class="container mx-auto px-4 py-12">
        <div class="max-w-3xl mx-auto">

            <!-- Success Icon -->
            <div class="text-center mb-8">
                <div class="inline-block bg-green-100 rounded-full p-8 mb-6">
                    <div class="checkmark">
                        <i class="fas fa-check-circle text-8xl text-green-600"></i>
                    </div>
                </div>
                <h1 class="text-5xl font-bold text-gray-800 mb-4">Pagamento Completato!</h1>
                <p class="text-xl text-gray-600">Grazie per il tuo acquisto 🎉</p>
            </div>

            <!-- Dettagli Ordine -->
            <div class="bg-white rounded-2xl shadow-xl p-8 mb-6">
                <h2 class="text-2xl font-bold text-gray-800 mb-6 border-b pb-4">
                    <i class="fas fa-receipt text-green-600 mr-2"></i>Riepilogo Transazione
                </h2>

                <div class="space-y-4">
                    @if($ordine)
                        <div class="flex justify-between py-3 border-b">
                            <span class="text-gray-600">Numero Ordine:</span>
                            <span class="font-bold text-gray-800">{{ $ordine->numero_ordine ?? '#' . $ordine->id }}</span>
                        </div>

                        <div class="flex justify-between py-3 border-b">
                            <span class="text-gray-600">Data Pagamento:</span>
                            <span class="font-semibold text-gray-800">{{ \Carbon\Carbon::parse($ordine->data_pagamento ?? now())->format('d/m/Y H:i') }}</span>
                        </div>

                        <div class="flex justify-between py-3 border-b">
                            <span class="text-gray-600">Metodo di Pagamento:</span>
                            <span class="font-semibold text-gray-800">
                                <i class="fab fa-paypal text-blue-600 mr-1"></i>PayPal
                            </span>
                        </div>

                        <div class="flex justify-between py-4 text-2xl font-bold text-green-600">
                            <span>Importo Pagato:</span>
                            <span>€ {{ number_format($ordine->importo ?? 0, 2, ',', '.') }}</span>
                        </div>
                    @endif
                </div>

                <div class="mt-6 bg-green-50 border-l-4 border-green-500 rounded-lg p-4">
                    <p class="text-green-800 font-semibold">
                        <i class="fas fa-check-circle mr-2"></i>
                        Il tuo pagamento è stato elaborato con successo
                    </p>
                    <p class="text-green-700 text-sm mt-1">
                        Riceverai una email di conferma con tutti i dettagli della transazione
                    </p>
                </div>
            </div>

            <!-- Prossimi Passi -->
            <div class="bg-white rounded-2xl shadow-xl p-8 mb-6">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">
                    <i class="fas fa-tasks text-purple-600 mr-2"></i>Cosa Succede Ora?
                </h2>

                <div class="space-y-4">
                    <div class="flex items-start">
                        <div class="bg-purple-100 rounded-full p-3 mr-4">
                            <i class="fas fa-envelope text-purple-600 text-xl"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-800 mb-1">Email di Conferma</h3>
                            <p class="text-gray-600 text-sm">Riceverai un'email con la conferma del pagamento e i dettagli dell'ordine</p>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <div class="bg-pink-100 rounded-full p-3 mr-4">
                            <i class="fas fa-user-check text-pink-600 text-xl"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-800 mb-1">Accesso all'Area Cliente</h3>
                            <p class="text-gray-600 text-sm">Potrai visualizzare tutti i tuoi ordini e pagamenti dalla tua area riservata</p>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <div class="bg-orange-100 rounded-full p-3 mr-4">
                            <i class="fas fa-phone text-orange-600 text-xl"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-800 mb-1">Contatto dal Team</h3>
                            <p class="text-gray-600 text-sm">Il nostro staff ti contatterà per confermare i dettagli e organizzare il servizio</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @auth
                    <a href="{{ route('cliente.dashboard') }}"
                       class="bg-gradient-to-r from-purple-600 to-pink-600 text-white text-center px-8 py-4 rounded-xl font-bold hover:shadow-lg transition transform hover:scale-105">
                        <i class="fas fa-tachometer-alt mr-2"></i>Vai all'Area Cliente
                    </a>
                @else
                    <a href="{{ route('home') }}"
                       class="bg-gradient-to-r from-purple-600 to-pink-600 text-white text-center px-8 py-4 rounded-xl font-bold hover:shadow-lg transition transform hover:scale-105">
                        <i class="fas fa-home mr-2"></i>Torna alla Home
                    </a>
                @endauth

                <a href="mailto:info@magiadonna.it"
                   class="bg-white border-2 border-purple-600 text-purple-600 text-center px-8 py-4 rounded-xl font-bold hover:bg-purple-50 transition">
                    <i class="fas fa-envelope mr-2"></i>Contattaci
                </a>
            </div>

            <!-- Info Aggiuntive -->
            <div class="mt-8 text-center">
                <p class="text-gray-600 mb-4">Hai domande o dubbi sul tuo ordine?</p>
                <div class="flex justify-center space-x-6 text-sm">
                    <a href="tel:+393471234567" class="text-purple-600 hover:text-purple-800">
                        <i class="fas fa-phone mr-1"></i>Chiamaci
                    </a>
                    <a href="mailto:info@magiadonna.it" class="text-purple-600 hover:text-purple-800">
                        <i class="fas fa-envelope mr-1"></i>Scrivici
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
