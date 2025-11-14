@extends('layouts.pubblico')

@section('titolo', 'Accedi alla tua Area')

@section('contenuto')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-viola-magia via-fucsia-magia to-arancio-magia py-12 px-4">
    <div class="max-w-md w-full">
        
        <!-- Logo -->
        <div class="text-center mb-8">
            <div class="mx-auto flex justify-center mb-6">
                <img src="{{ asset('images/logo.png') }}" 
                     alt="Ma.Gia srl - Wellness for Women" 
                     class="h-28 w-auto rounded-2xl shadow-2xl">
            </div>
            <h2 class="text-3xl font-bold text-white">
                Bentornata!
            </h2>
            <p class="mt-2 text-lg text-pink-100">
                Accedi alla tua area personale
            </p>
        </div>

        <!-- Card Login -->
        <div class="bg-white rounded-2xl shadow-2xl p-8">
            
            <!-- Form Login -->
            <form method="POST" action="{{ route('cliente.login.post') }}" class="space-y-6">
                @csrf

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-envelope text-fucsia-magia mr-2"></i>
                        Email
                    </label>
                    <input 
                        id="email" 
                        name="email" 
                        type="email" 
                        autocomplete="email" 
                        required
                        autofocus
                        value="{{ old('email') }}"
                        class="appearance-none relative block w-full px-4 py-3 border @error('email') border-red-500 @else border-gray-300 @enderror placeholder-gray-400 text-gray-900 rounded-lg focus:outline-none focus:ring-2 focus:ring-fucsia-magia focus:border-transparent transition text-base"
                        placeholder="tua@email.com"
                    >
                    @error('email')
                        <p class="mt-2 text-sm text-red-600 flex items-center">
                            <i class="fas fa-exclamation-circle mr-1"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Password -->
                <div x-data="{ mostraPassword: false }">
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-lock text-fucsia-magia mr-2"></i>
                        Password
                    </label>
                    <div class="relative">
                        <input 
                            id="password" 
                            name="password" 
                            :type="mostraPassword ? 'text' : 'password'"
                            autocomplete="current-password" 
                            required
                            class="appearance-none relative block w-full px-4 py-3 pr-12 border @error('password') border-red-500 @else border-gray-300 @enderror placeholder-gray-400 text-gray-900 rounded-lg focus:outline-none focus:ring-2 focus:ring-fucsia-magia focus:border-transparent transition text-base"
                            placeholder="••••••••"
                        >
                        <button 
                            type="button"
                            @click="mostraPassword = !mostraPassword"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600"
                        >
                            <i :class="mostraPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                        </button>
                    </div>
                    @error('password')
                        <p class="mt-2 text-sm text-red-600 flex items-center">
                            <i class="fas fa-exclamation-circle mr-1"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Ricordami e Password Dimenticata -->
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                    <div class="flex items-center">
                        <input 
                            id="remember" 
                            name="remember" 
                            type="checkbox"
                            class="h-4 w-4 text-fucsia-magia focus:ring-fucsia-magia border-gray-300 rounded cursor-pointer"
                        >
                        <label for="remember" class="ml-2 block text-sm text-gray-700 cursor-pointer">
                            Ricordami
                        </label>
                    </div>

                    <div class="text-sm">
                        <a href="#" class="font-medium text-fucsia-magia hover:text-viola-magia transition">
                            Password dimenticata?
                        </a>
                    </div>
                </div>

                <!-- Bottone Login -->
                <div>
                    <button 
                        type="submit"
                        class="group relative w-full flex justify-center py-4 px-4 border border-transparent text-base font-bold rounded-xl text-white bg-gradient-to-r from-viola-magia to-fucsia-magia hover:from-fucsia-magia hover:to-viola-magia focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-fucsia-magia transition-all duration-300 shadow-lg hover:shadow-2xl transform hover:-translate-y-0.5"
                    >
                        <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                            <i class="fas fa-sign-in-alt text-xl text-pink-200 group-hover:text-white transition"></i>
                        </span>
                        Accedi
                    </button>
                </div>

                <!-- Divisore -->
                <div class="relative my-6">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-300"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-4 bg-white text-gray-500">Oppure</span>
                    </div>
                </div>

                <!-- Link Registrazione -->
                <div class="text-center">
                    <p class="text-sm text-gray-600 mb-3">
                        Non hai ancora un account?
                    </p>
                    <a href="{{ route('registrazione') }}" 
                       class="block w-full py-4 px-4 border-2 border-fucsia-magia text-fucsia-magia font-bold rounded-xl hover:bg-fucsia-magia hover:text-white transition-all duration-300 transform hover:-translate-y-0.5">
                        <i class="fas fa-user-plus mr-2"></i>
                        Registrati Gratuitamente
                    </a>
                </div>

            </form>

            <!-- Info Privacy -->
            <div class="mt-6 pt-6 border-t border-gray-200 text-center">
                <p class="text-xs text-gray-500 flex items-center justify-center">
                    <i class="fas fa-shield-alt mr-2 text-fucsia-magia"></i>
                    I tuoi dati sono protetti e sicuri
                </p>
            </div>

        </div>

        <!-- Info Aggiuntive -->
        <div class="mt-6 text-center">
            <div class="bg-white bg-opacity-90 rounded-lg p-4 shadow-lg">
                <p class="text-sm text-gray-700">
                    <i class="fas fa-info-circle text-fucsia-magia mr-2"></i>
                    <strong>Prima volta?</strong> Registrati per accedere al tuo programma personalizzato!
                </p>
            </div>
        </div>

        <!-- Link Area Admin -->
        <div class="mt-4 text-center">
            <a href="{{ route('admin.login') }}" class="text-sm text-white hover:text-pink-200 transition opacity-75 hover:opacity-100">
                <i class="fas fa-cog mr-1"></i> Accesso Admin
            </a>
        </div>

    </div>
</div>

@push('styles')
<style>
    /* Animazione gradiente background */
    @keyframes gradient {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }
    
    .bg-gradient-to-br {
        background-size: 200% 200%;
        animation: gradient 15s ease infinite;
    }
</style>
@endpush
@endsection
