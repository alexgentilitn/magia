<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Recupera Password - MA.GIA DONNA</title>

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
                    Recupera Password
                </h2>
                <p class="text-gray-600 mt-2">
                    Inserisci la tua email per ricevere un link di reset
                </p>
            </div>

            <!-- Messaggi -->
            @if (session('success'))
                <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-lg">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-green-500 text-xl mr-3"></i>
                        <p class="text-green-700">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            <!-- Card Form -->
            <div class="bg-white rounded-2xl shadow-xl p-8">

                <!-- Form Richiesta Reset -->
                <form method="POST" action="{{ route('admin.password.email') }}" class="space-y-6">
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

                    <!-- Info Box -->
                    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded">
                        <div class="flex">
                            <i class="fas fa-info-circle text-blue-500 mt-0.5 mr-3"></i>
                            <div class="text-sm text-blue-700">
                                <p class="font-medium mb-1">Come funziona:</p>
                                <ul class="list-disc list-inside space-y-1 text-xs">
                                    <li>Inserisci la tua email aziendale</li>
                                    <li>Riceverai un'email con un link sicuro</li>
                                    <li>Il link sarà valido per 24 ore</li>
                                    <li>Clic sul link per impostare una nuova password</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Bottone Invia -->
                    <div>
                        <button
                            type="submit"
                            class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-semibold rounded-lg text-white bg-gradient-to-r from-viola-magia to-fucsia-magia hover:from-fucsia-magia hover:to-viola-magia focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-viola-magia transition-all duration-300 shadow-lg hover:shadow-xl"
                        >
                            <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                                <i class="fas fa-paper-plane text-pink-200 group-hover:text-white transition"></i>
                            </span>
                            Invia Link di Reset
                        </button>
                    </div>

                </form>

                <!-- Link Torna al Login -->
                <div class="mt-6 pt-6 border-t border-gray-200 text-center">
                    <a href="{{ route('admin.login') }}" class="text-sm font-medium text-viola-magia hover:text-fucsia-magia transition flex items-center justify-center">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Torna al Login
                    </a>
                </div>

            </div>

        </div>
    </div>

</body>
</html>
