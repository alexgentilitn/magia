@extends('layouts.cliente')

@section('title', 'I Miei Pagamenti')

@section('content')
<div class="p-6">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">
            <i class="fas fa-credit-card text-purple-600 mr-3"></i>I Miei Pagamenti
        </h1>
        <p class="text-gray-600">Storico e gestione dei tuoi pagamenti</p>
    </div>

    <!-- Riepilogo Finanziario -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-gradient-to-br from-green-400 to-green-600 rounded-xl p-6 text-white shadow-lg">
            <div class="flex items-center justify-between mb-2">
                <p class="text-green-100">Totale Pagato</p>
                <i class="fas fa-check-circle text-2xl opacity-80"></i>
            </div>
            <p class="text-3xl font-bold">€ {{ number_format($totalePagato ?? 0, 2, ',', '.') }}</p>
        </div>

        <div class="bg-gradient-to-br from-orange-400 to-orange-600 rounded-xl p-6 text-white shadow-lg">
            <div class="flex items-center justify-between mb-2">
                <p class="text-orange-100">In Sospeso</p>
                <i class="fas fa-clock text-2xl opacity-80"></i>
            </div>
            <p class="text-3xl font-bold">€ {{ number_format($totaleInSospeso ?? 0, 2, ',', '.') }}</p>
        </div>

        <div class="bg-gradient-to-br from-purple-400 to-purple-600 rounded-xl p-6 text-white shadow-lg">
            <div class="flex items-center justify-between mb-2">
                <p class="text-purple-100">N° Transazioni</p>
                <i class="fas fa-receipt text-2xl opacity-80"></i>
            </div>
            <p class="text-3xl font-bold">{{ $numeroTransazioni ?? 0 }}</p>
        </div>
    </div>

    <!-- Pagamenti in Sospeso -->
    @if(isset($pagamentiInSospeso) && $pagamentiInSospeso->count() > 0)
        <div class="bg-orange-50 border-l-4 border-orange-500 rounded-lg p-6 mb-8">
            <h3 class="font-bold text-orange-800 mb-4">
                <i class="fas fa-exclamation-triangle mr-2"></i>Pagamenti in Sospeso
            </h3>
            <div class="space-y-3">
                @foreach($pagamentiInSospeso as $pagamento)
                    <div class="bg-white rounded-lg p-4 flex justify-between items-center">
                        <div>
                            <p class="font-semibold text-gray-800">{{ $pagamento->descrizione ?? 'Pagamento' }}</p>
                            <p class="text-sm text-gray-600">Scadenza: {{ \Carbon\Carbon::parse($pagamento->data_scadenza)->format('d/m/Y') }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-xl font-bold text-orange-600">€ {{ number_format($pagamento->importo, 2, ',', '.') }}</p>
                            <a href="{{ route('paypal.checkout', ['ordine_id' => $pagamento->id]) }}"
                               class="inline-block mt-2 bg-orange-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-orange-700 transition">
                                <i class="fab fa-paypal mr-1"></i>Paga Ora
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Storico Pagamenti -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="p-6 border-b bg-gray-50">
            <h2 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-history text-purple-600 mr-2"></i>Storico Pagamenti
            </h2>
        </div>

        @if(isset($pagamenti) && $pagamenti->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Data</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Descrizione</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Metodo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Importo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stato</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Azioni</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($pagamenti as $pag)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="font-semibold">{{ \Carbon\Carbon::parse($pag->data_pagamento ?? $pag->created_at)->format('d/m/Y') }}</span>
                                    <br>
                                    <span class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($pag->data_pagamento ?? $pag->created_at)->format('H:i') }}</span>
                                </td>

                                <td class="px-6 py-4">
                                    <p class="font-semibold text-gray-800">{{ $pag->descrizione ?? 'Servizi MA.GIA DONNA' }}</p>
                                    @if($pag->numero_ordine)
                                        <p class="text-xs text-gray-500">Ordine: {{ $pag->numero_ordine }}</p>
                                    @endif
                                </td>

                                <td class="px-6 py-4">
                                    @if($pag->metodo_pagamento === 'paypal')
                                        <span class="inline-flex items-center">
                                            <i class="fab fa-paypal text-blue-600 mr-1"></i>PayPal
                                        </span>
                                    @elseif($pag->metodo_pagamento === 'carta')
                                        <span class="inline-flex items-center">
                                            <i class="fas fa-credit-card text-purple-600 mr-1"></i>Carta
                                        </span>
                                    @else
                                        <span class="text-gray-600">{{ $pag->metodo_pagamento ?? 'Altro' }}</span>
                                    @endif
                                </td>

                                <td class="px-6 py-4">
                                    <span class="text-lg font-bold text-gray-800">€ {{ number_format($pag->importo, 2, ',', '.') }}</span>
                                </td>

                                <td class="px-6 py-4">
                                    @if($pag->stato_pagamento === 'completato')
                                        <span class="inline-block bg-green-100 text-green-800 px-3 py-1 rounded-full text-xs font-semibold">
                                            <i class="fas fa-check-circle mr-1"></i>Completato
                                        </span>
                                    @elseif($pag->stato_pagamento === 'in_attesa')
                                        <span class="inline-block bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-xs font-semibold">
                                            <i class="fas fa-clock mr-1"></i>In Attesa
                                        </span>
                                    @elseif($pag->stato_pagamento === 'rimborsato')
                                        <span class="inline-block bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-xs font-semibold">
                                            <i class="fas fa-undo mr-1"></i>Rimborsato
                                        </span>
                                    @elseif($pag->stato_pagamento === 'fallito')
                                        <span class="inline-block bg-red-100 text-red-800 px-3 py-1 rounded-full text-xs font-semibold">
                                            <i class="fas fa-times-circle mr-1"></i>Fallito
                                        </span>
                                    @endif
                                </td>

                                <td class="px-6 py-4">
                                    <a href="#" class="text-purple-600 hover:text-purple-800 mr-3" title="Visualizza ricevuta">
                                        <i class="fas fa-file-invoice"></i>
                                    </a>
                                    <a href="#" class="text-blue-600 hover:text-blue-800" title="Scarica PDF">
                                        <i class="fas fa-download"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="p-4 bg-gray-50">
                {{ $pagamenti->links() }}
            </div>
        @else
            <div class="p-12 text-center">
                <i class="fas fa-receipt text-6xl text-gray-300 mb-4"></i>
                <h3 class="text-xl font-bold text-gray-700 mb-2">Nessun Pagamento</h3>
                <p class="text-gray-600">Non hai ancora effettuato pagamenti</p>
            </div>
        @endif
    </div>

    <!-- Info Metodi di Pagamento -->
    <div class="mt-8 bg-gradient-to-br from-purple-50 to-pink-50 rounded-xl p-6">
        <h3 class="font-bold text-gray-800 mb-4">
            <i class="fas fa-info-circle text-purple-600 mr-2"></i>Metodi di Pagamento Accettati
        </h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white rounded-lg p-4 text-center">
                <i class="fab fa-paypal text-3xl text-blue-600 mb-2"></i>
                <p class="text-sm font-semibold">PayPal</p>
            </div>
            <div class="bg-white rounded-lg p-4 text-center">
                <i class="fas fa-credit-card text-3xl text-purple-600 mb-2"></i>
                <p class="text-sm font-semibold">Carte</p>
            </div>
            <div class="bg-white rounded-lg p-4 text-center">
                <i class="fas fa-university text-3xl text-green-600 mb-2"></i>
                <p class="text-sm font-semibold">Bonifico</p>
            </div>
            <div class="bg-white rounded-lg p-4 text-center">
                <i class="fas fa-money-bill-wave text-3xl text-orange-600 mb-2"></i>
                <p class="text-sm font-semibold">Contanti</p>
            </div>
        </div>
    </div>

</div>
@endsection
