@extends('layouts.admin')

@section('titolo', 'Cambia Password')

@section('contenuto')
<div class="p-6">
    <div class="mb-6">
        <a href="{{ route('admin.profilo.index') }}" class="text-fucsia-magia hover:text-viola-magia mb-2 inline-block">
            <i class="fas fa-arrow-left mr-2"></i> Torna al profilo
        </a>
        <h1 class="text-3xl font-bold text-gray-800">🔐 Cambia Password</h1>
        <p class="text-gray-600 mt-1">Aggiorna la tua password per mantenere il tuo account sicuro</p>
    </div>

    <div class="max-w-2xl">
        <div class="bg-white rounded-lg shadow p-6">
            <form method="POST" action="{{ route('admin.profilo.salva-password') }}">
                @csrf

                <div class="space-y-4">
                    <!-- Password Attuale -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Password Attuale *</label>
                        <input
                            type="password"
                            name="password_attuale"
                            required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent @error('password_attuale') border-red-500 @enderror"
                        >
                        @error('password_attuale')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Nuova Password -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nuova Password *</label>
                        <input
                            type="password"
                            name="password_nuova"
                            required
                            minlength="8"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent @error('password_nuova') border-red-500 @enderror"
                        >
                        @error('password_nuova')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @else
                        <p class="text-gray-500 text-xs mt-1">Minimo 8 caratteri</p>
                        @enderror
                    </div>

                    <!-- Conferma Password -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Conferma Nuova Password *</label>
                        <input
                            type="password"
                            name="password_nuova_confirmation"
                            required
                            minlength="8"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent"
                        >
                    </div>
                </div>

                <!-- Requisiti Password -->
                <div class="mt-6 p-4 bg-blue-50 rounded-lg">
                    <p class="text-sm font-medium text-blue-900 mb-2">
                        <i class="fas fa-info-circle mr-2"></i>Requisiti Password:
                    </p>
                    <ul class="text-sm text-blue-800 space-y-1 ml-6">
                        <li>✓ Almeno 8 caratteri</li>
                        <li>✓ Usa lettere, numeri e simboli per maggiore sicurezza</li>
                        <li>✓ Non utilizzare password già usate in precedenza</li>
                    </ul>
                </div>

                <div class="mt-6 flex justify-end space-x-3">
                    <a href="{{ route('admin.profilo.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                        Annulla
                    </a>
                    <button type="submit" class="px-4 py-2 bg-fucsia-magia text-white rounded-lg hover:bg-viola-magia">
                        <i class="fas fa-key mr-2"></i> Cambia Password
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
