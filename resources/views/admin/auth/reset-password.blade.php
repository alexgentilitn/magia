<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Imposta Nuova Password - MA.GIA DONNA</title>

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
                    Imposta Nuova Password
                </h2>
                <p class="text-gray-600 mt-2">
                    Scegli una password sicura per il tuo account
                </p>
            </div>

            <!-- Card Form -->
            <div class="bg-white rounded-2xl shadow-xl p-8">

                <!-- Form Reset Password -->
                <form method="POST" action="{{ route('admin.password.update') }}" class="space-y-6">
                    @csrf

                    <input type="hidden" name="token" value="{{ $token }}">

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
                            value="{{ old('email', request()->email) }}"
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

                    <!-- Nuova Password -->
                    <div x-data="{ mostraPassword: false }">
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-lock text-viola-magia mr-2"></i>
                            Nuova Password
                        </label>
                        <div class="relative">
                            <input
                                id="password"
                                name="password"
                                :type="mostraPassword ? 'text' : 'password'"
                                autocomplete="new-password"
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
                        @else
                            <p class="mt-2 text-xs text-gray-500">
                                Minimo 8 caratteri
                            </p>
                        @enderror
                    </div>

                    <!-- Conferma Password -->
                    <div x-data="{ mostraConferma: false }">
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-lock text-viola-magia mr-2"></i>
                            Conferma Password
                        </label>
                        <div class="relative">
                            <input
                                id="password_confirmation"
                                name="password_confirmation"
                                :type="mostraConferma ? 'text' : 'password'"
                                autocomplete="new-password"
                                required
                                class="appearance-none relative block w-full px-4 py-3 pr-12 border border-gray-300 placeholder-gray-400 text-gray-900 rounded-lg focus:outline-none focus:ring-2 focus:ring-viola-magia focus:border-transparent transition"
                                placeholder="••••••••"
                            >
                            <button
                                type="button"
                                @click="mostraConferma = !mostraConferma"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600"
                            >
                                <i :class="mostraConferma ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Info Sicurezza -->
                    <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 rounded">
                        <div class="flex">
                            <i class="fas fa-shield-alt text-yellow-500 mt-0.5 mr-3"></i>
                            <div class="text-sm text-yellow-700">
                                <p class="font-medium mb-1">Suggerimenti per una password sicura:</p>
                                <ul class="list-disc list-inside space-y-1 text-xs">
                                    <li>Almeno 8 caratteri</li>
                                    <li>Combina lettere maiuscole e minuscole</li>
                                    <li>Includi numeri e simboli</li>
                                    <li>Evita informazioni personali ovvie</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Bottone Conferma -->
                    <div>
                        <button
                            type="submit"
                            class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-semibold rounded-lg text-white bg-gradient-to-r from-viola-magia to-fucsia-magia hover:from-fucsia-magia hover:to-viola-magia focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-viola-magia transition-all duration-300 shadow-lg hover:shadow-xl"
                        >
                            <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                                <i class="fas fa-check-circle text-pink-200 group-hover:text-white transition"></i>
                            </span>
                            Conferma Nuova Password
                        </button>
                    </div>

                </form>

            </div>

        </div>
    </div>

</body>
</html>
