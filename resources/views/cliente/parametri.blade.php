@extends('layouts.cliente')

@section('title', 'Parametri Corporei')

@section('content')
<div class="p-6">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">
            <i class="fas fa-weight text-purple-600 mr-3"></i>Parametri Corporei
        </h1>
        <p class="text-gray-600">Monitora la tua evoluzione fisica</p>
    </div>

    <!-- Pulsante Nuova Rilevazione -->
    <div class="mb-6">
        <a href="{{ route('cliente.parametri.create') }}"
           class="inline-block bg-gradient-to-r from-purple-600 to-pink-600 text-white px-6 py-3 rounded-lg font-semibold hover:shadow-lg transition">
            <i class="fas fa-plus mr-2"></i>Nuova Rilevazione
        </a>
    </div>

    @if($parametri->count() > 0)
        <!-- Grafici Evoluzione -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h3 class="font-bold text-gray-800 mb-4">
                    <i class="fas fa-chart-line text-purple-600 mr-2"></i>Evoluzione Peso
                </h3>
                <div class="h-64 flex items-center justify-center text-gray-400">
                    <p>Grafico peso (da implementare con Chart.js)</p>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-6">
                <h3 class="font-bold text-gray-800 mb-4">
                    <i class="fas fa-chart-line text-pink-600 mr-2"></i>Massa Grassa vs Magra
                </h3>
                <div class="h-64 flex items-center justify-center text-gray-400">
                    <p>Grafico composizione (da implementare con Chart.js)</p>
                </div>
            </div>
        </div>

        <!-- Storico Rilevazioni -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="p-6 border-b">
                <h2 class="text-2xl font-bold text-gray-800">
                    <i class="fas fa-history text-purple-600 mr-2"></i>Storico Rilevazioni
                </h2>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Data</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Peso (kg)</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Massa Grassa (%)</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Massa Magra (%)</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Azioni</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($parametri as $param)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="font-semibold">{{ \Carbon\Carbon::parse($param->data_rilevazione)->format('d/m/Y') }}</span>
                                </td>
                                <td class="px-6 py-4">{{ $param->peso ?? '-' }}</td>
                                <td class="px-6 py-4">{{ $param->massa_grassa ?? '-' }}</td>
                                <td class="px-6 py-4">{{ $param->massa_magra ?? '-' }}</td>
                                <td class="px-6 py-4">
                                    <a href="{{ route('cliente.parametri.show', $param->id) }}"
                                       class="text-purple-600 hover:text-purple-800 mr-3">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-4 bg-gray-50">
                {{ $parametri->links() }}
            </div>
        </div>
    @else
        <div class="bg-white rounded-xl shadow-lg p-12 text-center">
            <i class="fas fa-chart-line text-6xl text-gray-300 mb-4"></i>
            <h3 class="text-xl font-bold text-gray-700 mb-2">Nessuna Rilevazione</h3>
            <p class="text-gray-600 mb-6">Non hai ancora effettuato rilevazioni dei parametri corporei</p>
            <a href="{{ route('cliente.parametri.create') }}"
               class="inline-block bg-purple-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-purple-700 transition">
                <i class="fas fa-plus mr-2"></i>Prima Rilevazione
            </a>
        </div>
    @endif
</div>
@endsection
