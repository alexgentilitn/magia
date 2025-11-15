@extends('layouts.cliente')

@section('title', 'Le Mie Prenotazioni')

@section('content')
<div class="p-6">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">
            <i class="fas fa-calendar-check text-purple-600 mr-3"></i>Le Mie Prenotazioni
        </h1>
        <p class="text-gray-600">Gestisci le tue lezioni programmate</p>
    </div>

    <!-- Tabs -->
    <div class="mb-6 border-b border-gray-200">
        <nav class="-mb-px flex space-x-8">
            <button class="tab-button active border-b-2 border-purple-600 py-4 px-1 text-purple-600 font-semibold"
                    data-tab="prossime">
                Prossime Lezioni
            </button>
            <button class="tab-button border-b-2 border-transparent py-4 px-1 text-gray-500 hover:text-gray-700"
                    data-tab="passate">
                Lezioni Passate
            </button>
            <button class="tab-button border-b-2 border-transparent py-4 px-1 text-gray-500 hover:text-gray-700"
                    data-tab="annullate">
                Annullate
            </button>
        </nav>
    </div>

    <!-- Tab Content: Prossime -->
    <div id="tab-prossime" class="tab-content">
        @if($prenotazioniProssime->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($prenotazioniProssime as $prenotazione)
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition">
                        <div class="bg-gradient-to-r from-purple-600 to-pink-600 p-4 text-white">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="text-sm opacity-90">{{ \Carbon\Carbon::parse($prenotazione->data)->locale('it')->isoFormat('dddd') }}</p>
                                    <p class="text-2xl font-bold">{{ \Carbon\Carbon::parse($prenotazione->data)->format('d M') }}</p>
                                </div>
                                <span class="bg-white/20 px-3 py-1 rounded-full text-sm">
                                    {{ \Carbon\Carbon::parse($prenotazione->ora_inizio)->format('H:i') }}
                                </span>
                            </div>
                        </div>

                        <div class="p-6">
                            <h3 class="font-bold text-gray-800 mb-2">{{ $prenotazione->programma->nome ?? 'Lezione' }}</h3>
                            <div class="space-y-2 text-sm text-gray-600 mb-4">
                                <p><i class="fas fa-user-tie text-purple-600 w-5"></i> {{ $prenotazione->professionista->nome ?? 'Da definire' }}</p>
                                <p><i class="fas fa-map-marker-alt text-purple-600 w-5"></i> {{ $prenotazione->sede->nome ?? 'Sede' }}</p>
                                <p><i class="fas fa-clock text-purple-600 w-5"></i> {{ \Carbon\Carbon::parse($prenotazione->ora_inizio)->format('H:i') }} - {{ \Carbon\Carbon::parse($prenotazione->ora_fine)->format('H:i') }}</p>
                            </div>

                            @if($prenotazione->stato === 'confermata')
                                <span class="inline-block bg-green-100 text-green-800 px-3 py-1 rounded-full text-xs font-semibold">
                                    <i class="fas fa-check-circle mr-1"></i>Confermata
                                </span>
                            @elseif($prenotazione->stato === 'in_attesa')
                                <span class="inline-block bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-xs font-semibold">
                                    <i class="fas fa-clock mr-1"></i>In Attesa
                                </span>
                            @endif

                            <div class="mt-4 flex gap-2">
                                <a href="{{ route('cliente.prenotazioni.show', $prenotazione->id) }}"
                                   class="flex-1 text-center bg-purple-100 text-purple-700 px-4 py-2 rounded-lg text-sm font-semibold hover:bg-purple-200 transition">
                                    Dettagli
                                </a>
                                <button onclick="annullaPrenotazione({{ $prenotazione->id }})"
                                        class="text-red-600 hover:text-red-800 px-3">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-white rounded-xl shadow-lg p-12 text-center">
                <i class="fas fa-calendar-times text-6xl text-gray-300 mb-4"></i>
                <h3 class="text-xl font-bold text-gray-700 mb-2">Nessuna Prenotazione</h3>
                <p class="text-gray-600 mb-6">Non hai prenotazioni per le prossime lezioni</p>
                <a href="{{ route('cliente.dashboard') }}"
                   class="inline-block bg-purple-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-purple-700 transition">
                    Prenota Ora
                </a>
            </div>
        @endif
    </div>

    <!-- Tab Content: Passate -->
    <div id="tab-passate" class="tab-content hidden">
        @if($prenotazioniPassate->count() > 0)
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Data</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Lezione</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Istruttore</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Presenza</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @foreach($prenotazioniPassate as $pren)
                            <tr>
                                <td class="px-6 py-4">{{ \Carbon\Carbon::parse($pren->data)->format('d/m/Y H:i') }}</td>
                                <td class="px-6 py-4">{{ $pren->programma->nome ?? '-' }}</td>
                                <td class="px-6 py-4">{{ $pren->professionista->nome ?? '-' }}</td>
                                <td class="px-6 py-4">
                                    @if($pren->presenza_confermata)
                                        <span class="text-green-600"><i class="fas fa-check-circle mr-1"></i>Presente</span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="bg-white rounded-xl shadow-lg p-12 text-center">
                <p class="text-gray-600">Nessuna lezione passata</p>
            </div>
        @endif
    </div>

    <!-- Tab Content: Annullate -->
    <div id="tab-annullate" class="tab-content hidden">
        <div class="bg-white rounded-xl shadow-lg p-12 text-center">
            <p class="text-gray-600">Nessuna prenotazione annullata</p>
        </div>
    </div>

</div>

<script>
    // Tab switching
    document.querySelectorAll('.tab-button').forEach(button => {
        button.addEventListener('click', () => {
            const tabName = button.dataset.tab;

            // Update buttons
            document.querySelectorAll('.tab-button').forEach(btn => {
                btn.classList.remove('active', 'border-purple-600', 'text-purple-600');
                btn.classList.add('border-transparent', 'text-gray-500');
            });
            button.classList.add('active', 'border-purple-600', 'text-purple-600');

            // Update content
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.add('hidden');
            });
            document.getElementById(`tab-${tabName}`).classList.remove('hidden');
        });
    });

    function annullaPrenotazione(id) {
        if (confirm('Sei sicura di voler annullare questa prenotazione?')) {
            // Implementa logica annullamento
            alert('Funzionalità da implementare');
        }
    }
</script>
@endsection
