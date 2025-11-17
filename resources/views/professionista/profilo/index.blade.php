@extends('layouts.professionista')

@section('titolo', 'Il Mio Profilo')
@section('sottotitolo', 'Visualizza e modifica le tue informazioni')

@section('content')
<div class="space-y-6">

    <!-- Informazioni Personali -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900">Informazioni Personali</h3>
            <a href="{{ route('professionista.profilo.cambia-password') }}"
               class="text-sm text-violet-600 hover:text-violet-900">
                <i class="fas fa-key mr-1"></i>
                Cambia Password
            </a>
        </div>
        <div class="p-6">
            <form action="{{ route('professionista.profilo.aggiorna') }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Nome -->
                    <div>
                        <label for="nome" class="block text-sm font-medium text-gray-700 mb-2">
                            Nome <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               id="nome"
                               name="nome"
                               value="{{ old('nome', $utente->nome) }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-violet-500 focus:border-transparent @error('nome') border-red-500 @enderror"
                               required>
                        @error('nome')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Cognome -->
                    <div>
                        <label for="cognome" class="block text-sm font-medium text-gray-700 mb-2">
                            Cognome <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               id="cognome"
                               name="cognome"
                               value="{{ old('cognome', $utente->cognome) }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-violet-500 focus:border-transparent @error('cognome') border-red-500 @enderror"
                               required>
                        @error('cognome')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            Email <span class="text-red-500">*</span>
                        </label>
                        <input type="email"
                               id="email"
                               name="email"
                               value="{{ old('email', $utente->email) }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-violet-500 focus:border-transparent @error('email') border-red-500 @enderror"
                               required>
                        @error('email')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Telefono -->
                    <div>
                        <label for="telefono" class="block text-sm font-medium text-gray-700 mb-2">
                            Telefono
                        </label>
                        <input type="text"
                               id="telefono"
                               name="telefono"
                               value="{{ old('telefono', $utente->telefono) }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-violet-500 focus:border-transparent @error('telefono') border-red-500 @enderror">
                        @error('telefono')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Bio -->
                    <div class="md:col-span-2">
                        <label for="bio" class="block text-sm font-medium text-gray-700 mb-2">
                            Biografia
                        </label>
                        <textarea id="bio"
                                  name="bio"
                                  rows="4"
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-violet-500 focus:border-transparent @error('bio') border-red-500 @enderror"
                                  placeholder="Racconta qualcosa di te...">{{ old('bio', $professionista->bio) }}</textarea>
                        @error('bio')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-6 flex items-center justify-end space-x-3">
                    <button type="submit"
                            class="px-6 py-2 bg-gradient-to-r from-violet-600 to-purple-700 text-white font-medium rounded-lg hover:from-violet-700 hover:to-purple-800 transition">
                        <i class="fas fa-save mr-2"></i>
                        Salva Modifiche
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Informazioni Professionali -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Informazioni Professionali</h3>
            <p class="text-sm text-gray-600 mt-1">
                Per modificare queste informazioni contatta l'amministratore
            </p>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @if($professionista->codice_professionista)
                <div>
                    <p class="text-sm font-medium text-gray-600">Codice Professionista</p>
                    <p class="text-lg text-gray-900 mt-1">{{ $professionista->codice_professionista }}</p>
                </div>
                @endif

                @if($professionista->titolo_professionale)
                <div>
                    <p class="text-sm font-medium text-gray-600">Titolo Professionale</p>
                    <p class="text-lg text-gray-900 mt-1">{{ $professionista->titolo_professionale }}</p>
                </div>
                @endif

                @if($professionista->anni_esperienza)
                <div>
                    <p class="text-sm font-medium text-gray-600">Anni di Esperienza</p>
                    <p class="text-lg text-gray-900 mt-1">{{ $professionista->anni_esperienza }}</p>
                </div>
                @endif

                @if($professionista->tariffa_oraria)
                <div>
                    <p class="text-sm font-medium text-gray-600">Tariffa Oraria</p>
                    <p class="text-lg text-gray-900 mt-1">€ {{ number_format($professionista->tariffa_oraria, 2, ',', '.') }}</p>
                </div>
                @endif

                @if($professionista->specializzazioni && count($professionista->specializzazioni) > 0)
                <div class="md:col-span-2">
                    <p class="text-sm font-medium text-gray-600 mb-2">Specializzazioni</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach($professionista->specializzazioni as $spec)
                        <span class="px-3 py-1 bg-purple-100 text-purple-800 rounded-full text-sm">
                            {{ $spec }}
                        </span>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
