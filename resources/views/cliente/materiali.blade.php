@extends('layouts.cliente')

@section('title', 'Materiali Didattici')

@section('content')
<div class="p-6">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">
            <i class="fas fa-book text-purple-600 mr-3"></i>Materiali Didattici
        </h1>
        <p class="text-gray-600">Accedi alle tue schede di allenamento e documenti</p>
    </div>

    <!-- Schede Allenamento -->
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-gray-800 mb-4">
            <i class="fas fa-dumbbell text-purple-600 mr-2"></i>Schede di Allenamento
        </h2>

        @if(isset($schedeAllenamento) && $schedeAllenamento->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($schedeAllenamento as $scheda)
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition">
                        <div class="bg-gradient-to-r from-purple-500 to-pink-500 p-4 text-white">
                            <h3 class="font-bold text-lg">{{ $scheda->nome }}</h3>
                            <p class="text-sm opacity-90">{{ $scheda->tipologia }}</p>
                        </div>

                        <div class="p-6">
                            <div class="space-y-2 text-sm text-gray-600 mb-4">
                                <p><i class="fas fa-calendar text-purple-600 w-5"></i> Creata: {{ \Carbon\Carbon::parse($scheda->created_at)->format('d/m/Y') }}</p>
                                <p><i class="fas fa-user-tie text-purple-600 w-5"></i> Istruttore: {{ $scheda->professionista->nome ?? 'Staff' }}</p>
                                @if($scheda->validita_fino)
                                    <p><i class="fas fa-clock text-purple-600 w-5"></i> Valida fino: {{ \Carbon\Carbon::parse($scheda->validita_fino)->format('d/m/Y') }}</p>
                                @endif
                            </div>

                            <div class="flex gap-2">
                                <a href="{{ route('cliente.materiali.scheda', $scheda->id) }}"
                                   class="flex-1 text-center bg-purple-100 text-purple-700 px-4 py-2 rounded-lg text-sm font-semibold hover:bg-purple-200 transition">
                                    <i class="fas fa-eye mr-1"></i>Visualizza
                                </a>
                                <a href="{{ route('cliente.materiali.scheda.pdf', $scheda->id) }}"
                                   class="bg-pink-100 text-pink-700 px-4 py-2 rounded-lg text-sm font-semibold hover:bg-pink-200 transition">
                                    <i class="fas fa-download"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-white rounded-xl shadow-lg p-12 text-center">
                <i class="fas fa-dumbbell text-6xl text-gray-300 mb-4"></i>
                <h3 class="text-xl font-bold text-gray-700 mb-2">Nessuna Scheda Disponibile</h3>
                <p class="text-gray-600">Il tuo istruttore non ha ancora creato schede di allenamento per te</p>
            </div>
        @endif
    </div>

    <!-- Documenti e Materiali -->
    <div>
        <h2 class="text-2xl font-bold text-gray-800 mb-4">
            <i class="fas fa-file-alt text-purple-600 mr-2"></i>Altri Documenti
        </h2>

        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                    <a href="#" class="flex items-center p-4 border border-gray-200 rounded-lg hover:border-purple-300 hover:bg-purple-50 transition">
                        <div class="bg-blue-100 rounded-full p-3 mr-4">
                            <i class="fas fa-file-pdf text-blue-600 text-2xl"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800">Regolamento Centro</p>
                            <p class="text-sm text-gray-500">PDF - 245 KB</p>
                        </div>
                    </a>

                    <a href="#" class="flex items-center p-4 border border-gray-200 rounded-lg hover:border-purple-300 hover:bg-purple-50 transition">
                        <div class="bg-green-100 rounded-full p-3 mr-4">
                            <i class="fas fa-file-pdf text-green-600 text-2xl"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800">Consigli Nutrizionali</p>
                            <p class="text-sm text-gray-500">PDF - 1.2 MB</p>
                        </div>
                    </a>

                    <a href="#" class="flex items-center p-4 border border-gray-200 rounded-lg hover:border-purple-300 hover:bg-purple-50 transition">
                        <div class="bg-purple-100 rounded-full p-3 mr-4">
                            <i class="fas fa-video text-purple-600 text-2xl"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800">Video Tutorial Esercizi</p>
                            <p class="text-sm text-gray-500">Playlist YouTube</p>
                        </div>
                    </a>

                    <a href="#" class="flex items-center p-4 border border-gray-200 rounded-lg hover:border-purple-300 hover:bg-purple-50 transition">
                        <div class="bg-orange-100 rounded-full p-3 mr-4">
                            <i class="fas fa-file-pdf text-orange-600 text-2xl"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800">Programma Benessere</p>
                            <p class="text-sm text-gray-500">PDF - 890 KB</p>
                        </div>
                    </a>

                </div>
            </div>
        </div>
    </div>

</div>
@endsection
