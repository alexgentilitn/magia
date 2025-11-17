@extends('layouts.app')

@section('titolo', 'Chat')

@section('contenuto')
<div class="container mx-auto p-6">
    <!-- Header -->
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 mb-2">
                <i class="fas fa-comments text-fucsia-magia mr-3"></i>
                Messaggi
            </h1>
            <p class="text-gray-600">
                <span class="font-semibold">{{ $totaleConversazioni }}</span> conversazioni
                @if($messaggiNonLetti > 0)
                    · <span class="text-red-600 font-semibold">{{ $messaggiNonLetti }}</span> non letti
                @endif
            </p>
        </div>

        <a href="{{ route('messaging.create') }}" class="px-4 py-2 bg-fucsia-magia text-white rounded-lg hover:bg-opacity-90 transition">
            <i class="fas fa-plus mr-2"></i>
            Nuova Conversazione
        </a>
    </div>

    <!-- Lista Conversazioni -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        @if($conversazioni->isEmpty())
            <div class="text-center py-16">
                <i class="fas fa-inbox text-gray-300 text-6xl mb-4"></i>
                <p class="text-gray-500 text-lg mb-4">Nessuna conversazione</p>
                <a href="{{ route('messaging.create') }}" class="inline-block px-6 py-3 bg-fucsia-magia text-white rounded-lg hover:bg-opacity-90">
                    <i class="fas fa-plus mr-2"></i>
                    Inizia una conversazione
                </a>
            </div>
        @else
            <div class="divide-y divide-gray-200">
                @foreach($conversazioni as $conv)
                    @php
                        $altriUtenti = $conv->utenti->where('id', '!=', Auth::id());
                        $ultimoMessaggio = $conv->messaggi->last();
                        $pivot = $conv->utenti->where('id', Auth::id())->first()->pivot ?? null;
                        $nonLetti = $pivot ? $pivot->messaggi_non_letti : 0;
                    @endphp

                    <a href="{{ route('messaging.show', $conv->id) }}"
                       class="block hover:bg-gray-50 transition p-4 {{ $nonLetti > 0 ? 'bg-blue-50' : '' }}">
                        <div class="flex items-start gap-4">
                            <!-- Avatar -->
                            <div class="flex-shrink-0">
                                @if($conv->tipo === 'privata' && $altriUtenti->count() === 1)
                                    @php $altroUtente = $altriUtenti->first(); @endphp
                                    <div class="w-12 h-12 rounded-full bg-gradient-to-br from-fucsia-magia to-purple-600 flex items-center justify-center text-white font-bold text-lg">
                                        {{ strtoupper(substr($altroUtente->nome ?? 'U', 0, 1)) }}
                                    </div>
                                @else
                                    <div class="w-12 h-12 rounded-full bg-gradient-to-br from-blue-500 to-blue-700 flex items-center justify-center text-white">
                                        <i class="fas fa-users"></i>
                                    </div>
                                @endif
                            </div>

                            <!-- Contenuto -->
                            <div class="flex-1 min-w-0">
                                <!-- Nome -->
                                <div class="flex items-center justify-between mb-1">
                                    <h3 class="font-semibold text-gray-800 truncate {{ $nonLetti > 0 ? 'font-bold' : '' }}">
                                        @if($conv->tipo === 'privata' && $altriUtenti->count() === 1)
                                            {{ $altriUtenti->first()->nome_completo ?? 'Utente' }}
                                        @else
                                            {{ $conv->titolo ?? 'Conversazione di gruppo' }}
                                        @endif
                                    </h3>
                                    @if($ultimoMessaggio)
                                        <span class="text-xs text-gray-500 ml-2 flex-shrink-0">
                                            {{ $ultimoMessaggio->created_at->diffForHumans() }}
                                        </span>
                                    @endif
                                </div>

                                <!-- Ultimo messaggio -->
                                @if($ultimoMessaggio)
                                    <p class="text-sm text-gray-600 truncate {{ $nonLetti > 0 ? 'font-semibold' : '' }}">
                                        @if($ultimoMessaggio->mittente_id === Auth::id())
                                            <i class="fas fa-reply text-gray-400 mr-1"></i>
                                        @endif
                                        @if($ultimoMessaggio->tipo === 'immagine')
                                            <i class="fas fa-image mr-1"></i> Immagine
                                        @elseif($ultimoMessaggio->tipo === 'file')
                                            <i class="fas fa-file mr-1"></i> {{ $ultimoMessaggio->allegato_nome }}
                                        @else
                                            {{ Str::limit($ultimoMessaggio->corpo, 60) }}
                                        @endif
                                    </p>
                                @else
                                    <p class="text-sm text-gray-400 italic">Nessun messaggio</p>
                                @endif
                            </div>

                            <!-- Badge non letti -->
                            @if($nonLetti > 0)
                                <div class="flex-shrink-0">
                                    <span class="inline-flex items-center justify-center w-6 h-6 text-xs font-bold text-white bg-red-500 rounded-full">
                                        {{ $nonLetti > 9 ? '9+' : $nonLetti }}
                                    </span>
                                </div>
                            @endif
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
