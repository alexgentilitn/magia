@extends('layouts.admin')

@section('titolo', 'Risultati Importazione')

@section('contenuto')
<div class="p-6">

    <!-- Header -->
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Importazione Completata</h2>
        <p class="text-gray-600 mt-1">Riepilogo dell'importazione pesate</p>
    </div>

    <!-- Risultati -->
    <div class="bg-white rounded-lg shadow p-8 mb-6">

        <!-- Success Message -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-green-100 rounded-full mb-4">
                <i class="fas fa-check-circle text-green-600 text-4xl"></i>
            </div>
            <h3 class="text-2xl font-bold text-gray-800 mb-2">Importazione Completata con Successo!</h3>
            <p class="text-gray-600">Le pesate sono state importate nel database</p>
        </div>

        <!-- Statistiche -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div class="bg-green-50 border-l-4 border-green-500 p-6 rounded-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-green-700 font-medium mb-1">Pesate Importate</p>
                        <p class="text-4xl font-bold text-green-900">{{ $import_results['imported'] }}</p>
                    </div>
                    <i class="fas fa-check-circle text-green-400 text-5xl"></i>
                </div>
            </div>

            @if($import_results['skipped'] > 0)
                <div class="bg-yellow-50 border-l-4 border-yellow-500 p-6 rounded-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-yellow-700 font-medium mb-1">Righe Saltate</p>
                            <p class="text-4xl font-bold text-yellow-900">{{ $import_results['skipped'] }}</p>
                        </div>
                        <i class="fas fa-exclamation-triangle text-yellow-400 text-5xl"></i>
                    </div>
                </div>
            @endif
        </div>

        <!-- Errori (se presenti) -->
        @if(!empty($import_results['errors']))
            <div class="bg-yellow-50 border-l-4 border-yellow-500 p-6 rounded-lg mb-6">
                <h4 class="font-bold text-yellow-900 mb-3 flex items-center">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    Dettaglio Errori ({{ count($import_results['errors']) }})
                </h4>
                <div class="bg-white rounded p-4 max-h-60 overflow-y-auto">
                    <ul class="space-y-2">
                        @foreach($import_results['errors'] as $error)
                            <li class="text-sm text-yellow-800">
                                <i class="fas fa-chevron-right text-yellow-600 mr-2"></i>
                                {{ $error }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <!-- Azioni -->
        <div class="flex flex-col md:flex-row gap-4 mt-8">
            <a href="{{ route('admin.clienti-pesate.index') }}"
               class="flex-1 px-6 py-3 bg-gradient-to-r from-viola-magia to-fucsia-magia text-white font-semibold rounded-lg hover:shadow-lg transition text-center">
                <i class="fas fa-list mr-2"></i>
                Vai alla Lista Clienti con Pesate
            </a>
            <a href="{{ route('pesate.import') }}"
               class="flex-1 px-6 py-3 bg-gray-200 text-gray-700 font-semibold rounded-lg hover:bg-gray-300 transition text-center">
                <i class="fas fa-upload mr-2"></i>
                Importa Altre Pesate
            </a>
        </div>
    </div>

    <!-- Info Box -->
    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-lg">
        <div class="flex">
            <i class="fas fa-info-circle text-blue-600 text-xl mr-3"></i>
            <div>
                <p class="font-bold text-blue-900 mb-1">Cosa fare ora?</p>
                <ul class="list-disc list-inside text-blue-800 text-sm space-y-1">
                    <li>Visualizza le pesate importate dalla lista clienti</li>
                    <li>Controlla i grafici di evoluzione per ogni cliente</li>
                    <li>Modifica eventuali dati importati in modo errato</li>
                    <li>Importa pesate di altre sedi se necessario</li>
                </ul>
            </div>
        </div>
    </div>

</div>
@endsection
