@extends('layouts.admin')

@section('titolo', 'Impostazioni Sistema')

@section('contenuto')
<div class="p-6 max-w-7xl mx-auto">

    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">
            <i class="fas fa-cogs text-fucsia-magia mr-3"></i>
            Impostazioni Sistema
        </h1>
        <p class="text-gray-600">Gestisci i valori configurabili utilizzati in tutto il sistema</p>
    </div>

    <!-- Categorie -->
    @foreach($categorie as $catKey => $catInfo)
        @if(isset($impostazioni[$catKey]))
        <div class="bg-white rounded-lg shadow-lg mb-6 overflow-hidden">
            
            <!-- Header Categoria -->
            <div class="bg-gradient-to-r from-viola-magia to-fucsia-magia p-4 flex items-center justify-between">
                <div class="flex items-center text-white">
                    <i class="fas {{ $catInfo['icona'] }} text-2xl mr-3"></i>
                    <div>
                        <h2 class="text-xl font-bold">{{ $catInfo['nome'] }}</h2>
                        <p class="text-sm opacity-90">{{ $catInfo['descrizione'] }}</p>
                    </div>
                </div>
                <a href="{{ route('admin.impostazioni-sistema.create', ['categoria' => $catKey]) }}"
                   class="px-4 py-2 bg-white text-viola-magia rounded-lg hover:bg-gray-100 transition font-medium">
                    <i class="fas fa-plus mr-2"></i>Aggiungi
                </a>
            </div>

            <!-- Tabella Impostazioni -->
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Chiave</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Etichetta</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Colore/Icona</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Ordine</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Stato</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Sistema</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Azioni</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($impostazioni[$catKey] as $imp)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 text-sm font-mono text-gray-700">{{ $imp->chiave }}</td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $imp->etichetta }}</div>
                                @if($imp->descrizione)
                                <div class="text-xs text-gray-500 mt-1">{{ Str::limit($imp->descrizione, 50) }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-2">
                                    @if($imp->colore)
                                    <div class="w-6 h-6 rounded border border-gray-300" style="background-color: {{ $imp->colore }}"></div>
                                    @endif
                                    @if($imp->icona)
                                    <i class="fas {{ $imp->icona }} text-gray-600"></i>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center text-sm text-gray-700">{{ $imp->ordinamento }}</td>
                            <td class="px-6 py-4 text-center">
                                @if($imp->attivo)
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                    Attivo
                                </span>
                                @else
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                    Disattivo
                                </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($imp->di_sistema)
                                <i class="fas fa-lock text-gray-400" title="Impostazione di sistema"></i>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right space-x-2">
                                <form action="{{ route('admin.impostazioni-sistema.toggle', $imp->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-gray-600 hover:text-gray-900" title="{{ $imp->attivo ? 'Disattiva' : 'Attiva' }}">
                                        <i class="fas fa-{{ $imp->attivo ? 'eye-slash' : 'eye' }}"></i>
                                    </button>
                                </form>
                                <a href="{{ route('admin.impostazioni-sistema.edit', $imp->id) }}" class="text-blue-600 hover:text-blue-900">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @if(!$imp->di_sistema)
                                <form action="{{ route('admin.impostazioni-sistema.destroy', $imp->id) }}" method="POST" class="inline" id="delete-form-{{ $imp->id }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="text-red-600 hover:text-red-900"
                                            onclick="confermaEliminazione('delete-form-{{ $imp->id }}', 'Eliminare l\'impostazione?', 'L\'impostazione {{ $imp->chiave }} sarà eliminata definitivamente.')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        </div>
        @endif
    @endforeach

</div>
@endsection
