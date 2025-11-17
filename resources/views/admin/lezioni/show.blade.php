@extends('layouts.admin')

@section('titolo', 'Dettaglio Lezione')

@section('contenuto')
<div class="p-6 max-w-7xl mx-auto">

    <!-- Header con gradiente -->
    <div class="bg-gradient-to-r from-viola-magia to-fucsia-magia rounded-lg p-6 text-white mb-6 shadow-lg">
        <div class="flex flex-col md:flex-row md:items-start md:justify-between">
            <div class="mb-4 md:mb-0 flex-1">
                <a href="{{ route('admin.lezioni.index') }}" class="text-white hover:text-gray-200 mb-3 inline-block opacity-90 hover:opacity-100 transition">
                    <i class="fas fa-arrow-left mr-2"></i> Torna alla lista lezioni
                </a>
                <h1 class="text-3xl font-bold mb-2">{{ $lezione->titolo }}</h1>
                <div class="flex flex-wrap items-center gap-4 text-sm opacity-90">
                    <span><i class="fas fa-calendar mr-2"></i>{{ $lezione->data->format('d/m/Y') }} ({{ $lezione->data->locale('it')->dayName }})</span>
                    <span><i class="fas fa-clock mr-2"></i>{{ $lezione->range_orario }}</span>
                    <span><i class="fas fa-hourglass-half mr-2"></i>{{ $lezione->durata_minuti }} minuti</span>
                </div>
            </div>
            <div class="flex-shrink-0">
                <span class="px-4 py-2 rounded-lg text-white text-sm font-semibold inline-block"
                      style="background-color:
                      @if($lezione->stato === 'programmata') #ffa726
                      @elseif($lezione->stato === 'confermata') #66bb6a
                      @elseif($lezione->stato === 'in_corso') #42a5f5
                      @elseif($lezione->stato === 'completata') #26a69a
                      @elseif($lezione->stato === 'cancellata') #ef5350
                      @else #ffa726 @endif">
                    {{ ucfirst(str_replace('_', ' ', $lezione->stato)) }}
                </span>
            </div>
        </div>
    </div>

    <!-- Azioni rapide -->
    <div class="flex flex-wrap gap-3 mb-6">
        <a href="{{ route('admin.lezioni.edit', $lezione->id) }}"
           class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-viola-magia to-fucsia-magia text-white rounded-lg hover:shadow-lg transition-all font-medium">
            <i class="fas fa-edit mr-2"></i> Modifica Lezione
        </a>
        <a href="{{ route('admin.lezioni.prenotazioni', $lezione->id) }}"
           class="inline-flex items-center px-5 py-2.5 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium">
            <i class="fas fa-users mr-2"></i> Gestisci Prenotazioni
        </a>
        <form method="POST" action="{{ route('admin.lezioni.destroy', $lezione->id) }}" class="inline" id="delete-lezione-form">
            @csrf
            @method('DELETE')
            <button type="button"
                    onclick="confermaEliminazione('delete-lezione-form', 'Eliminare la lezione?', 'La lezione {{ $lezione->titolo }} del {{ \Carbon\Carbon::parse($lezione->data)->format('d/m/Y') }} sarà eliminata definitivamente, incluse tutte le prenotazioni.')"
                    class="inline-flex items-center px-5 py-2.5 bg-red-600 text-white rounded-lg hover:bg-red-700 transition font-medium">
                <i class="fas fa-trash mr-2"></i> Elimina Lezione
            </button>
        </form>
    </div>

    <!-- Cards Info Rapide -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <!-- Card Tipologia -->
        <div class="bg-purple-50 rounded-lg p-5 border-l-4 border-viola-magia shadow-sm">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 rounded-full flex items-center justify-center"
                         style="background-color:
                         @if($lezione->tipologia === 'gruppo') #9c27b0
                         @elseif($lezione->tipologia === 'individuale') #e91e63
                         @elseif($lezione->tipologia === 'online') #2196f3
                         @else #ff9800 @endif">
                        <i class="fas fa-tag text-white text-lg"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Tipologia</p>
                    <p class="text-xl font-bold text-gray-900">{{ ucfirst($lezione->tipologia) }}</p>
                </div>
            </div>
        </div>

        <!-- Card Durata -->
        <div class="bg-blue-50 rounded-lg p-5 border-l-4 border-blue-500 shadow-sm">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center">
                        <i class="fas fa-hourglass-half text-white text-lg"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Durata</p>
                    <p class="text-xl font-bold text-gray-900">{{ $lezione->durata_minuti }} <span class="text-sm font-normal">min</span></p>
                </div>
            </div>
        </div>

        <!-- Card Posti Occupati -->
        <div class="bg-orange-50 rounded-lg p-5 border-l-4 border-orange-500 shadow-sm">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-orange-500 rounded-full flex items-center justify-center">
                        <i class="fas fa-users text-white text-lg"></i>
                    </div>
                </div>
                <div class="ml-4 flex-1">
                    <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Occupazione</p>
                    <p class="text-xl font-bold text-gray-900">{{ $lezione->posti_occupati }}/{{ $lezione->posti_totali }}</p>
                    <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                        <div class="bg-orange-500 h-2 rounded-full transition-all" style="width: {{ $lezione->percentuale_occupazione }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card Disponibili -->
        <div class="bg-green-50 rounded-lg p-5 border-l-4 border-green-500 shadow-sm">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-green-500 rounded-full flex items-center justify-center">
                        <i class="fas fa-check-circle text-white text-lg"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Disponibili</p>
                    <p class="text-xl font-bold {{ $lezione->postiDisponibili() > 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ $lezione->postiDisponibili() }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <div class="bg-white rounded-lg shadow-lg" x-data="{ tab: 'dettagli' }">

        <!-- Tab Headers -->
        <div class="border-b border-gray-200 overflow-x-auto">
            <nav class="flex -mb-px">
                <button @click="tab = 'dettagli'"
                        :class="tab === 'dettagli' ? 'border-fucsia-magia text-fucsia-magia bg-pink-50' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="py-4 px-6 border-b-2 font-medium text-sm transition whitespace-nowrap">
                    <i class="fas fa-info-circle mr-2"></i> Dettagli Completi
                </button>
                <button @click="tab = 'partecipanti'"
                        :class="tab === 'partecipanti' ? 'border-fucsia-magia text-fucsia-magia bg-pink-50' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="py-4 px-6 border-b-2 font-medium text-sm transition whitespace-nowrap">
                    <i class="fas fa-users mr-2"></i> Partecipanti <span class="ml-1 px-2 py-0.5 bg-fucsia-magia text-white rounded-full text-xs">{{ $lezione->clienti->count() }}</span>
                </button>
                <button @click="tab = 'note'"
                        :class="tab === 'note' ? 'border-fucsia-magia text-fucsia-magia bg-pink-50' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="py-4 px-6 border-b-2 font-medium text-sm transition whitespace-nowrap">
                    <i class="fas fa-sticky-note mr-2"></i> Note
                </button>
                @if($lezione->isOnline())
                <button @click="tab = 'online'"
                        :class="tab === 'online' ? 'border-fucsia-magia text-fucsia-magia bg-pink-50' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="py-4 px-6 border-b-2 font-medium text-sm transition whitespace-nowrap">
                    <i class="fas fa-video mr-2"></i> Link Online
                </button>
                @endif
            </nav>
        </div>

        <!-- Tab Content -->
        <div class="p-6">

            <!-- Tab Dettagli -->
            <div x-show="tab === 'dettagli'" x-cloak>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                    <!-- Colonna Sinistra -->
                    <div class="space-y-4">

                        <!-- Informazioni Base -->
                        <div class="bg-gray-50 rounded-lg p-5 border-l-4 border-gray-400">
                            <h3 class="font-bold text-gray-900 mb-4 flex items-center">
                                <i class="fas fa-info-circle text-fucsia-magia mr-2"></i>
                                Informazioni Base
                            </h3>
                            <dl class="space-y-3">
                                <div class="flex justify-between py-2 border-b border-gray-200">
                                    <dt class="text-sm text-gray-600 font-medium">Titolo:</dt>
                                    <dd class="text-sm font-semibold text-gray-900">{{ $lezione->titolo }}</dd>
                                </div>
                                @if($lezione->descrizione)
                                <div class="py-2">
                                    <dt class="text-sm text-gray-600 font-medium mb-2">Descrizione:</dt>
                                    <dd class="text-sm text-gray-800 leading-relaxed bg-white p-3 rounded">{{ $lezione->descrizione }}</dd>
                                </div>
                                @endif
                            </dl>
                        </div>

                        <!-- Data e Orario -->
                        <div class="bg-blue-50 rounded-lg p-5 border-l-4 border-blue-500">
                            <h3 class="font-bold text-gray-900 mb-4 flex items-center">
                                <i class="fas fa-calendar-alt text-blue-600 mr-2"></i>
                                Data e Orario
                            </h3>
                            <dl class="space-y-3">
                                <div class="flex justify-between items-center py-2 border-b border-blue-200">
                                    <dt class="text-sm text-gray-700 font-medium">
                                        <i class="fas fa-calendar text-blue-600 mr-2"></i>Data:
                                    </dt>
                                    <dd class="text-sm font-semibold text-gray-900">
                                        {{ $lezione->data->format('d/m/Y') }} ({{ $lezione->data->locale('it')->dayName }})
                                    </dd>
                                </div>
                                <div class="flex justify-between items-center py-2 border-b border-blue-200">
                                    <dt class="text-sm text-gray-700 font-medium">
                                        <i class="fas fa-clock text-blue-600 mr-2"></i>Orario:
                                    </dt>
                                    <dd class="text-sm font-semibold text-gray-900">{{ $lezione->range_orario }}</dd>
                                </div>
                                <div class="flex justify-between items-center py-2">
                                    <dt class="text-sm text-gray-700 font-medium">
                                        <i class="fas fa-hourglass-half text-blue-600 mr-2"></i>Durata:
                                    </dt>
                                    <dd class="text-sm font-semibold text-gray-900">{{ $lezione->durata_minuti }} minuti</dd>
                                </div>
                            </dl>
                        </div>

                    </div>

                    <!-- Colonna Destra -->
                    <div class="space-y-4">

                        <!-- Assegnazioni -->
                        <div class="bg-purple-50 rounded-lg p-5 border-l-4 border-viola-magia">
                            <h3 class="font-bold text-gray-900 mb-4 flex items-center">
                                <i class="fas fa-link text-viola-magia mr-2"></i>
                                Assegnazioni
                            </h3>
                            <dl class="space-y-3">
                                @if($lezione->programma)
                                <div class="flex justify-between items-center py-2 border-b border-purple-200">
                                    <dt class="text-sm text-gray-700 font-medium">
                                        <i class="fas fa-dumbbell text-viola-magia mr-2"></i>Programma:
                                    </dt>
                                    <dd class="text-sm font-semibold text-gray-900">{{ $lezione->programma->nome }}</dd>
                                </div>
                                @endif
                                @if($lezione->professionista)
                                <div class="flex justify-between items-center py-2 border-b border-purple-200">
                                    <dt class="text-sm text-gray-700 font-medium">
                                        <i class="fas fa-user-tie text-viola-magia mr-2"></i>Professionista:
                                    </dt>
                                    <dd class="text-sm font-semibold text-gray-900">
                                        {{ $lezione->professionista->nome }} {{ $lezione->professionista->cognome }}
                                    </dd>
                                </div>
                                @endif
                                @if($lezione->sede)
                                <div class="flex justify-between items-center py-2">
                                    <dt class="text-sm text-gray-700 font-medium">
                                        <i class="fas fa-map-marker-alt text-viola-magia mr-2"></i>Sede:
                                    </dt>
                                    <dd class="text-sm font-semibold text-gray-900">{{ $lezione->sede->nome }}</dd>
                                </div>
                                @else
                                <div class="flex justify-between items-center py-2">
                                    <dt class="text-sm text-gray-700 font-medium">
                                        <i class="fas fa-globe text-viola-magia mr-2"></i>Modalità:
                                    </dt>
                                    <dd class="text-sm font-semibold text-gray-900">Online</dd>
                                </div>
                                @endif
                            </dl>
                        </div>

                        <!-- Ricorrenza -->
                        @if($lezione->ricorrente)
                        <div class="bg-orange-50 rounded-lg p-5 border-l-4 border-orange-500">
                            <h3 class="font-bold text-gray-900 mb-4 flex items-center">
                                <i class="fas fa-repeat text-orange-600 mr-2"></i>
                                Ricorrenza
                            </h3>
                            <dl class="space-y-3">
                                <div class="flex justify-between items-center py-2 border-b border-orange-200">
                                    <dt class="text-sm text-gray-700 font-medium">Frequenza:</dt>
                                    <dd class="text-sm font-semibold text-gray-900">{{ ucfirst($lezione->frequenza_ricorrenza) }}</dd>
                                </div>
                                @if($lezione->fine_ricorrenza)
                                <div class="flex justify-between items-center py-2 border-b border-orange-200">
                                    <dt class="text-sm text-gray-700 font-medium">Fino al:</dt>
                                    <dd class="text-sm font-semibold text-gray-900">{{ $lezione->fine_ricorrenza->format('d/m/Y') }}</dd>
                                </div>
                                @endif
                                @if($lezione->lezioniFiglie && $lezione->lezioniFiglie->count() > 0)
                                <div class="flex justify-between items-center py-2">
                                    <dt class="text-sm text-gray-700 font-medium">Lezioni Generate:</dt>
                                    <dd class="text-sm font-semibold text-gray-900">{{ $lezione->lezioniFiglie->count() }} lezioni</dd>
                                </div>
                                @endif
                            </dl>
                        </div>
                        @endif

                    </div>

                </div>
            </div>

            <!-- Tab Partecipanti -->
            <div x-show="tab === 'partecipanti'" x-cloak>
                @if($lezione->clienti->count() > 0)
                <div class="overflow-hidden rounded-lg border border-gray-200">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gradient-to-r from-viola-magia to-fucsia-magia text-white">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider">Cliente</th>
                                <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider">Contatto</th>
                                <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider">Prenotazione</th>
                                <th class="px-6 py-4 text-center text-xs font-bold uppercase tracking-wider">Check-in</th>
                                <th class="px-6 py-4 text-center text-xs font-bold uppercase tracking-wider">Check-out</th>
                                <th class="px-6 py-4 text-center text-xs font-bold uppercase tracking-wider">Stato</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($lezione->clienti as $cliente)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-fucsia-magia rounded-full flex items-center justify-center text-white font-bold">
                                            {{ strtoupper(substr($cliente->nome, 0, 1)) }}{{ strtoupper(substr($cliente->cognome, 0, 1)) }}
                                        </div>
                                        <div class="ml-3">
                                            <div class="text-sm font-bold text-gray-900">
                                                {{ $cliente->nome }} {{ $cliente->cognome }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900">
                                        <i class="fas fa-envelope text-gray-400 mr-2"></i>{{ $cliente->email }}
                                    </div>
                                    @if($cliente->telefono)
                                    <div class="text-sm text-gray-600 mt-1">
                                        <i class="fas fa-phone text-gray-400 mr-2"></i>{{ $cliente->telefono }}
                                    </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    {{ $cliente->pivot->data_prenotazione ? \Carbon\Carbon::parse($cliente->pivot->data_prenotazione)->format('d/m/Y H:i') : '-' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-center text-gray-900">
                                    {{ $cliente->pivot->check_in ? \Carbon\Carbon::parse($cliente->pivot->check_in)->format('H:i') : '-' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-center text-gray-900">
                                    {{ $cliente->pivot->check_out ? \Carbon\Carbon::parse($cliente->pivot->check_out)->format('H:i') : '-' }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($cliente->pivot->stato === 'presente')
                                        <span class="px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                            <i class="fas fa-check-circle mr-1"></i> Presente
                                        </span>
                                    @elseif($cliente->pivot->stato === 'assente')
                                        <span class="px-3 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                            <i class="fas fa-times-circle mr-1"></i> Assente
                                        </span>
                                    @else
                                        <span class="px-3 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                            <i class="fas fa-clock mr-1"></i> Prenotato
                                        </span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-4 p-4 bg-gray-50 rounded-lg">
                    <div class="flex items-center text-sm text-gray-600">
                        <i class="fas fa-info-circle text-fucsia-magia mr-2"></i>
                        <span><strong class="text-gray-900">{{ $lezione->clienti->count() }}</strong> partecipanti iscritti su <strong class="text-gray-900">{{ $lezione->posti_totali }}</strong> posti totali</span>
                    </div>
                </div>
                @else
                <div class="text-center py-16">
                    <div class="inline-block p-6 bg-gray-100 rounded-full mb-4">
                        <i class="fas fa-users text-gray-400 text-6xl"></i>
                    </div>
                    <p class="text-gray-600 font-medium text-lg">Nessun partecipante prenotato</p>
                    <p class="text-gray-500 text-sm mt-2">I partecipanti appariranno qui una volta effettuata la prenotazione</p>
                </div>
                @endif
            </div>

            <!-- Tab Note -->
            <div x-show="tab === 'note'" x-cloak>
                <div class="space-y-6">
                    @if($lezione->note_pubbliche)
                    <div class="bg-blue-50 rounded-lg p-6 border-l-4 border-blue-500">
                        <h3 class="font-bold text-gray-900 mb-3 flex items-center text-lg">
                            <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center mr-3">
                                <i class="fas fa-sticky-note text-white"></i>
                            </div>
                            Note Pubbliche
                        </h3>
                        <div class="bg-white rounded-lg p-4 text-gray-800 leading-relaxed">
                            {{ $lezione->note_pubbliche }}
                        </div>
                    </div>
                    @endif

                    @if($lezione->note_interne)
                    <div class="bg-yellow-50 rounded-lg p-6 border-l-4 border-yellow-500">
                        <h3 class="font-bold text-gray-900 mb-3 flex items-center text-lg">
                            <div class="w-10 h-10 bg-yellow-500 rounded-full flex items-center justify-center mr-3">
                                <i class="fas fa-lock text-white"></i>
                            </div>
                            Note Interne (Riservate)
                        </h3>
                        <div class="bg-white rounded-lg p-4 text-gray-800 leading-relaxed">
                            {{ $lezione->note_interne }}
                        </div>
                    </div>
                    @endif

                    @if(!$lezione->note_pubbliche && !$lezione->note_interne)
                    <div class="text-center py-16">
                        <div class="inline-block p-6 bg-gray-100 rounded-full mb-4">
                            <i class="fas fa-sticky-note text-gray-400 text-6xl"></i>
                        </div>
                        <p class="text-gray-600 font-medium text-lg">Nessuna nota disponibile</p>
                        <p class="text-gray-500 text-sm mt-2">Le note verranno visualizzate qui</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Tab Online -->
            @if($lezione->isOnline())
            <div x-show="tab === 'online'" x-cloak>
                <div class="space-y-6">
                    @if($lezione->link_online)
                    <div class="bg-indigo-50 rounded-lg p-6 border-l-4 border-indigo-500">
                        <h3 class="font-bold text-gray-900 mb-4 flex items-center text-lg">
                            <div class="w-10 h-10 bg-indigo-500 rounded-full flex items-center justify-center mr-3">
                                <i class="fas fa-video text-white"></i>
                            </div>
                            Link Meeting Online
                        </h3>
                        <div class="bg-white rounded-lg p-4 mb-4">
                            <a href="{{ $lezione->link_online }}" target="_blank"
                               class="text-indigo-600 hover:text-indigo-800 underline break-all font-medium">
                                {{ $lezione->link_online }}
                            </a>
                        </div>
                        <button onclick="navigator.clipboard.writeText('{{ $lezione->link_online }}'); alert('Link copiato negli appunti!');"
                                class="px-5 py-2.5 bg-indigo-500 text-white rounded-lg hover:bg-indigo-600 transition font-medium">
                            <i class="fas fa-copy mr-2"></i> Copia Link negli Appunti
                        </button>
                    </div>
                    @endif

                    @if($lezione->password_online)
                    <div class="bg-amber-50 rounded-lg p-6 border-l-4 border-amber-500">
                        <h3 class="font-bold text-gray-900 mb-4 flex items-center text-lg">
                            <div class="w-10 h-10 bg-amber-500 rounded-full flex items-center justify-center mr-3">
                                <i class="fas fa-key text-white"></i>
                            </div>
                            Password Meeting
                        </h3>
                        <div class="bg-white rounded-lg p-4 mb-4">
                            <code class="text-2xl font-mono font-bold text-gray-900">{{ $lezione->password_online }}</code>
                        </div>
                        <button onclick="navigator.clipboard.writeText('{{ $lezione->password_online }}'); alert('Password copiata negli appunti!');"
                                class="px-5 py-2.5 bg-amber-500 text-white rounded-lg hover:bg-amber-600 transition font-medium">
                            <i class="fas fa-copy mr-2"></i> Copia Password negli Appunti
                        </button>
                    </div>
                    @endif
                </div>
            </div>
            @endif

        </div>

    </div>

</div>
@endsection
