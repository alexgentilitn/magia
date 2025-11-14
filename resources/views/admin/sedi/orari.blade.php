@extends('layouts.admin')

@section('titolo', 'Gestione Orari Sede')

@section('contenuto')
<div class="p-6">
    <div class="mb-6">
        <a href="{{ route('admin.sedi.show', $sede->id) }}" class="text-fucsia-magia hover:text-viola-magia mb-2 inline-block">
            <i class="fas fa-arrow-left mr-2"></i> Torna al dettaglio
        </a>
        <h1 class="text-3xl font-bold text-gray-800">Orari di Apertura: {{ $sede->nome }}</h1>
        <p class="text-gray-600 mt-1">Configura gli orari di apertura per ogni giorno della settimana</p>
    </div>

    <form method="POST" action="{{ route('admin.sedi.salva-orari', $sede->id) }}" class="bg-white rounded-lg shadow p-6">
        @csrf

        <div class="space-y-6">
            @foreach($giorniSettimana as $giorno => $nomeGiorno)
                @php
                    $orarioGiorno = $sede->orari_apertura[$giorno] ?? null;
                    $aperto = $orarioGiorno['aperto'] ?? false;
                    $apertura = $orarioGiorno['apertura'] ?? '09:00';
                    $chiusura = $orarioGiorno['chiusura'] ?? '18:00';
                @endphp

                <div class="border rounded-lg p-4">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                            <input
                                type="checkbox"
                                name="aperto_{{ $giorno }}"
                                id="aperto_{{ $giorno }}"
                                value="1"
                                {{ $aperto ? 'checked' : '' }}
                                class="rounded border-gray-300 text-fucsia-magia mr-3"
                                onchange="toggleOrari('{{ $giorno }}')"
                            >
                            <label for="aperto_{{ $giorno }}" class="text-lg font-semibold text-gray-800">
                                {{ $nomeGiorno }}
                            </label>
                        </div>
                        <span class="text-sm text-gray-500">
                            {{ $aperto ? 'Aperto' : 'Chiuso' }}
                        </span>
                    </div>

                    <div id="orari_{{ $giorno }}" class="grid grid-cols-2 gap-4 {{ $aperto ? '' : 'hidden' }}">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Apertura</label>
                            <input
                                type="time"
                                name="apertura_{{ $giorno }}"
                                value="{{ $apertura }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent"
                            >
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Chiusura</label>
                            <input
                                type="time"
                                name="chiusura_{{ $giorno }}"
                                value="{{ $chiusura }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent"
                            >
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Azioni Rapide -->
        <div class="mt-6 p-4 bg-gray-50 rounded-lg">
            <p class="text-sm font-medium text-gray-700 mb-3">Azioni Rapide:</p>
            <div class="flex gap-2 flex-wrap">
                <button type="button" onclick="setOrariStandard()" class="px-3 py-2 bg-blue-100 text-blue-800 rounded hover:bg-blue-200 text-sm">
                    <i class="fas fa-clock mr-1"></i> 9:00-18:00 (Lun-Ven)
                </button>
                <button type="button" onclick="setOrariWeekend()" class="px-3 py-2 bg-green-100 text-green-800 rounded hover:bg-green-200 text-sm">
                    <i class="fas fa-calendar-week mr-1"></i> 9:00-13:00 (Sab)
                </button>
                <button type="button" onclick="chiudiTutto()" class="px-3 py-2 bg-red-100 text-red-800 rounded hover:bg-red-200 text-sm">
                    <i class="fas fa-times mr-1"></i> Chiudi Tutto
                </button>
                <button type="button" onclick="apriTutto()" class="px-3 py-2 bg-green-100 text-green-800 rounded hover:bg-green-200 text-sm">
                    <i class="fas fa-check mr-1"></i> Apri Tutto
                </button>
            </div>
        </div>

        <div class="mt-6 flex justify-end space-x-3">
            <a href="{{ route('admin.sedi.show', $sede->id) }}" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                Annulla
            </a>
            <button type="submit" class="px-4 py-2 bg-fucsia-magia text-white rounded-lg hover:bg-viola-magia">
                <i class="fas fa-save mr-2"></i> Salva Orari
            </button>
        </div>
    </form>
</div>

<script>
function toggleOrari(giorno) {
    const checkbox = document.getElementById('aperto_' + giorno);
    const orariDiv = document.getElementById('orari_' + giorno);

    if (checkbox.checked) {
        orariDiv.classList.remove('hidden');
    } else {
        orariDiv.classList.add('hidden');
    }
}

function setOrariStandard() {
    const giorni = ['lunedi', 'martedi', 'mercoledi', 'giovedi', 'venerdi'];
    giorni.forEach(giorno => {
        document.getElementById('aperto_' + giorno).checked = true;
        document.querySelector('input[name="apertura_' + giorno + '"]').value = '09:00';
        document.querySelector('input[name="chiusura_' + giorno + '"]').value = '18:00';
        document.getElementById('orari_' + giorno).classList.remove('hidden');
    });

    // Chiudi sabato e domenica
    ['sabato', 'domenica'].forEach(giorno => {
        document.getElementById('aperto_' + giorno).checked = false;
        document.getElementById('orari_' + giorno).classList.add('hidden');
    });
}

function setOrariWeekend() {
    document.getElementById('aperto_sabato').checked = true;
    document.querySelector('input[name="apertura_sabato"]').value = '09:00';
    document.querySelector('input[name="chiusura_sabato"]').value = '13:00';
    document.getElementById('orari_sabato').classList.remove('hidden');

    document.getElementById('aperto_domenica').checked = false;
    document.getElementById('orari_domenica').classList.add('hidden');
}

function chiudiTutto() {
    const giorni = ['lunedi', 'martedi', 'mercoledi', 'giovedi', 'venerdi', 'sabato', 'domenica'];
    giorni.forEach(giorno => {
        document.getElementById('aperto_' + giorno).checked = false;
        document.getElementById('orari_' + giorno).classList.add('hidden');
    });
}

function apriTutto() {
    const giorni = ['lunedi', 'martedi', 'mercoledi', 'giovedi', 'venerdi', 'sabato', 'domenica'];
    giorni.forEach(giorno => {
        document.getElementById('aperto_' + giorno).checked = true;
        document.getElementById('orari_' + giorno).classList.remove('hidden');
    });
}
</script>
@endsection
