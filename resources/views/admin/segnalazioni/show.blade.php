@extends('layouts.admin')

@section('titolo', 'Dettaglio Segnalazione #' . $segnalazione->id)

@section('contenuto')
<div class="p-6 max-w-5xl mx-auto">

    <!-- Header con Breadcrumb -->
    <div class="mb-6">
        <nav class="text-sm mb-4">
            <a href="{{ route('admin.impostazioni.index') }}" class="text-fucsia-magia hover:underline">Impostazioni</a>
            <span class="mx-2 text-gray-400">/</span>
            <span class="text-gray-600">Segnalazione #{{ $segnalazione->id }}</span>
        </nav>

        <div class="flex items-center justify-between">
            <h1 class="text-3xl font-bold text-gray-800">
                <i class="fas {{ $segnalazione->tipo_icona }} text-fucsia-magia mr-3"></i>
                Segnalazione #{{ $segnalazione->id }}
            </h1>

            <a href="{{ route('admin.impostazioni.index') }}?tab=segnalazioni" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                <i class="fas fa-arrow-left mr-2"></i>
                Torna all'elenco
            </a>
        </div>
    </div>

    <!-- Messaggi di successo/errore -->
    @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 rounded-lg">
            <div class="flex items-center">
                <i class="fas fa-check-circle text-green-500 text-xl mr-3"></i>
                <p class="text-green-800 font-medium">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-lg">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle text-red-500 text-xl mr-3"></i>
                <p class="text-red-800 font-medium">{{ session('error') }}</p>
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-lg">
            <div class="flex items-start">
                <i class="fas fa-exclamation-triangle text-red-500 text-xl mr-3 mt-1"></i>
                <div>
                    <p class="text-red-800 font-medium mb-2">Errori di validazione:</p>
                    <ul class="list-disc list-inside text-red-700">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Colonna Principale: Dettagli Segnalazione -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Card: Informazioni Segnalazione -->
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-xl font-bold text-gray-800">{{ $segnalazione->titolo }}</h2>
                    <div class="flex items-center space-x-4 mt-3">
                        <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $segnalazione->tipo_badge }}">
                            <i class="fas {{ $segnalazione->tipo_icona }} mr-1"></i>
                            {{ $segnalazione->tipo_label }}
                        </span>
                        <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $segnalazione->priorita_badge }}">
                            Priorità: {{ $segnalazione->priorita_label }}
                        </span>
                        <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $segnalazione->stato_badge }}">
                            {{ $segnalazione->stato_label }}
                        </span>
                    </div>
                </div>

                <div class="p-6">
                    <h3 class="font-bold text-gray-800 mb-3">Descrizione:</h3>
                    <div class="prose max-w-none text-gray-700 whitespace-pre-wrap bg-gray-50 p-4 rounded-lg border border-gray-200">{{ $segnalazione->descrizione }}</div>
                </div>
            </div>

            <!-- Card: Risposta/Soluzione (se presente) -->
            @if($segnalazione->risposta)
            <div class="bg-green-50 rounded-lg shadow border-l-4 border-green-500">
                <div class="p-6">
                    <div class="flex items-start">
                        <div class="p-3 bg-green-100 rounded-full mr-4">
                            <i class="fas fa-reply text-2xl text-green-600"></i>
                        </div>
                        <div class="flex-1">
                            <h3 class="font-bold text-green-900 mb-2 text-lg">Risposta / Soluzione</h3>
                            <div class="text-green-800 whitespace-pre-wrap">{{ $segnalazione->risposta }}</div>

                            @if($segnalazione->risolutore)
                            <div class="mt-4 pt-4 border-t border-green-200 text-sm text-green-700">
                                <i class="fas fa-user-check mr-2"></i>
                                Gestita da: <strong>{{ $segnalazione->risolutore->name ?? 'N/D' }}</strong>
                                @if($segnalazione->risolto_il)
                                    il {{ $segnalazione->risolto_il->format('d/m/Y H:i') }}
                                @endif
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Form Gestione (Solo Super Admin) -->
            @if($isSuperAdmin)
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-viola-magia to-fucsia-magia">
                    <h2 class="text-xl font-bold text-white flex items-center">
                        <i class="fas fa-tools mr-3"></i>
                        Gestione Segnalazione (Super Admin)
                    </h2>
                </div>

                <form method="POST" action="{{ route('admin.impostazioni.segnalazioni.update', $segnalazione) }}" class="p-6">
                    @csrf
                    @method('PUT')

                    <!-- Stato -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Cambia Stato *
                        </label>
                        <select name="stato" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent">
                            <option value="aperta" {{ $segnalazione->stato == 'aperta' ? 'selected' : '' }}>🔴 Aperta</option>
                            <option value="in_lavorazione" {{ $segnalazione->stato == 'in_lavorazione' ? 'selected' : '' }}>🟡 In Lavorazione</option>
                            <option value="risolta" {{ $segnalazione->stato == 'risolta' ? 'selected' : '' }}>🟢 Risolta</option>
                        </select>
                    </div>

                    <!-- Risposta -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Risposta / Soluzione
                        </label>
                        <textarea
                            name="risposta"
                            rows="6"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent"
                            placeholder="Descrivi la soluzione o aggiungi note sullo stato della segnalazione..."
                        >{{ old('risposta', $segnalazione->risposta) }}</textarea>
                        <p class="text-xs text-gray-500 mt-1">
                            <i class="fas fa-info-circle mr-1"></i>
                            Questa risposta verrà inviata via email all'utente se ha fornito un indirizzo email
                        </p>
                    </div>

                    <!-- Pulsanti -->
                    <div class="flex justify-between items-center">
                        <!-- Elimina (solo se non risolta) -->
                        @if($segnalazione->stato !== 'risolta')
                        <form method="POST" action="{{ route('admin.impostazioni.segnalazioni.destroy', $segnalazione) }}" class="inline" id="delete-form">
                            @csrf
                            @method('DELETE')
                            <button type="button" onclick="confermaEliminazione()" class="px-4 py-2 bg-red-600 text-white font-medium rounded-lg hover:bg-red-700 transition-colors">
                                <i class="fas fa-trash mr-2"></i>
                                Elimina
                            </button>
                        </form>
                        @else
                        <div></div>
                        @endif

                        <!-- Salva -->
                        <button type="submit" class="px-6 py-3 bg-fucsia-magia text-white font-medium rounded-lg hover:bg-viola-magia transition-colors">
                            <i class="fas fa-save mr-2"></i>
                            Aggiorna Segnalazione
                        </button>
                    </div>
                </form>
            </div>
            @endif

        </div>

        <!-- Sidebar: Informazioni -->
        <div class="lg:col-span-1 space-y-6">

            <!-- Card: Informazioni Utente -->
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="font-bold text-gray-800 flex items-center">
                        <i class="fas fa-user text-fucsia-magia mr-2"></i>
                        Creata da
                    </h3>
                </div>
                <div class="p-6">
                    <div class="flex items-center mb-4">
                        <div class="h-12 w-12 bg-gradient-to-br from-viola-magia to-fucsia-magia rounded-full flex items-center justify-center text-white font-bold text-lg">
                            {{ substr($segnalazione->utente->name ?? 'ND', 0, 2) }}
                        </div>
                        <div class="ml-3">
                            <p class="font-medium text-gray-900">{{ $segnalazione->utente->name ?? 'N/D' }}</p>
                            <p class="text-sm text-gray-500">{{ ucfirst($segnalazione->utente->tipo_utente ?? 'N/D') }}</p>
                        </div>
                    </div>

                    @if($segnalazione->email_notifica)
                    <div class="pt-4 border-t border-gray-200">
                        <p class="text-xs text-gray-500 mb-1">Email notifiche:</p>
                        <p class="text-sm text-gray-700 font-mono break-all">{{ $segnalazione->email_notifica }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Card: Timeline -->
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="font-bold text-gray-800 flex items-center">
                        <i class="fas fa-clock text-fucsia-magia mr-2"></i>
                        Timeline
                    </h3>
                </div>
                <div class="p-6 space-y-4">
                    <!-- Creazione -->
                    <div class="flex items-start">
                        <div class="p-2 bg-blue-100 rounded-full mr-3">
                            <i class="fas fa-plus text-blue-600"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">Creata</p>
                            <p class="text-xs text-gray-500">{{ $segnalazione->created_at->format('d/m/Y H:i') }}</p>
                            <p class="text-xs text-gray-400">{{ $segnalazione->created_at->diffForHumans() }}</p>
                        </div>
                    </div>

                    <!-- Ultimo aggiornamento -->
                    @if($segnalazione->updated_at != $segnalazione->created_at)
                    <div class="flex items-start">
                        <div class="p-2 bg-yellow-100 rounded-full mr-3">
                            <i class="fas fa-edit text-yellow-600"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">Aggiornata</p>
                            <p class="text-xs text-gray-500">{{ $segnalazione->updated_at->format('d/m/Y H:i') }}</p>
                            <p class="text-xs text-gray-400">{{ $segnalazione->updated_at->diffForHumans() }}</p>
                        </div>
                    </div>
                    @endif

                    <!-- Risoluzione -->
                    @if($segnalazione->risolto_il)
                    <div class="flex items-start">
                        <div class="p-2 bg-green-100 rounded-full mr-3">
                            <i class="fas fa-check text-green-600"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">Risolta</p>
                            <p class="text-xs text-gray-500">{{ $segnalazione->risolto_il->format('d/m/Y H:i') }}</p>
                            <p class="text-xs text-gray-400">{{ $segnalazione->risolto_il->diffForHumans() }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

        </div>

    </div>

</div>

@if($isSuperAdmin)
<script>
function confermaEliminazione() {
    if (confirm('Sei sicuro di voler eliminare questa segnalazione?\n\nL\'operazione è irreversibile.')) {
        document.getElementById('delete-form').submit();
    }
}
</script>
@endif

@endsection
