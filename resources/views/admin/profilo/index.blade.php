@extends('layouts.admin')

@section('titolo', 'Il Mio Profilo')

@section('contenuto')
<div class="p-6">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800">👤 Il Mio Profilo</h1>
        <p class="text-gray-600 mt-1">Gestisci le tue informazioni personali e le impostazioni dell'account</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Informazioni Profilo -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-bold">Informazioni Personali</h2>
                </div>

                <form method="POST" action="{{ route('admin.profilo.aggiorna') }}">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nome *</label>
                            <input type="text" name="nome" required value="{{ old('nome', $utente->nome) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Cognome *</label>
                            <input type="text" name="cognome" required value="{{ old('cognome', $utente->cognome) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                            <input type="email" name="email" required value="{{ old('email', $utente->email) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Telefono</label>
                            <input type="text" name="telefono" value="{{ old('telefono', $utente->telefono) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent">
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <button type="submit" class="px-4 py-2 bg-fucsia-magia text-white rounded-lg hover:bg-viola-magia">
                            <i class="fas fa-save mr-2"></i> Salva Modifiche
                        </button>
                    </div>
                </form>
            </div>

            <!-- Informazioni Account -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold mb-4">Informazioni Account</h2>

                <div class="space-y-3">
                    <div class="flex justify-between py-2 border-b">
                        <span class="text-gray-600">Tipo Utente</span>
                        <span class="font-semibold capitalize">{{ $utente->tipo_utente }}</span>
                    </div>

                    <div class="flex justify-between py-2 border-b">
                        <span class="text-gray-600">Stato Account</span>
                        <span class="px-2 py-1 rounded-full text-xs {{ $utente->attivo ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $utente->attivo ? 'Attivo' : 'Disattivato' }}
                        </span>
                    </div>

                    <div class="flex justify-between py-2 border-b">
                        <span class="text-gray-600">Registrato il</span>
                        <span class="font-semibold">{{ $utente->created_at->format('d/m/Y') }}</span>
                    </div>

                    @if($utente->ultimo_accesso)
                    <div class="flex justify-between py-2">
                        <span class="text-gray-600">Ultimo Accesso</span>
                        <span class="font-semibold">{{ $utente->ultimo_accesso->format('d/m/Y H:i') }}</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Azioni Rapide -->
        <div class="space-y-4">
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold mb-4">Sicurezza</h2>
                <div class="space-y-2">
                    <a href="{{ route('admin.profilo.cambia-password') }}" class="block w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-center">
                        <i class="fas fa-key mr-2"></i> Cambia Password
                    </a>
                </div>
            </div>

            @if($professionista)
            <div class="bg-gradient-to-br from-fucsia-magia to-viola-magia text-white rounded-lg shadow p-6">
                <h3 class="text-lg font-bold mb-2">Profilo Professionista</h3>
                <p class="text-sm opacity-90 mb-4">Codice: {{ $professionista->codice_professionista }}</p>
                <a href="{{ route('admin.professionisti.show', $professionista->id) }}" class="block w-full px-4 py-2 bg-white bg-opacity-20 hover:bg-opacity-30 rounded-lg text-center transition">
                    <i class="fas fa-user-tie mr-2"></i> Vai al Profilo Professionista
                </a>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
