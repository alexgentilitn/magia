<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Login Amministratore - MA.GIA DONNA</title>

    <!-- Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full">
            
            <!-- Logo e Titolo -->
            <div class="text-center mb-8">
                <img src="{{ asset('images/logo.png') }}" 
                     alt="Ma.Gia srl" 
                     class="h-20 w-auto mx-auto mb-4">
                <h2 class="text-3xl font-bold text-gray-800">
                    Pannello Amministratore
                </h2>
                <p class="text-gray-600 mt-2">
                    Accesso riservato al personale autorizzato
                </p>
            </div>

            <!-- Card Login -->
            <div class="bg-white rounded-2xl shadow-xl p-8">
                
                <!-- Form Login -->
                <form method="POST" action="{{ route('admin.login.post') }}" class="space-y-6">
                    @csrf

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-envelope text-viola-magia mr-2"></i>
                            Email Aziendale
                        </label>
                        <input 
                            id="email" 
                            name="email" 
                            type="email" 
                            autocomplete="email" 
                            required
                            autofocus
                            value="{{ old('email') }}"
                            class="appearance-none relative block w-full px-4 py-3 border @error('email') border-red-500 @else border-gray-300 @enderror placeholder-gray-400 text-gray-900 rounded-lg focus:outline-none focus:ring-2 focus:ring-viola-magia focus:border-transparent transition"
                            placeholder="admin@magiadonna.it"
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
                            <i class="fas fa-lock text-viola-magia mr-2"></i>
                            Password
                        </label>
                        <div class="relative">
                            <input 
                                id="password" 
                                name="password" 
                                :type="mostraPassword ? 'text' : 'password'"
                                autocomplete="current-password" 
                                required
                                class="appearance-none relative block w-full px-4 py-3 pr-12 border @error('password') border-red-500 @else border-gray-300 @enderror placeholder-gray-400 text-gray-900 rounded-lg focus:outline-none focus:ring-2 focus:ring-viola-magia focus:border-transparent transition"
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

                    <!-- Ricordami -->
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <input 
                                id="remember" 
                                name="remember" 
                                type="checkbox"
                                class="h-4 w-4 text-viola-magia focus:ring-viola-magia border-gray-300 rounded cursor-pointer"
                            >
                            <label for="remember" class="ml-2 block text-sm text-gray-700 cursor-pointer">
                                Ricordami su questo dispositivo
                            </label>
                        </div>
                    </div>

                    <!-- Bottone Login -->
                    <div>
                        <button 
                            type="submit"
                            class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-semibold rounded-lg text-white bg-gradient-to-r from-viola-magia to-fucsia-magia hover:from-fucsia-magia hover:to-viola-magia focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-viola-magia transition-all duration-300 shadow-lg hover:shadow-xl"
                        >
                            <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                                <i class="fas fa-sign-in-alt text-pink-200 group-hover:text-white transition"></i>
                            </span>
                            Accedi al Pannello
                        </button>
                    </div>

                </form>

                <!-- Info Sicurezza -->
                <div class="mt-6 pt-6 border-t border-gray-200 text-center">
                    <p class="text-xs text-gray-500 flex items-center justify-center">
                        <i class="fas fa-shield-alt mr-2 text-viola-magia"></i>
                        Accesso protetto con crittografia SSL
                    </p>
                </div>

            </div>

            <!-- Link Area Cliente -->
            <div class="mt-6 text-center">
                <p class="text-sm text-gray-600">
                    Sei una cliente? 
                    <a href="{{ route('cliente.login') }}" class="font-medium text-fucsia-magia hover:text-viola-magia transition">
                        Accedi all'area clienti <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </p>
            </div>

        </div>
    </div>

</body>
</html>
