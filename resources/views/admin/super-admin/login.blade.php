@extends('layouts.admin')

@section('titolo', 'Super Admin - Autenticazione')

@section('contenuto')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-gray-50 to-gray-100 p-6">
    <div class="w-full max-w-md">
        <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
            <!-- Header -->
            <div class="bg-gradient-to-r from-red-600 to-red-700 p-8 text-center">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-white rounded-full mb-4">
                    <i class="fas fa-shield-alt text-4xl text-red-600"></i>
                </div>
                <h1 class="text-2xl font-bold text-white">Super Admin</h1>
                <p class="text-red-100 mt-2">Accesso riservato</p>
            </div>

            <!-- Body -->
            <div class="p-8">
                <!-- Alert -->
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6 rounded-r">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-yellow-700">
                                <strong>Attenzione:</strong> Stai per accedere a funzionalità avanzate di sistema.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Form -->
                <form method="POST" action="{{ route('admin.super-admin.index') }}" class="space-y-6">
                    @csrf

                    <div>
                        <label for="super_admin_password" class="block text-sm font-medium text-gray-700 mb-2">
                            Password Super Admin
                        </label>
                        <input
                            type="password"
                            id="super_admin_password"
                            name="super_admin_password"
                            required
                            autofocus
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent transition @error('super_admin_password') border-red-500 @enderror"
                            placeholder="Inserisci la password"
                        >
                        @error('super_admin_password')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-3">
                        <button
                            type="submit"
                            class="w-full bg-gradient-to-r from-red-600 to-red-700 text-white font-semibold py-3 px-4 rounded-lg hover:from-red-700 hover:to-red-800 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition transform hover:scale-105"
                        >
                            <i class="fas fa-key mr-2"></i> Accedi
                        </button>

                        <a
                            href="{{ route('admin.dashboard') }}"
                            class="block w-full text-center bg-gray-100 text-gray-700 font-semibold py-3 px-4 rounded-lg hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-400 transition"
                        >
                            <i class="fas fa-arrow-left mr-2"></i> Annulla
                        </a>
                    </div>
                </form>
            </div>

            <!-- Footer -->
            <div class="bg-gray-50 px-8 py-4 border-t border-gray-100">
                <p class="text-xs text-gray-500 text-center">
                    <i class="fas fa-info-circle mr-1"></i>
                    La sessione scade dopo 30 minuti di inattività
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
