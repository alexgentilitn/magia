@extends('layouts.admin')

@section('titolo', 'Gestione Prenotazioni')

@section('contenuto')
<div class="p-6 max-w-7xl mx-auto">

    <!-- Header con gradiente -->
    <div class="bg-gradient-to-r from-viola-magia to-fucsia-magia rounded-lg p-6 text-white mb-6 shadow-lg">
        <div class="flex flex-col md:flex-row md:items-start md:justify-between">
            <div class="mb-4 md:mb-0 flex-1">
                <a href="{{ route('admin.lezioni.show', $lezione->id) }}" class="text-white hover:text-gray-200 mb-3 inline-block opacity-90 hover:opacity-100 transition">
                    <i class="fas fa-arrow-left mr-2"></i> Torna ai dettagli lezione
                </a>
                <h1 class="text-3xl font-bold mb-2">Gestione Prenotazioni</h1>
                <p class="text-lg opacity-90">{{ $lezione->titolo }}</p>
                <div class="flex flex-wrap items-center gap-4 text-sm opacity-90 mt-2">
                    <span><i class="fas fa-calendar mr-2"></i>{{ $lezione->data->format('d/m/Y') }}</span>
                    <span><i class="fas fa-clock mr-2"></i>{{ $lezione->range_orario }}</span>
                </div>
            </div>
            <div class="flex-shrink-0">
                <div class="bg-white bg-opacity-20 rounded-lg p-4 text-center backdrop-blur">
                    <p class="text-xs uppercase tracking-wide opacity-90 mb-1">Posti Occupati</p>
                    <p class="text-3xl font-bold">{{ $lezione->posti_occupati }}/{{ $lezione->posti_totali }}</p>
                    <div class="w-full bg-white bg-opacity-30 rounded-full h-2 mt-2">
                        <div class="bg-white h-2 rounded-full transition-all" style="width: {{ $lezione->percentuale_occupazione }}%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Cards Info -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <!-- Prenotati -->
        <div class="bg-blue-50 rounded-lg p-5 border-l-4 border-blue-500 shadow-sm">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center">
                    <i class="fas fa-clock text-white text-lg"></i>
                </div>
                <div class="ml-4">
                    <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Prenotati</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $lezione->clienti->where('pivot.stato', 'prenotato')->count() }}</p>
                </div>
            </div>
        </div>

        <!-- Presenti -->
        <div class="bg-green-50 rounded-lg p-5 border-l-4 border-green-500 shadow-sm">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-green-500 rounded-full flex items-center justify-center">
                    <i class="fas fa-check-circle text-white text-lg"></i>
                </div>
                <div class="ml-4">
                    <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Presenti</p>
                    <p class="text-2xl font-bold text-green-600">{{ $lezione->clienti->where('pivot.stato', 'presente')->count() }}</p>
                </div>
            </div>
        </div>

        <!-- Assenti -->
        <div class="bg-red-50 rounded-lg p-5 border-l-4 border-red-500 shadow-sm">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-red-500 rounded-full flex items-center justify-center">
                    <i class="fas fa-times-circle text-white text-lg"></i>
                </div>
                <div class="ml-4">
                    <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Assenti</p>
                    <p class="text-2xl font-bold text-red-600">{{ $lezione->clienti->where('pivot.stato', 'assente')->count() }}</p>
                </div>
            </div>
        </div>

        <!-- Disponibili -->
        <div class="bg-purple-50 rounded-lg p-5 border-l-4 border-viola-magia shadow-sm">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-viola-magia rounded-full flex items-center justify-center">
                    <i class="fas fa-chair text-white text-lg"></i>
                </div>
                <div class="ml-4">
                    <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Disponibili</p>
                    <p class="text-2xl font-bold {{ $lezione->postiDisponibili() > 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ $lezione->postiDisponibili() }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Bottone Aggiungi Prenotazione -->
    @if($lezione->postiDisponibili() > 0)
    <div class="mb-6">
        <button onclick="document.getElementById('modalAggiungi').classList.remove('hidden')"
                class="inline-flex items-center px-5 py-3 bg-gradient-to-r from-viola-magia to-fucsia-magia text-white rounded-lg hover:shadow-lg transition-all font-medium">
            <i class="fas fa-plus-circle mr-2"></i> Aggiungi Prenotazione
        </button>
    </div>
    @endif

    <!-- Lista Prenotazioni -->
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="bg-gradient-to-r from-viola-magia to-fucsia-magia px-6 py-4">
            <h2 class="text-xl font-bold text-white">
                <i class="fas fa-users mr-2"></i>
                Lista Prenotazioni ({{ $lezione->clienti->count() }})
            </h2>
        </div>

        @if($lezione->clienti->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-gray-700">Cliente</th>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-gray-700">Contatti</th>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-gray-700">Data Prenotazione</th>
                        <th class="px-6 py-4 text-center text-xs font-bold uppercase tracking-wider text-gray-700">Check-in</th>
                        <th class="px-6 py-4 text-center text-xs font-bold uppercase tracking-wider text-gray-700">Check-out</th>
                        <th class="px-6 py-4 text-center text-xs font-bold uppercase tracking-wider text-gray-700">Stato</th>
                        <th class="px-6 py-4 text-center text-xs font-bold uppercase tracking-wider text-gray-700">Azioni</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($lezione->clienti->sortBy('pivot.data_prenotazione') as $cliente)
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
                                    @if($cliente->cliente && $cliente->cliente->codice_fiscale)
                                    <div class="text-xs text-gray-500">
                                        CF: {{ $cliente->cliente->codice_fiscale }}
                                    </div>
                                    @endif
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
                        <td class="px-6 py-4 text-center">
                            @if($cliente->pivot->check_in)
                                <span class="text-sm font-medium text-green-600">
                                    <i class="fas fa-check mr-1"></i>
                                    {{ \Carbon\Carbon::parse($cliente->pivot->check_in)->format('H:i') }}
                                </span>
                            @else
                                <form action="{{ route('admin.lezioni.check-in', [$lezione->id, $cliente->id]) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="px-3 py-1 bg-green-500 text-white text-xs rounded hover:bg-green-600 transition">
                                        <i class="fas fa-sign-in-alt mr-1"></i> Check-in
                                    </button>
                                </form>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($cliente->pivot->check_out)
                                <span class="text-sm font-medium text-blue-600">
                                    <i class="fas fa-check mr-1"></i>
                                    {{ \Carbon\Carbon::parse($cliente->pivot->check_out)->format('H:i') }}
                                </span>
                            @elseif($cliente->pivot->check_in)
                                <form action="{{ route('admin.lezioni.check-out', [$lezione->id, $cliente->id]) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="px-3 py-1 bg-blue-500 text-white text-xs rounded hover:bg-blue-600 transition">
                                        <i class="fas fa-sign-out-alt mr-1"></i> Check-out
                                    </button>
                                </form>
                            @else
                                <span class="text-xs text-gray-400">-</span>
                            @endif
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
                        <td class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center space-x-2">
                                <!-- Segna Assente -->
                                @if($cliente->pivot->stato !== 'assente')
                                <form action="{{ route('admin.lezioni.segna-assente', [$lezione->id, $cliente->id]) }}" method="POST" class="inline" id="assente-form-{{ $cliente->id }}">
                                    @csrf
                                    <button type="button"
                                            onclick="confermaAzione('assente-form-{{ $cliente->id }}', 'Segnare come assente?', '{{ $cliente->nome }} {{ $cliente->cognome }} sarà segnato come assente a questa lezione.', 'Sì, segna assente')"
                                            class="px-2 py-1 bg-red-500 text-white text-xs rounded hover:bg-red-600 transition"
                                            title="Segna come assente">
                                        <i class="fas fa-user-times"></i>
                                    </button>
                                </form>
                                @else
                                <!-- Annulla Assenza -->
                                <form action="{{ route('admin.lezioni.annulla-assenza', [$lezione->id, $cliente->id]) }}" method="POST" class="inline" id="annulla-assenza-form-{{ $cliente->id }}">
                                    @csrf
                                    <button type="button"
                                            onclick="confermaAzione('annulla-assenza-form-{{ $cliente->id }}', 'Annullare l\'assenza?', '{{ $cliente->nome }} {{ $cliente->cognome }} sarà riportato allo stato prenotato.', 'Sì, annulla assenza')"
                                            class="px-2 py-1 bg-green-500 text-white text-xs rounded hover:bg-green-600 transition"
                                            title="Annulla assenza">
                                        <i class="fas fa-user-check"></i>
                                    </button>
                                </form>
                                @endif

                                <!-- Rimuovi Prenotazione -->
                                <form action="{{ route('admin.lezioni.rimuovi-prenotazione', [$lezione->id, $cliente->id]) }}" method="POST" class="inline" id="rimuovi-pren-form-{{ $cliente->id }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button"
                                            onclick="confermaEliminazione('rimuovi-pren-form-{{ $cliente->id }}', 'Rimuovere la prenotazione?', 'La prenotazione di {{ $cliente->nome }} {{ $cliente->cognome }} sarà rimossa definitivamente.')"
                                            class="px-2 py-1 bg-gray-500 text-white text-xs rounded hover:bg-gray-600 transition"
                                            title="Rimuovi prenotazione">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="text-center py-16">
            <div class="inline-block p-6 bg-gray-100 rounded-full mb-4">
                <i class="fas fa-users text-gray-400 text-6xl"></i>
            </div>
            <p class="text-gray-600 font-medium text-lg">Nessuna prenotazione</p>
            <p class="text-gray-500 text-sm mt-2">Clicca su "Aggiungi Prenotazione" per iniziare</p>
        </div>
        @endif
    </div>

</div>

<!-- Modal Aggiungi Prenotazione -->
<div id="modalAggiungi" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-2/3 lg:w-1/2 shadow-lg rounded-lg bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-user-plus text-fucsia-magia mr-2"></i>
                Aggiungi Prenotazione
            </h3>
            <button onclick="document.getElementById('modalAggiungi').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-2xl"></i>
            </button>
        </div>

        <form action="{{ route('admin.lezioni.aggiungi-prenotazione', $lezione->id) }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Seleziona Cliente <span class="text-red-500">*</span>
                </label>
                <select name="cliente_id" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent">
                    <option value="">Seleziona un cliente...</option>
                    @foreach(\App\Models\Utente::where('tipo_utente', 'cliente')->where('attivo', true)->orderBy('cognome')->orderBy('nome')->get() as $utente)
                        <option value="{{ $utente->id }}">{{ $utente->nome }} {{ $utente->cognome }} - {{ $utente->email }}</option>
                    @endforeach
                </select>
            </div>

            <div class="bg-blue-50 rounded-lg p-4">
                <p class="text-sm text-blue-800">
                    <i class="fas fa-info-circle mr-2"></i>
                    Posti disponibili: <strong>{{ $lezione->postiDisponibili() }}</strong>
                </p>
            </div>

            <div class="pt-4 border-t border-gray-200 flex justify-end space-x-3">
                <button type="button"
                        onclick="document.getElementById('modalAggiungi').classList.add('hidden')"
                        class="px-5 py-2.5 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition font-medium">
                    <i class="fas fa-times mr-2"></i>
                    Annulla
                </button>
                <button type="submit"
                        class="px-5 py-2.5 bg-gradient-to-r from-viola-magia to-fucsia-magia text-white rounded-lg hover:shadow-lg transition font-medium">
                    <i class="fas fa-save mr-2"></i>
                    Conferma Prenotazione
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Click fuori dal modal per chiuderlo -->
<script>
document.getElementById('modalAggiungi').addEventListener('click', function(e) {
    if (e.target === this) {
        this.classList.add('hidden');
    }
});
</script>

@endsection
