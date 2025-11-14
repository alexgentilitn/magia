<div class="space-y-6">
    <!-- Header con titolo e badge -->
    <div class="bg-gradient-to-r from-viola-magia to-fucsia-magia rounded-lg p-4 text-white">
        <div class="flex items-start justify-between">
            <div class="flex-1">
                <h3 class="text-xl font-bold mb-2">{{ $lezione->titolo }}</h3>
                <div class="flex items-center text-sm opacity-90">
                    <i class="fas fa-calendar mr-2"></i>
                    {{ $lezione->data->format('d/m/Y') }}
                    <span class="mx-3">•</span>
                    <i class="fas fa-clock mr-2"></i>
                    {{ \Carbon\Carbon::parse($lezione->ora_inizio)->format('H:i') }} - {{ \Carbon\Carbon::parse($lezione->ora_fine)->format('H:i') }}
                    <span class="mx-3">•</span>
                    <i class="fas fa-hourglass-half mr-2"></i>
                    {{ $lezione->durata_minuti }} min
                </div>
            </div>
            <div class="ml-4">
                <span class="px-3 py-1 rounded-full text-white text-xs font-semibold"
                      style="background-color:
                      @if($lezione->stato === 'programmata') #ffa726
                      @elseif($lezione->stato === 'confermata') #66bb6a
                      @elseif($lezione->stato === 'in_corso') #42a5f5
                      @elseif($lezione->stato === 'completata') #26a69a
                      @elseif($lezione->stato === 'cancellata') #ef5350
                      @else #ffa726 @endif">
                    {{ ucfirst($lezione->stato) }}
                </span>
            </div>
        </div>
    </div>

    <!-- Grid informazioni principali -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Card Tipologia -->
        <div class="bg-purple-50 rounded-lg p-4 border-l-4 border-viola-magia">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center"
                         style="background-color:
                         @if($lezione->tipologia === 'gruppo') #9c27b0
                         @elseif($lezione->tipologia === 'individuale') #e91e63
                         @elseif($lezione->tipologia === 'online') #2196f3
                         @else #ff9800 @endif">
                        <i class="fas fa-tag text-white"></i>
                    </div>
                </div>
                <div class="ml-3">
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Tipologia</p>
                    <p class="text-lg font-semibold text-gray-900">{{ ucfirst($lezione->tipologia) }}</p>
                </div>
            </div>
        </div>

        <!-- Card Posti -->
        <div class="bg-blue-50 rounded-lg p-4 border-l-4 border-blue-500">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center">
                        <i class="fas fa-users text-white"></i>
                    </div>
                </div>
                <div class="ml-3">
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Partecipanti</p>
                    <p class="text-lg font-semibold text-gray-900">
                        {{ $lezione->posti_occupati }} / {{ $lezione->posti_totali }}
                        @php
                            $postiDisponibili = $lezione->posti_totali - $lezione->posti_occupati;
                        @endphp
                        <span class="text-sm {{ $postiDisponibili > 0 ? 'text-green-600' : 'text-red-600' }}">
                            ({{ $postiDisponibili }} liberi)
                        </span>
                    </p>
                </div>
            </div>
        </div>

        <!-- Card Professionista -->
        <div class="bg-pink-50 rounded-lg p-4 border-l-4 border-fucsia-magia">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 bg-fucsia-magia rounded-full flex items-center justify-center">
                        <i class="fas fa-user-tie text-white"></i>
                    </div>
                </div>
                <div class="ml-3">
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Professionista</p>
                    <p class="text-lg font-semibold text-gray-900">
                        {{ $lezione->professionista ? $lezione->professionista->nome . ' ' . $lezione->professionista->cognome : 'Non assegnato' }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Card Sede -->
        <div class="bg-green-50 rounded-lg p-4 border-l-4 border-green-500">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center">
                        <i class="fas fa-map-marker-alt text-white"></i>
                    </div>
                </div>
                <div class="ml-3">
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Sede</p>
                    <p class="text-lg font-semibold text-gray-900">
                        {{ $lezione->sede ? $lezione->sede->nome : 'Online' }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Programma (se presente) -->
    @if($lezione->programma)
    <div class="bg-orange-50 rounded-lg p-4 border-l-4 border-orange-500">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <div class="w-10 h-10 bg-orange-500 rounded-full flex items-center justify-center">
                    <i class="fas fa-dumbbell text-white"></i>
                </div>
            </div>
            <div class="ml-3">
                <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Programma</p>
                <p class="text-base font-semibold text-gray-900">{{ $lezione->programma->nome }}</p>
                @if($lezione->programma->descrizione)
                <p class="text-sm text-gray-600 mt-1">{{ Str::limit($lezione->programma->descrizione, 100) }}</p>
                @endif
            </div>
        </div>
    </div>
    @endif

    <!-- Descrizione (se presente) -->
    @if($lezione->descrizione)
    <div class="bg-gray-50 rounded-lg p-4 border-l-4 border-gray-400">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <div class="w-10 h-10 bg-gray-400 rounded-full flex items-center justify-center">
                    <i class="fas fa-align-left text-white"></i>
                </div>
            </div>
            <div class="ml-3 flex-1">
                <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Descrizione</p>
                <p class="text-sm text-gray-700 leading-relaxed">{{ $lezione->descrizione }}</p>
            </div>
        </div>
    </div>
    @endif

    <!-- Note Pubbliche (se presenti) -->
    @if($lezione->note_pubbliche)
    <div class="bg-blue-50 rounded-lg p-4 border-l-4 border-blue-400">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <div class="w-10 h-10 bg-blue-400 rounded-full flex items-center justify-center">
                    <i class="fas fa-sticky-note text-white"></i>
                </div>
            </div>
            <div class="ml-3 flex-1">
                <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Note</p>
                <p class="text-sm text-gray-700 leading-relaxed">{{ $lezione->note_pubbliche }}</p>
            </div>
        </div>
    </div>
    @endif

    <!-- Link Online (se presente) -->
    @if($lezione->link_online)
    <div class="bg-indigo-50 rounded-lg p-4 border-l-4 border-indigo-500">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 bg-indigo-500 rounded-full flex items-center justify-center">
                        <i class="fas fa-video text-white"></i>
                    </div>
                </div>
                <div class="ml-3">
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Lezione Online</p>
                    <p class="text-sm text-gray-700 mt-1">Clicca per accedere al meeting</p>
                </div>
            </div>
            <a href="{{ $lezione->link_online }}" target="_blank"
               class="px-4 py-2 bg-indigo-500 text-white rounded-lg hover:bg-indigo-600 transition-colors text-sm font-medium">
                <i class="fas fa-external-link-alt mr-2"></i>
                Accedi
            </a>
        </div>
        @if($lezione->password_online)
        <div class="mt-3 pt-3 border-t border-indigo-200">
            <p class="text-xs text-gray-500 mb-1">Password:</p>
            <code class="text-sm bg-white px-3 py-1 rounded border border-indigo-200">{{ $lezione->password_online }}</code>
        </div>
        @endif
    </div>
    @endif

    <!-- Lista Partecipanti (se ci sono iscritti) -->
    @if($lezione->posti_occupati > 0 && $lezione->clienti && $lezione->clienti->count() > 0)
    <div class="bg-white rounded-lg border border-gray-200">
        <div class="px-4 py-3 bg-gray-50 border-b border-gray-200 rounded-t-lg">
            <div class="flex items-center">
                <i class="fas fa-users text-fucsia-magia mr-2"></i>
                <h4 class="font-semibold text-gray-900">Partecipanti Iscritti ({{ $lezione->clienti->count() }})</h4>
            </div>
        </div>
        <div class="p-4">
            <div class="space-y-2 max-h-40 overflow-y-auto">
                @foreach($lezione->clienti->take(10) as $cliente)
                <div class="flex items-center py-2 px-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                    <div class="w-8 h-8 bg-fucsia-magia rounded-full flex items-center justify-center text-white text-sm font-bold">
                        {{ strtoupper(substr($cliente->nome, 0, 1)) }}{{ strtoupper(substr($cliente->cognome, 0, 1)) }}
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-900">{{ $cliente->nome }} {{ $cliente->cognome }}</p>
                        <p class="text-xs text-gray-500">{{ $cliente->email }}</p>
                    </div>
                </div>
                @endforeach
                @if($lezione->clienti->count() > 10)
                <p class="text-xs text-gray-500 text-center pt-2">+ altri {{ $lezione->clienti->count() - 10 }} partecipanti</p>
                @endif
            </div>
        </div>
    </div>
    @endif

    <!-- Azioni -->
    <div class="pt-4 border-t border-gray-200 flex flex-col sm:flex-row justify-end gap-3">
        <a href="{{ route('admin.lezioni.show', $lezione->id) }}"
           class="inline-flex items-center justify-center px-5 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors font-medium">
            <i class="fas fa-eye mr-2"></i>
            Dettagli Completi
        </a>
        <a href="{{ route('admin.lezioni.edit', $lezione->id) }}?return=calendario"
           class="inline-flex items-center justify-center px-5 py-2.5 bg-gradient-to-r from-viola-magia to-fucsia-magia text-white rounded-lg hover:shadow-lg transition-all font-medium">
            <i class="fas fa-edit mr-2"></i>
            Modifica Lezione
        </a>
    </div>
</div>
