@extends('layouts.app')

@section('titolo', 'Nuova Conversazione')

@section('contenuto')
<div class="container mx-auto p-6">
    <div class="max-w-2xl mx-auto">

        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center gap-3 mb-2">
                <a href="{{ route('messaging.index') }}" class="text-gray-600 hover:text-gray-800">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <h1 class="text-3xl font-bold text-gray-800">
                    <i class="fas fa-comment-medical text-fucsia-magia mr-2"></i>
                    Nuova Conversazione
                </h1>
            </div>
            <p class="text-gray-600 ml-10">Invia un messaggio a un utente</p>
        </div>

        <!-- Form -->
        <div class="bg-white rounded-lg shadow p-6">
            <form action="{{ route('messaging.store') }}" method="POST">
                @csrf

                <!-- Destinatario -->
                <div class="mb-6">
                    <label for="destinatario_id" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-user mr-1"></i>
                        Destinatario *
                    </label>
                    <select name="destinatario_id"
                            id="destinatario_id"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent"
                            required>
                        <option value="">Seleziona un utente...</option>

                        @php
                            $utentiPerRuolo = $utenti->groupBy('tipo');
                        @endphp

                        @if($utentiPerRuolo->has('admin'))
                            <optgroup label="👑 Amministratori">
                                @foreach($utentiPerRuolo['admin'] as $u)
                                    <option value="{{ $u->id }}" {{ old('destinatario_id') == $u->id ? 'selected' : '' }}>
                                        {{ $u->nome_completo }}
                                    </option>
                                @endforeach
                            </optgroup>
                        @endif

                        @if($utentiPerRuolo->has('professionista'))
                            <optgroup label="👨‍⚕️ Professionisti">
                                @foreach($utentiPerRuolo['professionista'] as $u)
                                    <option value="{{ $u->id }}" {{ old('destinatario_id') == $u->id ? 'selected' : '' }}>
                                        {{ $u->nome_completo }}
                                    </option>
                                @endforeach
                            </optgroup>
                        @endif

                        @if($utentiPerRuolo->has('cliente'))
                            <optgroup label="👤 Clienti">
                                @foreach($utentiPerRuolo['cliente'] as $u)
                                    <option value="{{ $u->id }}" {{ old('destinatario_id') == $u->id ? 'selected' : '' }}>
                                        {{ $u->nome_completo }}
                                    </option>
                                @endforeach
                            </optgroup>
                        @endif
                    </select>
                    @error('destinatario_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Messaggio -->
                <div class="mb-6">
                    <label for="messaggio" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-comment-dots mr-1"></i>
                        Messaggio *
                    </label>
                    <textarea name="messaggio"
                              id="messaggio"
                              rows="6"
                              placeholder="Scrivi il tuo messaggio qui..."
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent resize-none"
                              required>{{ old('messaggio') }}</textarea>
                    @error('messaggio')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-gray-500 mt-1">Max 5000 caratteri</p>
                </div>

                <!-- Info Box -->
                <div class="mb-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex gap-3">
                        <div class="text-blue-500 flex-shrink-0">
                            <i class="fas fa-info-circle text-xl"></i>
                        </div>
                        <div class="text-sm text-blue-800">
                            <p class="font-semibold mb-1">Nota</p>
                            <ul class="list-disc list-inside space-y-1">
                                <li>Dopo l'invio verrai reindirizzato alla conversazione</li>
                                <li>Il destinatario riceverà una notifica</li>
                                <li>Potrai allegare file nella conversazione successivamente</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Bottoni -->
                <div class="flex gap-3 justify-end">
                    <a href="{{ route('messaging.index') }}"
                       class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                        <i class="fas fa-times mr-2"></i>
                        Annulla
                    </a>
                    <button type="submit"
                            class="px-6 py-2 bg-fucsia-magia text-white rounded-lg hover:bg-opacity-90 transition">
                        <i class="fas fa-paper-plane mr-2"></i>
                        Invia Messaggio
                    </button>
                </div>
            </form>
        </div>

        <!-- Lista Conversazioni Esistenti -->
        @if(Auth::user()->conversazioni()->count() > 0)
            <div class="mt-8 bg-gray-50 border border-gray-200 rounded-lg p-4">
                <h3 class="font-semibold text-gray-700 mb-3">
                    <i class="fas fa-history mr-2"></i>
                    Conversazioni Recenti
                </h3>
                <div class="space-y-2">
                    @foreach(Auth::user()->conversazioni()->with('utenti')->latest('ultimo_messaggio_at')->limit(5)->get() as $conv)
                        @php
                            $altriUtenti = $conv->utenti->where('id', '!=', Auth::id());
                        @endphp
                        <a href="{{ route('messaging.show', $conv->id) }}"
                           class="block px-3 py-2 bg-white rounded border border-gray-200 hover:border-fucsia-magia transition text-sm">
                            <span class="font-semibold text-gray-800">
                                @if($conv->tipo === 'privata' && $altriUtenti->count() === 1)
                                    {{ $altriUtenti->first()->nome_completo }}
                                @else
                                    {{ $conv->titolo ?? 'Gruppo' }}
                                @endif
                            </span>
                            <span class="text-gray-500 text-xs ml-2">
                                @if($conv->ultimo_messaggio_at)
                                    {{ $conv->ultimo_messaggio_at->diffForHumans() }}
                                @endif
                            </span>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
