@extends('layouts.admin')

@section('titolo', 'Galleria Foto')

@section('contenuto')
<div class="p-6">
    <div class="mb-6">
        <a href="{{ route('admin.professionisti.show', $professionista->id) }}" class="text-fucsia-magia hover:text-viola-magia mb-2 inline-block">
            <i class="fas fa-arrow-left mr-2"></i> Torna al profilo
        </a>
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Galleria Foto: {{ $professionista->nome_completo }}</h1>
                <p class="text-gray-600 mt-1">Gestisci le foto della galleria professionista</p>
            </div>
            <div class="text-right">
                @if(count($galleria) > 0)
                <a href="{{ route('admin.professionisti.galleria.download-all', $professionista->id) }}" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    <i class="fas fa-download mr-2"></i> Scarica Tutte (ZIP)
                </a>
                @endif
            </div>
        </div>
    </div>

    <!-- Form Upload Multiplo -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-xl font-bold mb-4">
            <i class="fas fa-cloud-upload-alt mr-2 text-fucsia-magia"></i> Carica Nuove Foto
        </h2>

        <form method="POST" action="{{ route('admin.professionisti.galleria.upload', $professionista->id) }}" enctype="multipart/form-data">
            @csrf

            <div class="flex gap-4 items-end">
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Seleziona Foto (max 10)</label>
                    <input type="file" name="foto[]" multiple required accept="image/jpeg,image/png,image/jpg" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-fucsia-magia file:text-white hover:file:bg-viola-magia">
                    <p class="text-xs text-gray-500 mt-1">Formati: JPG, PNG (max 5MB per foto)</p>
                </div>
                <button type="submit" class="px-6 py-2 bg-fucsia-magia text-white rounded-lg hover:bg-viola-magia">
                    <i class="fas fa-upload mr-2"></i> Carica
                </button>
            </div>
        </form>
    </div>

    <!-- Galleria Foto -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-bold mb-4">
            <i class="fas fa-images mr-2 text-fucsia-magia"></i> Foto Galleria ({{ count($galleria) }})
        </h2>

        @if(count($galleria) > 0)
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            @foreach($galleria as $index => $foto)
            <div class="relative group">
                <!-- Foto -->
                <div class="aspect-square rounded-lg overflow-hidden bg-gray-100 shadow-md">
                    <img src="{{ asset('storage/' . $foto['path']) }}" alt="Foto {{ $index + 1 }}" class="w-full h-full object-cover">
                </div>

                <!-- Overlay Azioni -->
                <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-60 transition-all duration-200 rounded-lg flex items-center justify-center opacity-0 group-hover:opacity-100">
                    <div class="flex gap-2">
                        <!-- Imposta come Copertina -->
                        <form method="POST" action="{{ route('admin.professionisti.galleria.imposta-copertina', [$professionista->id, $index]) }}" class="inline">
                            @csrf
                            <button type="submit" class="px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm" title="Usa come foto profilo">
                                <i class="fas fa-star"></i>
                            </button>
                        </form>

                        <!-- Visualizza a schermo intero -->
                        <a href="{{ asset('storage/' . $foto['path']) }}" target="_blank" class="px-3 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm" title="Visualizza">
                            <i class="fas fa-search-plus"></i>
                        </a>

                        <!-- Elimina -->
                        <form method="POST" action="{{ route('admin.professionisti.galleria.destroy', [$professionista->id, $index]) }}" class="inline" id="delete-foto-{{ $index }}">
                            @csrf
                            @method('DELETE')
                            <button type="button" onclick="confermaEliminazione('delete-foto-{{ $index }}', 'Eliminare questa foto?', 'La foto sarà rimossa definitivamente dalla galleria.')" class="px-3 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 text-sm" title="Elimina">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Info Foto -->
                <div class="mt-2 text-xs text-gray-600">
                    <p class="truncate">{{ $foto['nome_file'] ?? 'Foto ' . ($index + 1) }}</p>
                    @if(isset($foto['caricato_il']))
                    <p class="text-gray-400">{{ \Carbon\Carbon::parse($foto['caricato_il'])->format('d/m/Y H:i') }}</p>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-16">
            <i class="fas fa-images text-6xl text-gray-300 mb-4"></i>
            <p class="text-gray-500 text-lg">Nessuna foto in galleria</p>
            <p class="text-sm text-gray-400 mt-2">Usa il form sopra per caricare le prime foto</p>
        </div>
        @endif
    </div>
</div>

<!-- Modal Lightbox (opzionale per visualizzazione foto) -->
<style>
.aspect-square {
    aspect-ratio: 1 / 1;
}
</style>

@endsection
