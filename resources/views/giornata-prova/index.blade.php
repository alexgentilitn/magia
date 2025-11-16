<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giornata di Prova Gratuita - MA.GIA DONNA</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'viola-magia': '#8B5CF6',
                        'fucsia-magia': '#EC4899',
                    }
                }
            }
        }
    </script>
</head>
<body class="font-sans antialiased">

    <!-- Hero Section -->
    <section class="relative bg-gradient-to-br from-viola-magia via-purple-600 to-fucsia-magia min-h-screen flex items-center">

        <!-- Pattern Background -->
        <div class="absolute inset-0 opacity-10">
            <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'0.4\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
        </div>

        <div class="container mx-auto px-6 py-16 relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">

                <!-- Left Column: Content -->
                <div class="text-white">
                    <h1 class="text-5xl md:text-6xl font-bold mb-6 leading-tight">
                        Scopri il Tuo<br>
                        <span class="text-yellow-300">Benessere</span>
                    </h1>

                    <p class="text-xl md:text-2xl mb-8 text-purple-100">
                        Prenota la tua <strong class="text-white">giornata di prova gratuita</strong> e scopri come MA.GIA DONNA può trasformare il tuo corpo e la tua vita.
                    </p>

                    <!-- Benefits -->
                    <div class="space-y-4 mb-8">
                        <div class="flex items-start gap-4">
                            <div class="bg-white bg-opacity-20 rounded-full p-3 flex-shrink-0">
                                <i class="fas fa-dumbbell text-2xl"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-lg">Programmi Personalizzati</h3>
                                <p class="text-purple-100">Allenamenti su misura per te, studiati da professioniste esperte</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <div class="bg-white bg-opacity-20 rounded-full p-3 flex-shrink-0">
                                <i class="fas fa-utensils text-2xl"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-lg">Alimentazione Smart</h3>
                                <p class="text-purple-100">Ricette e piani alimentari personalizzati per raggiungere i tuoi obiettivi</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <div class="bg-white bg-opacity-20 rounded-full p-3 flex-shrink-0">
                                <i class="fas fa-spa text-2xl"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-lg">Pelle & Benessere</h3>
                                <p class="text-purple-100">Trattamenti estetici e benessere completo per corpo e mente</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <div class="bg-white bg-opacity-20 rounded-full p-3 flex-shrink-0">
                                <i class="fas fa-users text-2xl"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-lg">Community Femminile</h3>
                                <p class="text-purple-100">Entra a far parte di una community di donne motivate e positive</p>
                            </div>
                        </div>
                    </div>

                    <!-- Trust Indicators -->
                    <div class="flex flex-wrap gap-6 items-center pt-6 border-t border-white border-opacity-20">
                        <div class="text-center">
                            <p class="text-3xl font-bold text-yellow-300">500+</p>
                            <p class="text-sm text-purple-100">Clienti soddisfatte</p>
                        </div>
                        <div class="text-center">
                            <p class="text-3xl font-bold text-yellow-300">5</p>
                            <p class="text-sm text-purple-100">Sedi in Italia</p>
                        </div>
                        <div class="text-center">
                            <p class="text-3xl font-bold text-yellow-300">15+</p>
                            <p class="text-sm text-purple-100">Anni di esperienza</p>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Form -->
                <div class="bg-white rounded-2xl shadow-2xl p-8 lg:p-10">

                    <div class="text-center mb-8">
                        <h2 class="text-3xl font-bold text-gray-900 mb-2">Prenota Ora!</h2>
                        <p class="text-gray-600">Compila il form e ti contatteremo entro 24-48 ore</p>
                    </div>

                    <!-- Success Message -->
                    @if(session('success'))
                        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-lg">
                            <div class="flex items-center">
                                <i class="fas fa-check-circle text-xl mr-3"></i>
                                <div>
                                    <p class="font-bold">Richiesta Inviata!</p>
                                    <p class="text-sm">{{ session('success') }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Error Message -->
                    @if(session('error'))
                        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-lg">
                            <div class="flex items-center">
                                <i class="fas fa-exclamation-circle text-xl mr-3"></i>
                                <p>{{ session('error') }}</p>
                            </div>
                        </div>
                    @endif

                    <form action="{{ route('giornata-prova.richiedi') }}" method="POST" class="space-y-5">
                        @csrf

                        <!-- Nome -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Nome *</label>
                            <input type="text" name="nome" value="{{ old('nome') }}" required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-viola-magia focus:border-transparent @error('nome') border-red-500 @enderror"
                                   placeholder="Il tuo nome">
                            @error('nome')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                        </div>

                        <!-- Cognome -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Cognome *</label>
                            <input type="text" name="cognome" value="{{ old('cognome') }}" required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-viola-magia focus:border-transparent @error('cognome') border-red-500 @enderror"
                                   placeholder="Il tuo cognome">
                            @error('cognome')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                        </div>

                        <!-- Email -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Email *</label>
                            <input type="email" name="email" value="{{ old('email') }}" required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-viola-magia focus:border-transparent @error('email') border-red-500 @enderror"
                                   placeholder="tua@email.com">
                            @error('email')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                        </div>

                        <!-- Telefono -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Telefono *</label>
                            <input type="tel" name="telefono" value="{{ old('telefono') }}" required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-viola-magia focus:border-transparent @error('telefono') border-red-500 @enderror"
                                   placeholder="+39 XXX XXXXXXX">
                            @error('telefono')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                        </div>

                        <!-- Data Preferenza -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Data Preferenza (opzionale)</label>
                            <input type="date" name="data_preferenza" value="{{ old('data_preferenza') }}" min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-viola-magia focus:border-transparent">
                            @error('data_preferenza')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                        </div>

                        <!-- Messaggio -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Messaggio (opzionale)</label>
                            <textarea name="messaggio" rows="4"
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-viola-magia focus:border-transparent"
                                      placeholder="Raccontaci i tuoi obiettivi o facci una domanda...">{{ old('messaggio') }}</textarea>
                        </div>

                        <!-- Privacy -->
                        <div class="flex items-start">
                            <input type="checkbox" name="privacy" value="1" required
                                   class="w-5 h-5 text-viola-magia border-gray-300 rounded focus:ring-viola-magia mt-1 @error('privacy') border-red-500 @enderror">
                            <label class="ml-3 text-sm text-gray-700">
                                Accetto l'<a href="#" class="text-viola-magia hover:underline">informativa sulla privacy</a> e autorizzo il trattamento dei miei dati personali *
                            </label>
                        </div>
                        @error('privacy')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror

                        <!-- Submit Button -->
                        <button type="submit"
                                class="w-full py-4 bg-gradient-to-r from-viola-magia to-fucsia-magia text-white font-bold text-lg rounded-lg hover:shadow-xl transition transform hover:-translate-y-1">
                            <i class="fas fa-paper-plane mr-2"></i>
                            Richiedi la Giornata di Prova
                        </button>

                        <p class="text-xs text-gray-500 text-center mt-4">
                            * Campi obbligatori. Ti contatteremo entro 24-48 ore.
                        </p>
                    </form>
                </div>

            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12">
        <div class="container mx-auto px-6 text-center">
            <h3 class="text-2xl font-bold mb-4">MA.GIA DONNA</h3>
            <p class="text-gray-400 mb-6">Centro Benessere Femminile - Dove il benessere incontra la bellezza</p>

            <div class="flex justify-center gap-6 mb-6">
                <a href="#" class="text-gray-400 hover:text-white transition">
                    <i class="fab fa-facebook fa-2x"></i>
                </a>
                <a href="#" class="text-gray-400 hover:text-white transition">
                    <i class="fab fa-instagram fa-2x"></i>
                </a>
                <a href="#" class="text-gray-400 hover:text-white transition">
                    <i class="fab fa-youtube fa-2x"></i>
                </a>
            </div>

            <p class="text-sm text-gray-500">
                © {{ date('Y') }} MA.GIA DONNA. Tutti i diritti riservati.
            </p>
        </div>
    </footer>

</body>
</html>
