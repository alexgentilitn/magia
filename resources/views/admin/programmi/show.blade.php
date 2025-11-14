@extends('layouts.admin')

@section('titolo', 'Dettaglio Programma')

@section('contenuto')
<div class="p-6">

    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <div class="mb-4 md:mb-0">
            <a href="{{ route('admin.programmi.index') }}" class="text-fucsia-magia hover:text-viola-magia mb-2 inline-block">
                <i class="fas fa-arrow-left mr-2"></i> Torna alla lista
            </a>
            <h2 class="text-2xl font-bold text-gray-800">{{ $programma->nome }}</h2>
            @if($programma->descrizione_breve)
            <p class="text-gray-600 mt-1">{{ $programma->descrizione_breve }}</p>
            @endif
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.programmi.edit', $programma->id) }}"
               class="px-4 py-2 bg-fucsia-magia text-white rounded-lg hover:bg-viola-magia transition">
                <i class="fas fa-edit mr-2"></i> Modifica
            </a>
            <form method="POST" action="{{ route('admin.programmi.duplica', $programma->id) }}" class="inline">
                @csrf
                <button type="submit"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    <i class="fas fa-copy mr-2"></i> Duplica
                </button>
            </form>
            <form method="POST" action="{{ route('admin.programmi.destroy', $programma->id) }}" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit"
                        onclick="return confirm('Sei sicuro di voler eliminare questo programma?')"
                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                    <i class="fas fa-trash mr-2"></i> Elimina
                </button>
            </form>
        </div>
    </div>

    <!-- Statistiche -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-sm text-gray-600">Iscritti</p>
            <p class="text-2xl font-bold text-gray-800">{{ $statistiche['iscritti_totali'] }}</p>
            <p class="text-xs text-gray-500">di cui {{ $statistiche['iscritti_attivi'] }} attivi</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-sm text-gray-600">Lezioni</p>
            <p class="text-2xl font-bold text-gray-800">{{ $statistiche['lezioni_totali'] }}</p>
            <p class="text-xs text-gray-500">{{ $statistiche['lezioni_future'] }} future</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-sm text-gray-600">Posti</p>
            <p class="text-2xl font-bold text-gray-800">{{ $statistiche['posti_occupati'] }}@if($statistiche['posti_disponibili']) / {{ $statistiche['posti_disponibili'] }}@endif</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-sm text-gray-600">Prezzo</p>
            <p class="text-2xl font-bold {{ $programma->isInPromo() ? 'text-green-600' : 'text-gray-800' }}">
                @if($programma->prezzo_su_richiesta)
                    <span class="text-base">Su richiesta</span>
                @else
                    € {{ number_format($programma->prezzo_attuale, 2) }}
                @endif
            </p>
            @if($programma->isInPromo())
            <p class="text-xs text-red-600">-{{ $programma->sconto_percentuale }}% di sconto</p>
            @endif
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-sm text-gray-600">Stato</p>
            <div class="flex flex-wrap gap-1 mt-2">
                @if($programma->attivo)
                    <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Attivo</span>
                @else
                    <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">Non attivo</span>
                @endif
                @if($programma->visibile_pubblico)
                    <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">Visibile</span>
                @endif
                @if($programma->in_evidenza)
                    <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">★ In evidenza</span>
                @endif
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <div class="bg-white rounded-lg shadow" x-data="{ tab: 'info' }">

        <!-- Tab Headers -->
        <div class="border-b border-gray-200 overflow-x-auto">
            <nav class="flex -mb-px">
                <button @click="tab = 'info'"
                        :class="tab === 'info' ? 'border-fucsia-magia text-fucsia-magia' : 'border-transparent text-gray-500 hover:text-gray-700'"
                        class="py-4 px-6 border-b-2 font-medium text-sm transition whitespace-nowrap">
                    <i class="fas fa-info-circle mr-2"></i> Informazioni
                </button>
                <button @click="tab = 'iscritti'"
                        :class="tab === 'iscritti' ? 'border-fucsia-magia text-fucsia-magia' : 'border-transparent text-gray-500 hover:text-gray-700'"
                        class="py-4 px-6 border-b-2 font-medium text-sm transition whitespace-nowrap">
                    <i class="fas fa-users mr-2"></i> Iscritti ({{ $statistiche['iscritti_totali'] }})
                </button>
                <button @click="tab = 'lezioni'"
                        :class="tab === 'lezioni' ? 'border-fucsia-magia text-fucsia-magia' : 'border-transparent text-gray-500 hover:text-gray-700'"
                        class="py-4 px-6 border-b-2 font-medium text-sm transition whitespace-nowrap">
                    <i class="fas fa-calendar-alt mr-2"></i> Lezioni ({{ $programma->lezioni->count() }})
                </button>
            </nav>
        </div>

        <!-- Tab Content -->
        <div class="p-6">

            <!-- Tab Info -->
            <div x-show="tab === 'info'" x-cloak>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-6">
                        <div>
                            <h3 class="font-bold text-gray-800 mb-4 pb-2 border-b">Dettagli</h3>
                            <dl class="space-y-3">
                                <div><dt class="text-sm text-gray-600">Tipologia</dt><dd class="text-base font-medium text-gray-800">{{ ucfirst($programma->tipologia) }}</dd></div>
                                <div><dt class="text-sm text-gray-600">Livello</dt><dd class="text-base font-medium text-gray-800">{{ ucfirst($programma->livello) }}</dd></div>
                                @if($programma->durata_mesi || $programma->durata_giorni)
                                <div><dt class="text-sm text-gray-600">Durata</dt><dd class="text-base font-medium text-gray-800">
                                    @if($programma->durata_mesi){{ $programma->durata_mesi }} mesi @endif
                                    @if($programma->durata_giorni)({{ $programma->durata_giorni }} giorni)@endif
                                </dd></div>
                                @endif
                                @if($programma->lezioni_totali)
                                <div><dt class="text-sm text-gray-600">Lezioni Totali</dt><dd class="text-base font-medium text-gray-800">{{ $programma->lezioni_totali }} lezioni</dd></div>
                                @endif
                                @if($programma->lezioni_settimana)
                                <div><dt class="text-sm text-gray-600">Frequenza</dt><dd class="text-base font-medium text-gray-800">{{ $programma->lezioni_settimana }} lezioni/settimana</dd></div>
                                @endif
                                @if($programma->durata_singola_lezione)
                                <div><dt class="text-sm text-gray-600">Durata Lezione</dt><dd class="text-base font-medium text-gray-800">{{ $programma->durata_singola_lezione }} minuti</dd></div>
                                @endif
                            </dl>
                        </div>
                        @if($programma->descrizione)
                        <div>
                            <h3 class="font-bold text-gray-800 mb-3 pb-2 border-b">Descrizione</h3>
                            <p class="text-gray-700">{{ $programma->descrizione }}</p>
                        </div>
                        @endif
                    </div>

                    <div class="space-y-6">
                        <div>
                            <h3 class="font-bold text-gray-800 mb-4 pb-2 border-b">Assegnazioni</h3>
                            <dl class="space-y-3">
                                @if($programma->sede)
                                <div><dt class="text-sm text-gray-600">Sede</dt><dd class="text-base font-medium text-gray-800"><i class="fas fa-map-marker-alt text-fucsia-magia mr-2"></i>{{ $programma->sede->nome }}</dd></div>
                                @endif
                                @if($programma->professionista)
                                <div><dt class="text-sm text-gray-600">Professionista</dt><dd class="text-base font-medium text-gray-800"><i class="fas fa-user-tie text-fucsia-magia mr-2"></i>{{ $programma->professionista->nome }} {{ $programma->professionista->cognome }}</dd></div>
                                @endif
                            </dl>
                        </div>

                        @if($programma->data_inizio || $programma->data_fine)
                        <div>
                            <h3 class="font-bold text-gray-800 mb-4 pb-2 border-b">Periodo</h3>
                            <dl class="space-y-3">
                                @if($programma->data_inizio)<div><dt class="text-sm text-gray-600">Data Inizio</dt><dd class="text-base font-medium text-gray-800">{{ $programma->data_inizio->format('d/m/Y') }}</dd></div>@endif
                                @if($programma->data_fine)<div><dt class="text-sm text-gray-600">Data Fine</dt><dd class="text-base font-medium text-gray-800">{{ $programma->data_fine->format('d/m/Y') }}</dd></div>@endif
                            </dl>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Tab Iscritti -->
            <div x-show="tab === 'iscritti'" x-cloak>
                @if($programma->clienti->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cliente</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Iscrizione</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Periodo</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Stato</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($programma->clienti as $cliente)
                            <tr>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $cliente->nome }} {{ $cliente->cognome }}</div>
                                    <div class="text-xs text-gray-500">{{ $cliente->email }}</div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $cliente->pivot->data_iscrizione ? \Carbon\Carbon::parse($cliente->pivot->data_iscrizione)->format('d/m/Y') : '-' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    {{ $cliente->pivot->data_inizio ? \Carbon\Carbon::parse($cliente->pivot->data_inizio)->format('d/m/Y') : '' }}
                                    @if($cliente->pivot->data_fine) - {{ \Carbon\Carbon::parse($cliente->pivot->data_fine)->format('d/m/Y') }}@endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="px-2 py-1 text-xs rounded-full {{ $cliente->pivot->stato == 'attivo' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ ucfirst($cliente->pivot->stato ?? 'sconosciuto') }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-12">
                    <i class="fas fa-users text-gray-400 text-5xl mb-4"></i>
                    <p class="text-gray-500 font-medium">Nessun iscritto</p>
                </div>
                @endif
            </div>

            <!-- Tab Lezioni -->
            <div x-show="tab === 'lezioni'" x-cloak>
                @if($programma->lezioni->count() > 0)
                <div class="space-y-3">
                    @foreach($programma->lezioni->sortBy('data') as $lezione)
                    <div class="flex items-center justify-between p-4 border rounded-lg hover:bg-gray-50">
                        <div class="flex-1">
                            <p class="font-medium text-gray-800">{{ $lezione->titolo }}</p>
                            <p class="text-sm text-gray-600">
                                <i class="fas fa-calendar mr-1"></i> {{ $lezione->data->format('d/m/Y') }}
                                <i class="fas fa-clock ml-3 mr-1"></i> {{ $lezione->range_orario }}
                            </p>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="px-2 py-1 text-xs rounded-full {{ $lezione->badge_stato }}">{{ ucfirst($lezione->stato) }}</span>
                            <a href="{{ route('admin.lezioni.show', $lezione->id) }}" class="text-blue-600 hover:text-blue-800">
                                <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-12">
                    <i class="fas fa-calendar-times text-gray-400 text-5xl mb-4"></i>
                    <p class="text-gray-500 font-medium">Nessuna lezione programmata</p>
                    <a href="{{ route('admin.lezioni.create') }}?programma_id={{ $programma->id }}" class="text-fucsia-magia hover:text-viola-magia mt-2 inline-block">
                        <i class="fas fa-plus-circle mr-1"></i> Crea prima lezione
                    </a>
                </div>
                @endif
            </div>

        </div>
    </div>

</div>
@endsection
