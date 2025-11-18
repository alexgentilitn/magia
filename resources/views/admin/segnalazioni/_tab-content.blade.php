{{--
    TAB SEGNALAZIONI - Content per Impostazioni
    Sistema di segnalazione bug/suggerimenti
--}}

<div x-show="activeTab === 'segnalazioni'" x-cloak>
    <div class="max-w-7xl mx-auto">

        <!-- Header e Statistiche -->
        <div class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Stat: Totali -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Totali</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $statsSegnalazioni['totali'] }}</p>
                    </div>
                    <div class="p-3 bg-gray-100 rounded-full">
                        <i class="fas fa-exclamation-circle text-2xl text-gray-600"></i>
                    </div>
                </div>
            </div>

            <!-- Stat: Aperte -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Aperte</p>
                        <p class="text-3xl font-bold text-red-600">{{ $statsSegnalazioni['aperte'] }}</p>
                    </div>
                    <div class="p-3 bg-red-100 rounded-full">
                        <i class="fas fa-folder-open text-2xl text-red-600"></i>
                    </div>
                </div>
            </div>

            <!-- Stat: In Lavorazione -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">In Lavorazione</p>
                        <p class="text-3xl font-bold text-yellow-600">{{ $statsSegnalazioni['in_lavorazione'] }}</p>
                    </div>
                    <div class="p-3 bg-yellow-100 rounded-full">
                        <i class="fas fa-cog text-2xl text-yellow-600"></i>
                    </div>
                </div>
            </div>

            <!-- Stat: Risolte -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Risolte</p>
                        <p class="text-3xl font-bold text-green-600">{{ $statsSegnalazioni['risolte'] }}</p>
                    </div>
                    <div class="p-3 bg-green-100 rounded-full">
                        <i class="fas fa-check-circle text-2xl text-green-600"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Nuova Segnalazione -->
        <div class="bg-white rounded-lg shadow mb-6">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-xl font-bold text-gray-800 flex items-center">
                    <i class="fas fa-plus-circle text-fucsia-magia mr-3"></i>
                    Nuova Segnalazione
                </h2>
                <p class="text-sm text-gray-600 mt-1">Segnala un bug, proponi un miglioramento o richiedi assistenza</p>
            </div>

            <form method="POST" action="{{ route('admin.impostazioni.segnalazioni.store') }}" class="p-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <!-- Tipo -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Tipo di Segnalazione *
                        </label>
                        <select name="tipo" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent">
                            <option value="bug" {{ old('tipo') == 'bug' ? 'selected' : '' }}>🐛 Bug / Errore</option>
                            <option value="suggerimento" {{ old('tipo') == 'suggerimento' ? 'selected' : '' }}>💡 Suggerimento / Miglioramento</option>
                            <option value="altro" {{ old('tipo') == 'altro' ? 'selected' : '' }}>❓ Altro</option>
                        </select>
                    </div>

                    <!-- Priorità -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Priorità *
                        </label>
                        <select name="priorita" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent">
                            <option value="bassa" {{ old('priorita') == 'bassa' ? 'selected' : '' }}>Bassa</option>
                            <option value="media" {{ old('priorita', 'media') == 'media' ? 'selected' : '' }}>Media</option>
                            <option value="alta" {{ old('priorita') == 'alta' ? 'selected' : '' }}>Alta</option>
                        </select>
                    </div>
                </div>

                <!-- Titolo -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Titolo *
                    </label>
                    <input
                        type="text"
                        name="titolo"
                        required
                        maxlength="255"
                        value="{{ old('titolo') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent"
                        placeholder="Riassumi brevemente il problema o la richiesta"
                    >
                </div>

                <!-- Descrizione -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Descrizione *
                    </label>
                    <textarea
                        name="descrizione"
                        required
                        rows="5"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent"
                        placeholder="Descrivi dettagliatamente il problema, i passi per riprodurlo, o la funzionalità che vorresti..."
                    >{{ old('descrizione') }}</textarea>
                    <p class="text-xs text-gray-500 mt-1">
                        <i class="fas fa-info-circle mr-1"></i>
                        Più dettagli fornisci, più veloce sarà la risoluzione
                    </p>
                </div>

                <!-- Email Notifica -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Email per Notifiche (opzionale)
                    </label>
                    <input
                        type="email"
                        name="email_notifica"
                        value="{{ old('email_notifica', Auth::user()->email) }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent"
                        placeholder="tua-email@esempio.com"
                    >
                    <p class="text-xs text-gray-500 mt-1">
                        <i class="fas fa-envelope mr-1"></i>
                        Riceverai un'email quando lo stato della segnalazione cambierà
                    </p>
                </div>

                <!-- Pulsante Invia -->
                <div class="flex justify-end">
                    <button type="submit" class="px-6 py-3 bg-fucsia-magia text-white font-medium rounded-lg hover:bg-viola-magia transition-colors">
                        <i class="fas fa-paper-plane mr-2"></i>
                        Invia Segnalazione
                    </button>
                </div>
            </form>
        </div>

        <!-- Elenco Segnalazioni -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-xl font-bold text-gray-800 flex items-center">
                    <i class="fas fa-list text-fucsia-magia mr-3"></i>
                    {{ $isSuperAdmin ? 'Tutte le Segnalazioni' : 'Le Mie Segnalazioni' }}
                </h2>
            </div>

            @if($segnalazioni->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Titolo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipo</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Priorità</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Stato</th>
                            @if($isSuperAdmin)
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Utente</th>
                            @endif
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Data</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Azioni</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($segnalazioni as $seg)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 text-sm font-mono text-gray-700">#{{ $seg->id }}</td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ Str::limit($seg->titolo, 50) }}</div>
                                @if($seg->risposta)
                                <div class="text-xs text-green-600 mt-1">
                                    <i class="fas fa-reply mr-1"></i>
                                    {{ Str::limit($seg->risposta, 40) }}
                                </div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="flex items-center text-sm text-gray-700">
                                    <i class="fas {{ $seg->tipo_icona }} mr-2"></i>
                                    {{ $seg->tipo_label }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $seg->priorita_badge }}">
                                    {{ $seg->priorita_label }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $seg->stato_badge }}">
                                    {{ $seg->stato_label }}
                                </span>
                            </td>
                            @if($isSuperAdmin)
                            <td class="px-6 py-4 text-sm text-gray-700">
                                {{ $seg->utente->nome ?? 'N/D' }}
                            </td>
                            @endif
                            <td class="px-6 py-4 text-center text-sm text-gray-700">
                                {{ $seg->created_at->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('admin.impostazioni.segnalazioni.show', $seg) }}"
                                   class="text-fucsia-magia hover:text-viola-magia font-medium">
                                    <i class="fas fa-eye mr-1"></i>
                                    Dettagli
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Paginazione -->
            @if($segnalazioni->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $segnalazioni->links() }}
            </div>
            @endif

            @else
            <div class="p-12 text-center">
                <i class="fas fa-inbox text-6xl text-gray-300 mb-4"></i>
                <p class="text-gray-600 font-medium">Nessuna segnalazione presente</p>
                <p class="text-sm text-gray-500 mt-2">Compila il form sopra per creare la tua prima segnalazione</p>
            </div>
            @endif
        </div>

    </div>
</div>
