@extends('layouts.professionista')

@section('titolo', 'Cambia Password')
@section('sottotitolo', 'Modifica la tua password di accesso')

@section('content')
<div class="max-w-2xl">
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Cambia Password</h3>
        </div>
        <div class="p-6">
            <form action="{{ route('professionista.profilo.salva-password') }}" method="POST">
                @csrf

                <!-- Password Attuale -->
                <div class="mb-6">
                    <label for="password_attuale" class="block text-sm font-medium text-gray-700 mb-2">
                        Password Attuale <span class="text-red-500">*</span>
                    </label>
                    <input type="password"
                           id="password_attuale"
                           name="password_attuale"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-violet-500 focus:border-transparent @error('password_attuale') border-red-500 @enderror"
                           required>
                    @error('password_attuale')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Nuova Password -->
                <div class="mb-6">
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        Nuova Password <span class="text-red-500">*</span>
                    </label>
                    <input type="password"
                           id="password"
                           name="password"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-violet-500 focus:border-transparent @error('password') border-red-500 @enderror"
                           required>
                    @error('password')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @else
                        <p class="text-gray-500 text-xs mt-1">Minimo 8 caratteri</p>
                    @enderror
                </div>

                <!-- Conferma Password -->
                <div class="mb-6">
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                        Conferma Nuova Password <span class="text-red-500">*</span>
                    </label>
                    <input type="password"
                           id="password_confirmation"
                           name="password_confirmation"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-violet-500 focus:border-transparent"
                           required>
                </div>

                <!-- Pulsanti -->
                <div class="flex items-center justify-end space-x-3">
                    <a href="{{ route('professionista.profilo.index') }}"
                       class="px-6 py-2 bg-gray-200 text-gray-700 font-medium rounded-lg hover:bg-gray-300 transition">
                        Annulla
                    </a>
                    <button type="submit"
                            class="px-6 py-2 bg-gradient-to-r from-violet-600 to-purple-700 text-white font-medium rounded-lg hover:from-violet-700 hover:to-purple-800 transition">
                        <i class="fas fa-save mr-2"></i>
                        Salva Password
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Info Sicurezza -->
    <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-shield-alt text-blue-500 text-xl"></i>
            </div>
            <div class="ml-3">
                <h4 class="text-sm font-semibold text-blue-900">Suggerimenti per la sicurezza</h4>
                <ul class="text-sm text-blue-700 mt-2 space-y-1 list-disc list-inside">
                    <li>Usa una password di almeno 8 caratteri</li>
                    <li>Combina lettere maiuscole, minuscole, numeri e simboli</li>
                    <li>Non usare la stessa password per più account</li>
                    <li>Cambia la password regolarmente</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
