@extends('layouts.admin')

@section('titolo', 'Documenti Cliente')

@section('contenuto')
<div class="p-6">

    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Documenti di {{ $cliente->nome_completo }}</h2>
            <p class="text-gray-600 mt-1">Gestisci certificati medici, documenti d'identità e consensi</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.clienti.documenti.create', $cliente->id) }}" class="px-6 py-3 bg-gradient-to-r from-viola-magia to-fucsia-magia text-white font-semibold rounded-lg hover:shadow-lg transition">
                <i class="fas fa-upload mr-2"></i>Carica Documento
            </a>
            <a href="{{ route('admin.clienti.show', $cliente->id) }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                <i class="fas fa-arrow-left mr-2"></i>Torna al Cliente
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-lg">
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-3"></i>
                <p>{{ session('success') }}</p>
            </div>
        </div>
    @endif

    <!-- Documenti -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($cliente->documenti()->orderBy('created_at', 'desc')->get() as $documento)
            <div class="bg-white rounded-lg shadow hover:shadow-xl transition overflow-hidden">

                <!-- Header Documento -->
                <div class="bg-gradient-to-r from-viola-magia to-fucsia-magia px-5 py-4">
                    <div class="flex items-center justify-between text-white">
                        <div class="flex items-center gap-3">
                            <i class="fas {{ $documento->icona }} text-2xl"></i>
                            <div>
                                <p class="font-semibold">{{ ucfirst(str_replace('_', ' ', $documento->tipo_documento)) }}</p>
                                <p class="text-xs opacity-90">{{ $documento->created_at->format('d/m/Y') }}</p>
                            </div>
                        </div>
                        @if($documento->data_scadenza)
                            @php
                                $giorni_scadenza = now()->diffInDays($documento->data_scadenza, false);
                                $scaduto = $giorni_scadenza < 0;
                                $in_scadenza = $giorni_scadenza >= 0 && $giorni_scadenza <= 30;
                            @endphp
                            @if($scaduto)
                                <i class="fas fa-exclamation-triangle text-yellow-300" title="Scaduto"></i>
                            @elseif($in_scadenza)
                                <i class="fas fa-clock text-yellow-300" title="In scadenza"></i>
                            @endif
                        @endif
                    </div>
                </div>

                <!-- Corpo Documento -->
                <div class="p-5">

                    <!-- Nome File -->
                    <p class="font-medium text-gray-900 mb-2 truncate">{{ $documento->nome_originale }}</p>

                    <!-- Descrizione -->
                    @if($documento->descrizione)
                        <p class="text-sm text-gray-600 mb-3 line-clamp-2">{{ $documento->descrizione }}</p>
                    @endif

                    <!-- Info Documento -->
                    <div class="space-y-2 mb-4 text-sm">
                        @if($documento->data_scadenza)
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600">Scadenza:</span>
                                <span class="font-medium {{ $scaduto ? 'text-red-600' : ($in_scadenza ? 'text-yellow-600' : 'text-gray-900') }}">
                                    {{ $documento->data_scadenza->format('d/m/Y') }}
                                    @if($scaduto)
                                        (Scaduto)
                                    @elseif($in_scadenza)
                                        ({{ $giorni_scadenza }} giorni)
                                    @endif
                                </span>
                            </div>
                        @endif

                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">Dimensione:</span>
                            <span class="text-gray-900">{{ number_format($documento->dimensione / 1024, 2) }} KB</span>
                        </div>

                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">Stato:</span>
                            <span class="px-2 py-1 bg-{{ $documento->stato === 'valido' ? 'green' : 'red' }}-100 text-{{ $documento->stato === 'valido' ? 'green' : 'red' }}-800 text-xs rounded-full">
                                {{ ucfirst($documento->stato) }}
                            </span>
                        </div>

                        @if($documento->visibile_cliente)
                            <div class="flex items-center text-blue-600 text-xs">
                                <i class="fas fa-eye mr-1"></i>
                                Visibile al cliente
                            </div>
                        @endif
                    </div>

                    <!-- Azioni -->
                    <div class="flex gap-2">
                        <a href="{{ route('admin.documenti.download', $documento->id) }}" class="flex-1 px-4 py-2 bg-blue-100 text-blue-700 text-sm font-medium rounded-lg hover:bg-blue-200 transition text-center">
                            <i class="fas fa-download mr-1"></i>Scarica
                        </a>
                        <a href="{{ route('admin.documenti.edit', $documento->id) }}" class="flex-1 px-4 py-2 bg-yellow-100 text-yellow-700 text-sm font-medium rounded-lg hover:bg-yellow-200 transition text-center">
                            <i class="fas fa-edit mr-1"></i>Modifica
                        </a>
                        <form action="{{ route('admin.documenti.destroy', $documento->id) }}" method="POST" class="flex-1" onsubmit="return confirm('Sei sicuro?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full px-4 py-2 bg-red-100 text-red-700 text-sm font-medium rounded-lg hover:bg-red-200 transition">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="md:col-span-3 bg-white rounded-lg shadow p-12 text-center">
                <i class="fas fa-folder-open text-6xl text-gray-300 mb-4"></i>
                <p class="text-lg font-medium text-gray-600 mb-2">Nessun documento caricato</p>
                <p class="text-sm text-gray-500 mb-6">Carica il primo documento per questo cliente</p>
                <a href="{{ route('admin.clienti.documenti.create', $cliente->id) }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-viola-magia to-fucsia-magia text-white rounded-lg hover:shadow-lg transition">
                    <i class="fas fa-upload mr-2"></i>Carica Documento
                </a>
            </div>
        @endforelse
    </div>

</div>
@endsection
