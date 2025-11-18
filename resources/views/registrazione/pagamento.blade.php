@extends('layouts.guest')

@section('title', 'Completa il Pagamento')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-pink-50 to-purple-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-3xl mx-auto">

        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Completa la Registrazione</h1>
            <p class="text-gray-600">Scegli il metodo di pagamento per attivare il tuo account</p>
        </div>

        <!-- Alert Success -->
        @if(session('success'))
            <div class="mb-6 bg-green-50 border-l-4 border-green-400 p-4 rounded">
                <div class="flex">
                    <i class="fas fa-check-circle text-green-400 mt-0.5"></i>
                    <p class="ml-3 text-sm text-green-700">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        <!-- Alert Error -->
        @if(session('error'))
            <div class="mb-6 bg-red-50 border-l-4 border-red-400 p-4 rounded">
                <div class="flex">
                    <i class="fas fa-exclamation-circle text-red-400 mt-0.5"></i>
                    <p class="ml-3 text-sm text-red-700">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        <!-- Card Informazioni Utente -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Dati Registrazione</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-500">Nome Completo</p>
                    <p class="font-medium text-gray-900">{{ $utente->nome_completo }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Email</p>
                    <p class="font-medium text-gray-900">{{ $utente->email }}</p>
                </div>
            </div>
        </div>

        <!-- Card Importo -->
        <div class="bg-gradient-to-r from-pink-500 to-purple-600 rounded-lg shadow-sm p-6 mb-8 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-pink-100 text-sm mb-1">{{ $descrizione }}</p>
                    <p class="text-3xl font-bold">€ {{ number_format($importo, 2, ',', '.') }}</p>
                </div>
                <div class="bg-white bg-opacity-20 rounded-full p-4">
                    <i class="fas fa-euro-sign text-3xl"></i>
                </div>
            </div>
        </div>

        <!-- Scelta Metodo Pagamento -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">

            <!-- Opzione 1: PayPal -->
            <div class="bg-white rounded-lg shadow-sm border-2 border-gray-200 hover:border-blue-500 transition-all duration-200 cursor-pointer group">
                <form action="{{ route('pagamento.paypal.iniz ia') }}" method="POST" id="form-paypal">
                    @csrf
                    <input type="hidden" name="utente_id" value="{{ $utente->id }}">
                    <input type="hidden" name="importo" value="{{ $importo }}">

                    <button type="submit" class="w-full p-6 text-left">
                        <div class="flex items-center justify-between mb-4">
                            <div class="bg-blue-100 rounded-lg p-3 group-hover:bg-blue-200 transition">
                                <i class="fab fa-paypal text-3xl text-blue-600"></i>
                            </div>
                            <span class="text-green-600 text-sm font-medium">Immediato</span>
                        </div>

                        <h3 class="text-xl font-bold text-gray-900 mb-2">PayPal</h3>
                        <p class="text-gray-600 text-sm mb-4">Paga con PayPal o carta di credito/debito</p>

                        <ul class="space-y-2 mb-4">
                            <li class="flex items-start text-sm text-gray-600">
                                <i class="fas fa-check text-green-500 mt-1 mr-2"></i>
                                <span>Attivazione account immediata</span>
                            </li>
                            <li class="flex items-start text-sm text-gray-600">
                                <i class="fas fa-check text-green-500 mt-1 mr-2"></i>
                                <span>Pagamento sicuro e protetto</span>
                            </li>
                            <li class="flex items-start text-sm text-gray-600">
                                <i class="fas fa-check text-green-500 mt-1 mr-2"></i>
                                <span>Carta non necessaria</span>
                            </li>
                        </ul>

                        <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                            <span class="text-gray-500 text-sm">Vai al pagamento</span>
                            <i class="fas fa-arrow-right text-blue-600 group-hover:translate-x-1 transition-transform"></i>
                        </div>
                    </button>
                </form>
            </div>

            <!-- Opzione 2: Bonifico Bancario -->
            <div class="bg-white rounded-lg shadow-sm border-2 border-gray-200 hover:border-purple-500 transition-all duration-200 cursor-pointer group">
                <a href="{{ route('pagamento.bonifico.form', $utente->id) }}" class="block p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="bg-purple-100 rounded-lg p-3 group-hover:bg-purple-200 transition">
                            <i class="fas fa-university text-3xl text-purple-600"></i>
                        </div>
                        <span class="text-orange-600 text-sm font-medium">1-2 giorni</span>
                    </div>

                    <h3 class="text-xl font-bold text-gray-900 mb-2">Bonifico Bancario</h3>
                    <p class="text-gray-600 text-sm mb-4">Paga tramite bonifico bancario</p>

                    <ul class="space-y-2 mb-4">
                        <li class="flex items-start text-sm text-gray-600">
                            <i class="fas fa-check text-green-500 mt-1 mr-2"></i>
                            <span>Ricevi coordinate bancarie</span>
                        </li>
                        <li class="flex items-start text-sm text-gray-600">
                            <i class="fas fa-check text-green-500 mt-1 mr-2"></i>
                            <span>Carica ricevuta di pagamento</span>
                        </li>
                        <li class="flex items-start text-sm text-gray-600">
                            <i class="fas fa-check text-green-500 mt-1 mr-2"></i>
                            <span>Verifica entro 24-48h</span>
                        </li>
                    </ul>

                    <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                        <span class="text-gray-500 text-sm">Vedi coordinate</span>
                        <i class="fas fa-arrow-right text-purple-600 group-hover:translate-x-1 transition-transform"></i>
                    </div>
                </a>
            </div>

        </div>

        <!-- Info Sicurezza -->
        <div class="bg-blue-50 rounded-lg p-4 mb-6">
            <div class="flex items-start">
                <i class="fas fa-shield-alt text-blue-500 mt-1 mr-3"></i>
                <div>
                    <h4 class="font-semibold text-blue-900 mb-1">Pagamento Sicuro</h4>
                    <p class="text-sm text-blue-700">Tutti i pagamenti sono protetti con crittografia SSL. I tuoi dati finanziari sono al sicuro.</p>
                </div>
            </div>
        </div>

        <!-- Link Supporto -->
        <div class="text-center">
            <p class="text-sm text-gray-600">
                Hai bisogno di aiuto?
                <a href="mailto:info@magiadonna.it" class="text-pink-600 hover:text-pink-700 font-medium">Contatta il supporto</a>
            </p>
        </div>

    </div>
</div>
@endsection
