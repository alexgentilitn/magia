@extends('layouts.admin')

@section('titolo', 'Gestione Disponibilità')

@section('contenuto')
<div class="p-6">
    <div class="mb-6">
        <a href="{{ route('admin.professionisti.show', $professionista->id) }}" class="text-fucsia-magia hover:text-viola-magia mb-2 inline-block">
            <i class="fas fa-arrow-left mr-2"></i> Torna al dettaglio
        </a>
        <h1 class="text-3xl font-bold text-gray-800">Disponibilità: {{ $professionista->nome }} {{ $professionista->cognome }}</h1>
        <p class="text-gray-600 mt-1">Configura gli orari di disponibilità per ogni giorno della settimana</p>
    </div>

    <form method="POST" action="{{ route('admin.professionisti.salva-disponibilita', $professionista->id) }}" class="bg-white rounded-lg shadow p-6">
        @csrf

        <div class="space-y-6">
            @foreach($giorniSettimana as $giorno => $nomeGiorno)
                @php
                    $disponibilitaGiorno = $professionista->disponibilita_settimanale[$giorno] ?? null;
                    $disponibile = $disponibilitaGiorno['disponibile'] ?? false;
                    $dalle = $disponibilitaGiorno['dalle'] ?? '09:00';
                    $alle = $disponibilitaGiorno['alle'] ?? '18:00';
                @endphp

                <div class="border rounded-lg p-4">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                            <input
                                type="checkbox"
                                name="disponibile_{{ $giorno }}"
                                id="disponibile_{{ $giorno }}"
                                value="1"
                                {{ $disponibile ? 'checked' : '' }}
                                class="rounded border-gray-300 text-fucsia-magia mr-3"
                                onchange="toggleDisponibilita('{{ $giorno }}')"
                            >
                            <label for="disponibile_{{ $giorno }}" class="text-lg font-semibold text-gray-800">
                                {{ $nomeGiorno }}
                            </label>
                        </div>
                        <span class="text-sm text-gray-500">
                            {{ $disponibile ? 'Disponibile' : 'Non disponibile' }}
                        </span>
                    </div>

                    <div id="orari_{{ $giorno }}" class="grid grid-cols-2 gap-4 {{ $disponibile ? '' : 'hidden' }}">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Dalle</label>
                            <input
                                type="time"
                                name="dalle_{{ $giorno }}"
                                value="{{ $dalle }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent"
                            >
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Alle</label>
                            <input
                                type="time"
                                name="alle_{{ $giorno }}"
                                value="{{ $alle }}"
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
                <button type="button" onclick="setOrariLavoroStandard()" class="px-3 py-2 bg-blue-100 text-blue-800 rounded hover:bg-blue-200 text-sm">
                    <i class="fas fa-clock mr-1"></i> 9:00-18:00 (Lun-Ven)
                </button>
                <button type="button" onclick="setOrariMattino()" class="px-3 py-2 bg-green-100 text-green-800 rounded hover:bg-green-200 text-sm">
                    <i class="fas fa-sun mr-1"></i> 8:00-13:00 (Tutti i giorni)
                </button>
                <button type="button" onclick="setOrariPomeriggio()" class="px-3 py-2 bg-purple-100 text-purple-800 rounded hover:bg-purple-200 text-sm">
                    <i class="fas fa-cloud-sun mr-1"></i> 14:00-20:00 (Tutti i giorni)
                </button>
                <button type="button" onclick="setWeekend()" class="px-3 py-2 bg-yellow-100 text-yellow-800 rounded hover:bg-yellow-200 text-sm">
                    <i class="fas fa-calendar-week mr-1"></i> Solo Weekend
                </button>
                <button type="button" onclick="disabilitaTutto()" class="px-3 py-2 bg-red-100 text-red-800 rounded hover:bg-red-200 text-sm">
                    <i class="fas fa-times mr-1"></i> Disabilita Tutto
                </button>
                <button type="button" onclick="abilitaTutto()" class="px-3 py-2 bg-green-100 text-green-800 rounded hover:bg-green-200 text-sm">
                    <i class="fas fa-check mr-1"></i> Abilita Tutto
                </button>
            </div>
        </div>

        <div class="mt-6 flex justify-end space-x-3">
            <a href="{{ route('admin.professionisti.show', $professionista->id) }}" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                Annulla
            </a>
            <button type="submit" class="px-4 py-2 bg-fucsia-magia text-white rounded-lg hover:bg-viola-magia">
                <i class="fas fa-save mr-2"></i> Salva Disponibilità
            </button>
        </div>
    </form>
</div>

<script>
function toggleDisponibilita(giorno) {
    const checkbox = document.getElementById('disponibile_' + giorno);
    const orariDiv = document.getElementById('orari_' + giorno);

    if (checkbox.checked) {
        orariDiv.classList.remove('hidden');
    } else {
        orariDiv.classList.add('hidden');
    }
}

function setOrariLavoroStandard() {
    const giorni = ['lunedi', 'martedi', 'mercoledi', 'giovedi', 'venerdi'];
    giorni.forEach(giorno => {
        document.getElementById('disponibile_' + giorno).checked = true;
        document.querySelector('input[name="dalle_' + giorno + '"]').value = '09:00';
        document.querySelector('input[name="alle_' + giorno + '"]').value = '18:00';
        document.getElementById('orari_' + giorno).classList.remove('hidden');
    });

    // Disabilita weekend
    ['sabato', 'domenica'].forEach(giorno => {
        document.getElementById('disponibile_' + giorno).checked = false;
        document.getElementById('orari_' + giorno).classList.add('hidden');
    });
}

function setOrariMattino() {
    const giorni = ['lunedi', 'martedi', 'mercoledi', 'giovedi', 'venerdi', 'sabato', 'domenica'];
    giorni.forEach(giorno => {
        document.getElementById('disponibile_' + giorno).checked = true;
        document.querySelector('input[name="dalle_' + giorno + '"]').value = '08:00';
        document.querySelector('input[name="alle_' + giorno + '"]').value = '13:00';
        document.getElementById('orari_' + giorno).classList.remove('hidden');
    });
}

function setOrariPomeriggio() {
    const giorni = ['lunedi', 'martedi', 'mercoledi', 'giovedi', 'venerdi', 'sabato', 'domenica'];
    giorni.forEach(giorno => {
        document.getElementById('disponibile_' + giorno).checked = true;
        document.querySelector('input[name="dalle_' + giorno + '"]').value = '14:00';
        document.querySelector('input[name="alle_' + giorno + '"]').value = '20:00';
        document.getElementById('orari_' + giorno).classList.remove('hidden');
    });
}

function setWeekend() {
    // Disabilita settimana
    const settimana = ['lunedi', 'martedi', 'mercoledi', 'giovedi', 'venerdi'];
    settimana.forEach(giorno => {
        document.getElementById('disponibile_' + giorno).checked = false;
        document.getElementById('orari_' + giorno).classList.add('hidden');
    });

    // Abilita weekend
    ['sabato', 'domenica'].forEach(giorno => {
        document.getElementById('disponibile_' + giorno).checked = true;
        document.querySelector('input[name="dalle_' + giorno + '"]').value = '09:00';
        document.querySelector('input[name="alle_' + giorno + '"]').value = '18:00';
        document.getElementById('orari_' + giorno).classList.remove('hidden');
    });
}

function disabilitaTutto() {
    const giorni = ['lunedi', 'martedi', 'mercoledi', 'giovedi', 'venerdi', 'sabato', 'domenica'];
    giorni.forEach(giorno => {
        document.getElementById('disponibile_' + giorno).checked = false;
        document.getElementById('orari_' + giorno).classList.add('hidden');
    });
}

function abilitaTutto() {
    const giorni = ['lunedi', 'martedi', 'mercoledi', 'giovedi', 'venerdi', 'sabato', 'domenica'];
    giorni.forEach(giorno => {
        document.getElementById('disponibile_' + giorno).checked = true;
        document.getElementById('orari_' + giorno).classList.remove('hidden');
    });
}
</script>
@endsection
