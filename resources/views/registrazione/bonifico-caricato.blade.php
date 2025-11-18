@extends('layouts.guest')

@section('title', 'Ricevuta Caricata')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-green-50 to-blue-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-2xl mx-auto">

        <!-- Success Icon -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-24 h-24 bg-green-100 rounded-full mb-6 animate-bounce">
                <i class="fas fa-check text-5xl text-green-600"></i>
            </div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Ricevuta Caricata con Successo!</h1>
            <p class="text-lg text-gray-600">Il tuo documento è stato ricevuto correttamente</p>
        </div>

        <!-- Info Card -->
        <div class="bg-white rounded-lg shadow-lg p-8 mb-6">
            <div class="flex items-start mb-6">
                <div class="flex-shrink-0">
                    <div class="flex items-center justify-center w-12 h-12 bg-blue-100 rounded-full">
                        <i class="fas fa-info-circle text-2xl text-blue-600"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <h2 class="text-xl font-semibold text-gray-900 mb-2">Cosa succede ora?</h2>
                    <p class="text-gray-600">Il nostro team verificherà il bonifico e attiverà il tuo account.</p>
                </div>
            </div>

            <!-- Timeline -->
            <div class="space-y-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <div class="flex items-center justify-center w-8 h-8 bg-green-500 text-white rounded-full">
                            <i class="fas fa-check text-sm"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h3 class="font-semibold text-gray-900">Ricevuta Caricata</h3>
                        <p class="text-sm text-gray-600">Il documento è stato ricevuto correttamente</p>
                        <p class="text-xs text-gray-500 mt-1">{{ now()->format('d/m/Y H:i') }}</p>
                    </div>
                </div>

                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <div class="flex items-center justify-center w-8 h-8 bg-yellow-400 text-white rounded-full animate-pulse">
                            <i class="fas fa-clock text-sm"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h3 class="font-semibold text-gray-900">In Verifica</h3>
                        <p class="text-sm text-gray-600">Il team amministrativo verificherà il bonifico</p>
                        <p class="text-xs text-gray-500 mt-1">Tempo stimato: 24-48 ore lavorative</p>
                    </div>
                </div>

                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <div class="flex items-center justify-center w-8 h-8 bg-gray-300 text-gray-600 rounded-full">
                            <i class="fas fa-envelope text-sm"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h3 class="font-semibold text-gray-500">Conferma via Email</h3>
                        <p class="text-sm text-gray-500">Riceverai le credenziali di accesso</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Email Confirmation -->
        <div class="bg-blue-50 rounded-lg p-6 mb-6">
            <div class="flex items-start">
                <i class="fas fa-envelope text-blue-500 text-2xl mt-1 mr-4"></i>
                <div>
                    <h3 class="font-semibold text-blue-900 mb-2">Email di Conferma Inviata</h3>
                    <p class="text-sm text-blue-800 mb-2">
                        Abbiamo inviato una conferma all'indirizzo:
                    </p>
                    <p class="font-mono font-semibold text-blue-900">{{ $utente->email }}</p>
                    <p class="text-xs text-blue-700 mt-2">
                        Se non trovi l'email, controlla anche la cartella spam.
                    </p>
                </div>
            </div>
        </div>

        <!-- Info Details -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
            <h3 class="font-semibold text-gray-900 mb-4">Riepilogo Dati</h3>
            <dl class="space-y-3">
                <div class="flex justify-between">
                    <dt class="text-sm text-gray-600">Nome Completo</dt>
                    <dd class="text-sm font-medium text-gray-900">{{ $utente->nome_completo }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-sm text-gray-600">Email</dt>
                    <dd class="text-sm font-medium text-gray-900">{{ $utente->email }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-sm text-gray-600">Stato Pagamento</dt>
                    <dd>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                            <i class="fas fa-clock mr-1"></i>
                            In Verifica
                        </span>
                    </dd>
                </div>
            </dl>
        </div>

        <!-- Support Info -->
        <div class="bg-gradient-to-r from-purple-50 to-pink-50 rounded-lg p-6 mb-6">
            <h3 class="font-semibold text-gray-900 mb-3 flex items-center">
                <i class="fas fa-headset mr-2 text-purple-600"></i>
                Hai bisogno di supporto?
            </h3>
            <p class="text-sm text-gray-700 mb-3">
                Se hai domande o non ricevi risposta entro 48 ore, contattaci:
            </p>
            <div class="space-y-2">
                <a href="mailto:info@magiadonna.it" class="flex items-center text-sm text-purple-700 hover:text-purple-900">
                    <i class="fas fa-envelope mr-2"></i>
                    info@magiadonna.it
                </a>
                <a href="tel:+390123456789" class="flex items-center text-sm text-purple-700 hover:text-purple-900">
                    <i class="fas fa-phone mr-2"></i>
                    +39 012 345 6789
                </a>
            </div>
        </div>

        <!-- Action Button -->
        <div class="text-center">
            <a href="{{ url('/') }}"
               class="inline-flex items-center px-8 py-3 bg-gradient-to-r from-pink-600 to-purple-600 text-white font-semibold rounded-lg hover:from-pink-700 hover:to-purple-700 transition shadow-lg">
                <i class="fas fa-home mr-2"></i>
                Torna alla Homepage
            </a>
        </div>

    </div>
</div>

<style>
@keyframes bounce {
    0%, 100% {
        transform: translateY(0);
    }
    50% {
        transform: translateY(-10px);
    }
}

.animate-bounce {
    animation: bounce 2s infinite;
}
</style>
@endsection
