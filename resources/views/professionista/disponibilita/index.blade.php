@extends('layouts.professionista')

@section('titolo', 'Disponibilità')
@section('sottotitolo', 'Gestisci le tue disponibilità settimanali')

@section('content')
<div class="space-y-6">

    <div class="bg-white rounded-lg shadow p-6">
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-gray-900">Gestione Disponibilità</h3>
            <p class="text-sm text-gray-600 mt-2">
                Imposta i tuoi orari di disponibilità settimanali. Questi verranno utilizzati per programmare le lezioni.
            </p>
        </div>

        <form method="POST" action="{{ route('professionista.disponibilita.salva') }}">
            @csrf

            @php
                $giorniSettimana = [
                    'lunedi' => 'Lunedì',
                    'martedi' => 'Martedì',
                    'mercoledi' => 'Mercoledì',
                    'giovedi' => 'Giovedì',
                    'venerdi' => 'Venerdì',
                    'sabato' => 'Sabato',
                    'domenica' => 'Domenica',
                ];
            @endphp

            <!-- Preset Rapidi -->
            <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                <h4 class="text-sm font-semibold text-gray-700 mb-3">Preset Rapidi:</h4>
                <div class="flex flex-wrap gap-2">
                    <button type="button" onclick="applicaPreset('lun-ven', '09:00', '18:00')" class="px-3 py-1 bg-blue-600 text-white text-sm rounded hover:bg-blue-700">
                        Lun-Ven 9-18
                    </button>
                    <button type="button" onclick="applicaPreset('lun-ven', '08:00', '13:00')" class="px-3 py-1 bg-green-600 text-white text-sm rounded hover:bg-green-700">
                        Mattino (8-13)
                    </button>
                    <button type="button" onclick="applicaPreset('lun-ven', '14:00', '19:00')" class="px-3 py-1 bg-purple-600 text-white text-sm rounded hover:bg-purple-700">
                        Pomeriggio (14-19)
                    </button>
                    <button type="button" onclick="applicaPreset('sabato-domenica', '09:00', '13:00')" class="px-3 py-1 bg-orange-600 text-white text-sm rounded hover:bg-orange-700">
                        Weekend
                    </button>
                    <button type="button" onclick="azzeraTutto()" class="px-3 py-1 bg-red-600 text-white text-sm rounded hover:bg-red-700">
                        Azzera Tutto
                    </button>
                </div>
            </div>

            <!-- Griglia Giorni -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                @foreach($giorniSettimana as $codice => $nome)
                @php
                    $giornoData = $disponibilita[$codice] ?? null;
                    $disponibile = $giornoData['disponibile'] ?? false;
                    $dalle = $giornoData['dalle'] ?? '09:00';
                    $alle = $giornoData['alle'] ?? '18:00';
                @endphp
                <div class="border border-gray-200 rounded-lg p-4">
                    <div class="flex items-center justify-between mb-3">
                        <h5 class="font-semibold text-gray-900">{{ $nome }}</h5>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="disponibile_{{ $codice }}" id="disp_{{ $codice }}"
                                {{ $disponibile ? 'checked' : '' }}
                                class="sr-only peer" onchange="toggleGiorno('{{ $codice }}')">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-fucsia-magia/30 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-fucsia-magia"></div>
                        </label>
                    </div>

                    <div id="orari_{{ $codice }}" class="{{ $disponibile ? '' : 'hidden' }} space-y-2">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Dalle</label>
                            <input type="time" name="dalle_{{ $codice }}" value="{{ $dalle }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:ring-fucsia-magia focus:border-fucsia-magia">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Alle</label>
                            <input type="time" name="alle_{{ $codice }}" value="{{ $alle }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:ring-fucsia-magia focus:border-fucsia-magia">
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Bottoni Azioni -->
            <div class="mt-6 flex justify-end gap-3">
                <a href="{{ route('professionista.dashboard') }}" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                    Annulla
                </a>
                <button type="submit" class="px-4 py-2 bg-fucsia-magia text-white rounded-lg hover:bg-viola-magia">
                    <i class="fas fa-save mr-2"></i> Salva Disponibilità
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Toggle visibilità orari giorno
function toggleGiorno(codice) {
    const checkbox = document.getElementById('disp_' + codice);
    const orari = document.getElementById('orari_' + codice);

    if (checkbox.checked) {
        orari.classList.remove('hidden');
    } else {
        orari.classList.add('hidden');
    }
}

// Applica preset rapidi
function applicaPreset(tipo, dalle, alle) {
    const giorni = tipo === 'lun-ven'
        ? ['lunedi', 'martedi', 'mercoledi', 'giovedi', 'venerdi']
        : ['sabato', 'domenica'];

    giorni.forEach(giorno => {
        const checkbox = document.getElementById('disp_' + giorno);
        const orariDiv = document.getElementById('orari_' + giorno);
        const inputDalle = document.querySelector(`input[name="dalle_${giorno}"]`);
        const inputAlle = document.querySelector(`input[name="alle_${giorno}"]`);

        checkbox.checked = true;
        orariDiv.classList.remove('hidden');
        inputDalle.value = dalle;
        inputAlle.value = alle;
    });
}

// Azzera tutto
function azzeraTutto() {
    const giorni = ['lunedi', 'martedi', 'mercoledi', 'giovedi', 'venerdi', 'sabato', 'domenica'];

    giorni.forEach(giorno => {
        const checkbox = document.getElementById('disp_' + giorno);
        const orariDiv = document.getElementById('orari_' + giorno);

        checkbox.checked = false;
        orariDiv.classList.add('hidden');
    });
}
</script>

@endsection
