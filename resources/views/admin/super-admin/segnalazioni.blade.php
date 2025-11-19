@extends('layouts.admin')

@section('titolo', 'Super Admin - Gestione Segnalazioni')

@section('contenuto')
<div class="p-6 max-w-7xl mx-auto">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 flex items-center">
                <i class="fas fa-flag text-red-600 mr-3"></i>
                Gestione Segnalazioni
            </h1>
            <p class="text-gray-600 mt-1">Tutte le segnalazioni bug/suggerimenti degli utenti</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.super-admin.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg transition">
                <i class="fas fa-arrow-left mr-2"></i>Torna a Super Admin
            </a>
        </div>
    </div>

    <!-- Statistiche -->
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-4 mb-6">
        <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-4 rounded-lg border border-blue-200">
            <div class="text-center">
                <p class="text-xs text-blue-600 font-medium uppercase">Totale</p>
                <p class="text-2xl font-bold text-blue-900">{{ $stats['totale'] }}</p>
            </div>
        </div>

        <div class="bg-gradient-to-br from-red-50 to-red-100 p-4 rounded-lg border border-red-200">
            <div class="text-center">
                <p class="text-xs text-red-600 font-medium uppercase">Aperte</p>
                <p class="text-2xl font-bold text-red-900">{{ $stats['aperte'] }}</p>
            </div>
        </div>

        <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 p-4 rounded-lg border border-yellow-200">
            <div class="text-center">
                <p class="text-xs text-yellow-600 font-medium uppercase">In Lavorazione</p>
                <p class="text-2xl font-bold text-yellow-900">{{ $stats['in_lavorazione'] }}</p>
            </div>
        </div>

        <div class="bg-gradient-to-br from-green-50 to-green-100 p-4 rounded-lg border border-green-200">
            <div class="text-center">
                <p class="text-xs text-green-600 font-medium uppercase">Risolte</p>
                <p class="text-2xl font-bold text-green-900">{{ $stats['risolte'] }}</p>
            </div>
        </div>

        <div class="bg-gradient-to-br from-purple-50 to-purple-100 p-4 rounded-lg border border-purple-200">
            <div class="text-center">
                <p class="text-xs text-purple-600 font-medium uppercase">Bug</p>
                <p class="text-2xl font-bold text-purple-900">{{ $stats['bug'] }}</p>
            </div>
        </div>

        <div class="bg-gradient-to-br from-cyan-50 to-cyan-100 p-4 rounded-lg border border-cyan-200">
            <div class="text-center">
                <p class="text-xs text-cyan-600 font-medium uppercase">Suggerimenti</p>
                <p class="text-2xl font-bold text-cyan-900">{{ $stats['suggerimenti'] }}</p>
            </div>
        </div>

        <div class="bg-gradient-to-br from-orange-50 to-orange-100 p-4 rounded-lg border border-orange-200">
            <div class="text-center">
                <p class="text-xs text-orange-600 font-medium uppercase">Alta Priorità</p>
                <p class="text-2xl font-bold text-orange-900">{{ $stats['alta_priorita'] }}</p>
            </div>
        </div>
    </div>

    <!-- Filtri -->
    <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-filter text-purple-600 mr-2"></i>
            Filtri
        </h3>

        <form method="GET" action="{{ route('admin.super-admin.segnalazioni') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Ricerca -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Ricerca</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Titolo o descrizione..."
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
            </div>

            <!-- Stato -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Stato</label>
                <select name="stato" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                    <option value="">Tutti</option>
                    <option value="aperta" {{ request('stato') == 'aperta' ? 'selected' : '' }}>Aperta</option>
                    <option value="in_lavorazione" {{ request('stato') == 'in_lavorazione' ? 'selected' : '' }}>In Lavorazione</option>
                    <option value="risolta" {{ request('stato') == 'risolta' ? 'selected' : '' }}>Risolta</option>
                </select>
            </div>

            <!-- Tipo -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tipo</label>
                <select name="tipo" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                    <option value="">Tutti</option>
                    <option value="bug" {{ request('tipo') == 'bug' ? 'selected' : '' }}>Bug</option>
                    <option value="suggerimento" {{ request('tipo') == 'suggerimento' ? 'selected' : '' }}>Suggerimento</option>
                    <option value="altro" {{ request('tipo') == 'altro' ? 'selected' : '' }}>Altro</option>
                </select>
            </div>

            <!-- Priorità -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Priorità</label>
                <select name="priorita" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                    <option value="">Tutte</option>
                    <option value="alta" {{ request('priorita') == 'alta' ? 'selected' : '' }}>Alta</option>
                    <option value="media" {{ request('priorita') == 'media' ? 'selected' : '' }}>Media</option>
                    <option value="bassa" {{ request('priorita') == 'bassa' ? 'selected' : '' }}>Bassa</option>
                </select>
            </div>

            <!-- Pulsanti -->
            <div class="md:col-span-4 flex gap-3">
                <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-2 rounded-lg transition">
                    <i class="fas fa-search mr-2"></i>Filtra
                </button>
                <a href="{{ route('admin.super-admin.segnalazioni') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-2 rounded-lg transition">
                    <i class="fas fa-times mr-2"></i>Azzera
                </a>
            </div>
        </form>
    </div>

    <!-- Lista Segnalazioni -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-bold text-gray-800 flex items-center">
                <i class="fas fa-list text-blue-600 mr-2"></i>
                Segnalazioni ({{ $segnalazioni->total() }})
            </h3>
        </div>

        @if($segnalazioni->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Titolo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Priorità</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stato</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Segnalato da</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Azioni</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($segnalazioni as $segnalazione)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            #{{ $segnalazione->id }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            <div class="font-medium">{{ Str::limit($segnalazione->titolo, 50) }}</div>
                            <div class="text-xs text-gray-500 mt-1">{{ Str::limit($segnalazione->descrizione, 80) }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $segnalazione->tipo == 'bug' ? 'bg-purple-100 text-purple-800' : ($segnalazione->tipo == 'suggerimento' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') }}">
                                <i class="fas {{ $segnalazione->tipo_icona }} mr-1"></i>
                                {{ $segnalazione->tipo_label }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $segnalazione->priorita_badge }}">
                                {{ $segnalazione->priorita_label }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $segnalazione->stato_badge }}">
                                {{ $segnalazione->stato_label }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            @if($segnalazione->utente)
                                <div class="font-medium">{{ $segnalazione->utente->nome }} {{ $segnalazione->utente->cognome }}</div>
                                @if($segnalazione->email_notifica)
                                    <div class="text-xs text-gray-500">{{ $segnalazione->email_notifica }}</div>
                                @endif
                            @else
                                <span class="text-gray-400">N/A</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <div>{{ $segnalazione->created_at->format('d/m/Y') }}</div>
                            <div class="text-xs">{{ $segnalazione->created_at->format('H:i') }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="{{ route('admin.impostazioni.segnalazioni.show', $segnalazione) }}"
                               class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded transition inline-flex items-center">
                                <i class="fas fa-eye mr-1"></i>
                                Gestisci
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Paginazione -->
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $segnalazioni->links() }}
        </div>
        @else
        <div class="p-12 text-center">
            <i class="fas fa-inbox text-gray-300 text-6xl mb-4"></i>
            <p class="text-gray-500 text-lg">Nessuna segnalazione trovata</p>
            @if(request()->hasAny(['search', 'stato', 'tipo', 'priorita']))
                <a href="{{ route('admin.super-admin.segnalazioni') }}" class="mt-4 inline-block text-purple-600 hover:text-purple-700">
                    Azzera filtri
                </a>
            @endif
        </div>
        @endif
    </div>
</div>
@endsection
