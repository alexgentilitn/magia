@extends('layouts.admin')

@section('titolo', 'Dettaglio Professionista')

@section('contenuto')
<div class="p-6">
    <div class="mb-6">
        <a href="{{ route('admin.professionisti.index') }}" class="text-fucsia-magia hover:text-viola-magia mb-2 inline-block">
            <i class="fas fa-arrow-left mr-2"></i> Torna alla lista
        </a>
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">{{ $professionista->nome }} {{ $professionista->cognome }}</h1>
                <p class="text-gray-600 mt-1">{{ $professionista->titolo_professionale ?? 'Professionista' }}</p>
            </div>
            <div class="flex gap-2">
                <span class="px-3 py-1 rounded-full {{ $professionista->badge_stato }}">
                    {{ ucfirst($professionista->stato) }}
                </span>
                @if($professionista->visibile_pubblico)
                <span class="px-3 py-1 rounded-full bg-blue-100 text-blue-800">
                    <i class="fas fa-eye"></i> Pubblico
                </span>
                @endif
            </div>
        </div>
    </div>

    <!-- Statistiche -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-xs text-gray-600">Lezioni Totali</p>
            <p class="text-2xl font-bold text-gray-800">{{ $statistiche['lezioni_totali'] }}</p>
            <p class="text-xs text-blue-600">{{ $statistiche['lezioni_future'] }} future</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-xs text-gray-600">Programmi</p>
            <p class="text-2xl font-bold text-gray-800">{{ $statistiche['programmi_totali'] }}</p>
            <p class="text-xs text-green-600">{{ $statistiche['programmi_attivi'] }} attivi</p>
        </div>
        @if($professionista->tariffa_oraria)
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-xs text-gray-600">Tariffa Oraria</p>
            <p class="text-2xl font-bold text-gray-800">€{{ number_format($professionista->tariffa_oraria, 2) }}</p>
        </div>
        @endif
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-xs text-gray-600">Certificazioni</p>
            <p class="text-2xl font-bold text-gray-800">
                @if($statistiche['certificazioni_valide'])
                    <i class="fas fa-check-circle text-green-600"></i>
                @else
                    <i class="fas fa-exclamation-circle text-yellow-600"></i>
                @endif
            </p>
            @if($statistiche['certificazioni_scadenza'] > 0)
            <p class="text-xs text-yellow-600">{{ $statistiche['certificazioni_scadenza'] }} in scadenza</p>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Info Principali -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Informazioni Personali -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold mb-4">Informazioni Personali</h2>

                <div class="grid grid-cols-2 gap-4">
                    @if($professionista->email)
                    <div>
                        <p class="text-sm text-gray-600">Email</p>
                        <p class="font-medium">{{ $professionista->email }}</p>
                    </div>
                    @endif

                    @if($professionista->telefono_mobile)
                    <div>
                        <p class="text-sm text-gray-600">Telefono</p>
                        <p class="font-medium">{{ $professionista->telefono_mobile }}</p>
                    </div>
                    @endif

                    @if($professionista->anni_esperienza)
                    <div>
                        <p class="text-sm text-gray-600">Esperienza</p>
                        <p class="font-medium">{{ $professionista->anni_esperienza }} anni</p>
                    </div>
                    @endif

                    <div>
                        <p class="text-sm text-gray-600">Codice Professionista</p>
                        <p class="font-medium">{{ $professionista->codice_professionista }}</p>
                    </div>
                </div>

                @if($professionista->bio)
                <div class="mt-4 pt-4 border-t">
                    <p class="text-sm text-gray-600 mb-1">Biografia</p>
                    <p class="text-gray-800">{{ $professionista->bio }}</p>
                </div>
                @endif
            </div>

            <!-- Prossime Lezioni -->
            @if($prossimeLezioni->count() > 0)
            <div class="bg-white rounded-lg shadow">
                <div class="p-4 border-b">
                    <h2 class="text-lg font-semibold">Prossime Lezioni</h2>
                </div>
                <div class="p-4">
                    <div class="space-y-3">
                        @foreach($prossimeLezioni as $lezione)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex-1">
                                <p class="font-medium text-gray-800">{{ $lezione->titolo }}</p>
                                <p class="text-sm text-gray-600">
                                    <i class="far fa-calendar mr-1"></i>
                                    {{ $lezione->data->format('d/m/Y') }} - {{ substr($lezione->ora_inizio, 0, 5) }}
                                    @if($lezione->sede)
                                    <span class="ml-2">
                                        <i class="fas fa-map-marker-alt mr-1"></i>{{ $lezione->sede->nome }}
                                    </span>
                                    @endif
                                </p>
                            </div>
                            <a href="{{ route('admin.lezioni.show', $lezione->id) }}" class="text-blue-600 hover:text-blue-800">
                                <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Azioni Rapide -->
        <div class="space-y-4">
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold mb-4">Azioni</h2>
                <div class="space-y-2">
                    <a href="{{ route('admin.professionisti.edit', $professionista->id) }}" class="block w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-center">
                        <i class="fas fa-edit mr-2"></i> Modifica
                    </a>

                    <a href="{{ route('admin.professionisti.certificazioni', $professionista->id) }}" class="block w-full px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 text-center">
                        <i class="fas fa-certificate mr-2"></i> Gestisci Certificazioni
                    </a>

                    <a href="{{ route('admin.professionisti.disponibilita', $professionista->id) }}" class="block w-full px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-center">
                        <i class="fas fa-calendar-check mr-2"></i> Gestisci Disponibilità
                    </a>

                    <form method="POST" action="{{ route('admin.professionisti.reset-password', $professionista->id) }}" class="block" id="reset-password-form">
                        @csrf
                        <button type="button" onclick="confermaAzione('reset-password-form', 'Resettare la password?', 'Verrà generata una nuova password temporanea per {{ $professionista->nome }} {{ $professionista->cognome }}.', 'Sì, reset password')" class="w-full px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700">
                            <i class="fas fa-key mr-2"></i> Reset Password
                        </button>
                    </form>

                    <form method="POST" action="{{ route('admin.professionisti.destroy', $professionista->id) }}" class="block" id="delete-professionista-form">
                        @csrf
                        @method('DELETE')
                        <button type="button" onclick="confermaEliminazione('delete-professionista-form', 'Eliminare il professionista?', 'Il professionista {{ $professionista->nome }} {{ $professionista->cognome }} sarà eliminato definitivamente. Le lezioni esistenti non saranno eliminate.')" class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                            <i class="fas fa-trash mr-2"></i> Elimina
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
