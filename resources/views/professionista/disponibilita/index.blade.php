@extends('layouts.professionista')

@section('titolo', 'Disponibilità')
@section('sottotitolo', 'Gestisci le tue disponibilità settimanali')

@section('content')
<div class="space-y-6">

    <div class="bg-white rounded-lg shadow p-6">
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-gray-900">Informazioni Disponibilità</h3>
            <p class="text-sm text-gray-600 mt-2">
                Le tue disponibilità settimanali sono gestite dall'amministratore del sistema.
                Per modificare i tuoi orari di disponibilità, contatta l'amministrazione.
            </p>
        </div>

        @if($disponibilita && count($disponibilita) > 0)
        <div class="space-y-4">
            <h4 class="text-md font-semibold text-gray-800">Le tue disponibilità attuali:</h4>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach(['lunedi', 'martedi', 'mercoledi', 'giovedi', 'venerdi', 'sabato', 'domenica'] as $giorno)
                    @php
                        $giornoData = $disponibilita[$giorno] ?? null;
                        $disponibile = $giornoData['disponibile'] ?? false;
                    @endphp
                    <div class="border rounded-lg p-4 {{ $disponibile ? 'bg-green-50 border-green-200' : 'bg-gray-50 border-gray-200' }}">
                        <div class="flex items-center justify-between">
                            <div>
                                <h5 class="font-semibold text-gray-900 capitalize">{{ ucfirst($giorno) }}</h5>
                                @if($disponibile)
                                    <p class="text-sm text-gray-600 mt-1">
                                        <i class="fas fa-check-circle text-green-500 mr-1"></i>
                                        Disponibile
                                    </p>
                                    @if(isset($giornoData['orario_inizio']) && isset($giornoData['orario_fine']))
                                    <p class="text-xs text-gray-500 mt-1">
                                        {{ $giornoData['orario_inizio'] }} - {{ $giornoData['orario_fine'] }}
                                    </p>
                                    @endif
                                @else
                                    <p class="text-sm text-gray-400 mt-1">
                                        <i class="fas fa-times-circle mr-1"></i>
                                        Non disponibile
                                    </p>
                                @endif
                            </div>
                            <div class="text-2xl">
                                @if($disponibile)
                                    <i class="fas fa-calendar-check text-green-500"></i>
                                @else
                                    <i class="fas fa-calendar-times text-gray-300"></i>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @else
        <div class="text-center py-12">
            <i class="fas fa-calendar-alt text-6xl text-gray-300 mb-4"></i>
            <p class="text-gray-500">Nessuna disponibilità impostata</p>
            <p class="text-sm text-gray-400 mt-2">Contatta l'amministratore per configurare le tue disponibilità</p>
        </div>
        @endif

        <div class="mt-6 pt-6 border-t border-gray-200">
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-info-circle text-blue-500 text-xl"></i>
                    </div>
                    <div class="ml-3">
                        <h4 class="text-sm font-semibold text-blue-900">Nota</h4>
                        <p class="text-sm text-blue-700 mt-1">
                            Le disponibilità vengono utilizzate dal sistema per programmare le lezioni.
                            Per modifiche, invia una richiesta all'amministratore tramite email.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
