@extends('layouts.admin')

@section('titolo', 'Documenti Professionista')

@section('contenuto')
<div class="p-6">
    <div class="mb-6">
        <a href="{{ route('admin.professionisti.show', $professionista->id) }}" class="text-fucsia-magia hover:text-viola-magia mb-2 inline-block">
            <i class="fas fa-arrow-left mr-2"></i> Torna al profilo
        </a>
        <h1 class="text-3xl font-bold text-gray-800">Documenti: {{ $professionista->nome_completo }}</h1>
        <p class="text-gray-600 mt-1">Gestisci certificati, CV e documenti del professionista</p>
    </div>

    <!-- Statistiche -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-sm text-gray-600">Totale Documenti</p>
            <p class="text-2xl font-bold text-gray-800">{{ $statistiche['totale'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-sm text-gray-600">Verificati</p>
            <p class="text-2xl font-bold text-green-600">{{ $statistiche['verificati'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-sm text-gray-600">In Scadenza (30gg)</p>
            <p class="text-2xl font-bold text-orange-600">{{ $statistiche['in_scadenza'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-sm text-gray-600">Scaduti</p>
            <p class="text-2xl font-bold text-red-600">{{ $statistiche['scaduti'] }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Form Upload -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold mb-4">
                    <i class="fas fa-upload mr-2 text-fucsia-magia"></i> Carica Documento
                </h2>

                <form method="POST" action="{{ route('admin.professionisti.documenti.upload', $professionista->id) }}" enctype="multipart/form-data">
                    @csrf

                    <div class="space-y-4">
                        <!-- Tipo Documento -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tipo Documento *</label>
                            <select name="tipo_documento" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-fucsia-magia focus:border-fucsia-magia">
                                <option value="">Seleziona...</option>
                                <option value="certificato">Certificato/Attestato</option>
                                <option value="cv">Curriculum Vitae</option>
                                <option value="carta_identita">Documento d'Identità</option>
                                <option value="contratto">Contratto</option>
                                <option value="altro">Altro Documento</option>
                            </select>
                        </div>

                        <!-- File -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">File *</label>
                            <input type="file" name="file" required accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-fucsia-magia file:text-white hover:file:bg-viola-magia">
                            <p class="text-xs text-gray-500 mt-1">Max 10MB. Formati: PDF, DOC, DOCX, JPG, PNG</p>
                        </div>

                        <!-- Descrizione -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Descrizione</label>
                            <textarea name="descrizione" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-fucsia-magia focus:border-fucsia-magia" placeholder="Breve descrizione del documento..."></textarea>
                        </div>

                        <!-- Data Scadenza -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Data Scadenza (opzionale)</label>
                            <input type="date" name="data_scadenza" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-fucsia-magia focus:border-fucsia-magia">
                            <p class="text-xs text-gray-500 mt-1">Solo per documenti con validità temporale</p>
                        </div>

                        <!-- Submit -->
                        <button type="submit" class="w-full px-4 py-2 bg-fucsia-magia text-white rounded-lg hover:bg-viola-magia">
                            <i class="fas fa-upload mr-2"></i> Carica Documento
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Lista Documenti -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold mb-4">
                    <i class="fas fa-folder-open mr-2 text-fucsia-magia"></i> Documenti Caricati
                </h2>

                @if($professionista->documenti->count() > 0)
                <div class="space-y-3">
                    @foreach($professionista->documenti as $doc)
                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-2">
                                    <i class="fas {{ $doc->icona_file }} text-2xl"></i>
                                    <div class="flex-1">
                                        <h3 class="font-semibold text-gray-900">{{ $doc->nome_file }}</h3>
                                        <p class="text-xs text-gray-500">{{ $doc->dimensione_formattata }} • Caricato il {{ $doc->created_at->format('d/m/Y H:i') }}</p>
                                    </div>
                                </div>

                                <!-- Badges e Info -->
                                <div class="flex flex-wrap gap-2 mb-2">
                                    <span class="px-2 py-1 rounded text-xs {{ $doc->badge_tipo }}">
                                        {{ $doc->tipo_documento_leggibile }}
                                    </span>

                                    @if($doc->verificato)
                                    <span class="px-2 py-1 rounded text-xs bg-green-100 text-green-800">
                                        <i class="fas fa-check-circle mr-1"></i> Verificato
                                    </span>
                                    @else
                                    <span class="px-2 py-1 rounded text-xs bg-yellow-100 text-yellow-800">
                                        <i class="fas fa-clock mr-1"></i> Da verificare
                                    </span>
                                    @endif

                                    @if($doc->data_scadenza)
                                        @php
                                            $giorni = $doc->giorniAllaScadenza();
                                        @endphp
                                        @if($doc->isScaduto())
                                        <span class="px-2 py-1 rounded text-xs bg-red-100 text-red-800">
                                            <i class="fas fa-exclamation-triangle mr-1"></i> Scaduto
                                        </span>
                                        @elseif($giorni !== null && $giorni <= 30)
                                        <span class="px-2 py-1 rounded text-xs bg-orange-100 text-orange-800">
                                            <i class="fas fa-exclamation-circle mr-1"></i> Scade tra {{ $giorni }} giorni
                                        </span>
                                        @else
                                        <span class="px-2 py-1 rounded text-xs bg-gray-100 text-gray-800">
                                            Scadenza: {{ $doc->data_scadenza->format('d/m/Y') }}
                                        </span>
                                        @endif
                                    @endif
                                </div>

                                @if($doc->descrizione)
                                <p class="text-sm text-gray-600 mt-2">{{ $doc->descrizione }}</p>
                                @endif
                            </div>

                            <!-- Azioni -->
                            <div class="flex flex-col gap-2 ml-4">
                                <a href="{{ route('admin.professionisti.documenti.download', [$professionista->id, $doc->id]) }}" class="px-3 py-1 bg-blue-600 text-white text-sm rounded hover:bg-blue-700">
                                    <i class="fas fa-download"></i>
                                </a>

                                @if(!$doc->verificato)
                                <form method="POST" action="{{ route('admin.professionisti.documenti.verifica', [$professionista->id, $doc->id]) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="w-full px-3 py-1 bg-green-600 text-white text-sm rounded hover:bg-green-700">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </form>
                                @else
                                <form method="POST" action="{{ route('admin.professionisti.documenti.revoca-verifica', [$professionista->id, $doc->id]) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="w-full px-3 py-1 bg-yellow-600 text-white text-sm rounded hover:bg-yellow-700">
                                        <i class="fas fa-undo"></i>
                                    </button>
                                </form>
                                @endif

                                <form method="POST" action="{{ route('admin.professionisti.documenti.destroy', [$professionista->id, $doc->id]) }}" class="inline" id="delete-doc-{{ $doc->id }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" onclick="confermaEliminazione('delete-doc-{{ $doc->id }}', 'Eliminare questo documento?', 'Il file sarà rimosso definitivamente.')" class="w-full px-3 py-1 bg-red-600 text-white text-sm rounded hover:bg-red-700">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-12">
                    <i class="fas fa-folder-open text-6xl text-gray-300 mb-4"></i>
                    <p class="text-gray-500">Nessun documento caricato</p>
                    <p class="text-sm text-gray-400 mt-2">Usa il form a sinistra per caricare documenti</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
