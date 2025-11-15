@extends('layouts.cliente')

@section('title', 'Il Mio Profilo')

@section('content')
<div class="p-6">
    <!-- Header Pagina -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">
            <i class="fas fa-user-circle text-purple-600 mr-3"></i>Il Mio Profilo
        </h1>
        <p class="text-gray-600">Gestisci le tue informazioni personali</p>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-lg mb-6">
            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg mb-6">
            <i class="fas fa-exclamation-triangle mr-2"></i>
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Sidebar - Avatar e Info Rapide -->
        <div class="lg:col-span-1 space-y-6">

            <!-- Card Avatar -->
            <div class="bg-white rounded-xl shadow-lg p-6 text-center">
                <div class="mb-4">
                    @if($cliente->avatar)
                        <img src="{{ asset('storage/' . $cliente->avatar) }}"
                             alt="{{ $cliente->nome }}"
                             class="w-32 h-32 rounded-full mx-auto object-cover border-4 border-purple-200">
                    @else
                        <div class="w-32 h-32 rounded-full mx-auto bg-gradient-to-br from-purple-400 to-pink-400 flex items-center justify-center text-white text-4xl font-bold">
                            {{ substr($cliente->nome, 0, 1) }}{{ substr($cliente->cognome, 0, 1) }}
                        </div>
                    @endif
                </div>

                <h3 class="text-xl font-bold text-gray-800">{{ $cliente->nome }} {{ $cliente->cognome }}</h3>
                <p class="text-gray-600 text-sm mb-4">{{ $cliente->email }}</p>

                <span class="inline-block bg-green-100 text-green-800 px-4 py-1 rounded-full text-sm font-semibold">
                    <i class="fas fa-check-circle mr-1"></i>Account Attivo
                </span>
            </div>

            <!-- Info Rapide -->
            <div class="bg-gradient-to-br from-purple-100 to-pink-100 rounded-xl p-6">
                <h4 class="font-bold text-gray-800 mb-4">
                    <i class="fas fa-info-circle text-purple-600 mr-2"></i>Info Rapide
                </h4>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Membro dal:</span>
                        <span class="font-semibold">{{ $cliente->created_at->format('d/m/Y') }}</span>
                    </div>
                    @if($cliente->data_nascita)
                        <div class="flex justify-between">
                            <span class="text-gray-600">Età:</span>
                            <span class="font-semibold">{{ \Carbon\Carbon::parse($cliente->data_nascita)->age }} anni</span>
                        </div>
                    @endif
                    <div class="flex justify-between">
                        <span class="text-gray-600">Tipo account:</span>
                        <span class="font-semibold capitalize">{{ $cliente->tipo_utente }}</span>
                    </div>
                </div>
            </div>

        </div>

        <!-- Main Content - Form Modifica Profilo -->
        <div class="lg:col-span-2">

            <form action="{{ route('cliente.profilo.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- Dati Personali -->
                <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6 border-b pb-4">
                        <i class="fas fa-user text-purple-600 mr-2"></i>Dati Personali
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Nome *</label>
                            <input type="text"
                                   name="nome"
                                   value="{{ old('nome', $cliente->nome) }}"
                                   required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Cognome *</label>
                            <input type="text"
                                   name="cognome"
                                   value="{{ old('cognome', $cliente->cognome) }}"
                                   required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Data di Nascita</label>
                            <input type="date"
                                   name="data_nascita"
                                   value="{{ old('data_nascita', $cliente->data_nascita) }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Telefono</label>
                            <input type="tel"
                                   name="telefono"
                                   value="{{ old('telefono', $cliente->telefono) }}"
                                   placeholder="+39 123 456 7890"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        </div>
                    </div>
                </div>

                <!-- Indirizzo -->
                <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6 border-b pb-4">
                        <i class="fas fa-map-marker-alt text-purple-600 mr-2"></i>Indirizzo
                    </h2>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Via e Numero Civico</label>
                            <input type="text"
                                   name="indirizzo"
                                   value="{{ old('indirizzo', $cliente->indirizzo) }}"
                                   placeholder="Via Roma, 123"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">CAP</label>
                                <input type="text"
                                       name="cap"
                                       value="{{ old('cap', $cliente->cap) }}"
                                       maxlength="5"
                                       placeholder="00100"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Città</label>
                                <input type="text"
                                       name="citta"
                                       value="{{ old('citta', $cliente->citta) }}"
                                       placeholder="Roma"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Provincia</label>
                                <input type="text"
                                       name="provincia"
                                       value="{{ old('provincia', $cliente->provincia) }}"
                                       maxlength="2"
                                       placeholder="RM"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent uppercase">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Privacy e Preferenze -->
                <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6 border-b pb-4">
                        <i class="fas fa-cog text-purple-600 mr-2"></i>Preferenze
                    </h2>

                    <div class="space-y-4">
                        <label class="flex items-center">
                            <input type="checkbox"
                                   name="newsletter"
                                   value="1"
                                   {{ old('newsletter', $cliente->newsletter ?? false) ? 'checked' : '' }}
                                   class="w-5 h-5 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                            <span class="ml-3 text-gray-700">
                                Voglio ricevere newsletter e aggiornamenti via email
                            </span>
                        </label>

                        <label class="flex items-center">
                            <input type="checkbox"
                                   name="notifiche_whatsapp"
                                   value="1"
                                   {{ old('notifiche_whatsapp', $cliente->notifiche_whatsapp ?? false) ? 'checked' : '' }}
                                   class="w-5 h-5 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                            <span class="ml-3 text-gray-700">
                                Ricevi promemoria lezioni via WhatsApp
                            </span>
                        </label>
                    </div>
                </div>

                <!-- Pulsanti Azione -->
                <div class="flex gap-4">
                    <button type="submit"
                            class="flex-1 bg-gradient-to-r from-purple-600 to-pink-600 text-white px-8 py-4 rounded-xl font-bold hover:shadow-lg transition transform hover:scale-105">
                        <i class="fas fa-save mr-2"></i>Salva Modifiche
                    </button>

                    <a href="{{ route('cliente.dashboard') }}"
                       class="bg-gray-200 text-gray-700 px-8 py-4 rounded-xl font-bold hover:bg-gray-300 transition">
                        <i class="fas fa-times mr-2"></i>Annulla
                    </a>
                </div>

            </form>

            <!-- Cambia Password -->
            <div class="bg-white rounded-xl shadow-lg p-6 mt-6">
                <h2 class="text-2xl font-bold text-gray-800 mb-6 border-b pb-4">
                    <i class="fas fa-lock text-purple-600 mr-2"></i>Cambia Password
                </h2>

                <form action="{{ route('cliente.password.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="space-y-4 mb-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Password Attuale *</label>
                            <input type="password"
                                   name="current_password"
                                   required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Nuova Password *</label>
                            <input type="password"
                                   name="new_password"
                                   required
                                   minlength="8"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                            <p class="text-xs text-gray-500 mt-1">Minimo 8 caratteri</p>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Conferma Nuova Password *</label>
                            <input type="password"
                                   name="new_password_confirmation"
                                   required
                                   minlength="8"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        </div>
                    </div>

                    <button type="submit"
                            class="bg-purple-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-purple-700 transition">
                        <i class="fas fa-key mr-2"></i>Aggiorna Password
                    </button>
                </form>
            </div>

        </div>

    </div>
</div>
@endsection
