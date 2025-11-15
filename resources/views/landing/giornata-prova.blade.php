<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giornata di Prova Gratuita - Balla & Snella | MA.GIA DONNA</title>

    {{-- SEO Meta Tags --}}
    <meta name="description" content="Prova GRATIS Balla & Snella! Benessere, energia e bellezza al femminile. Un viaggio di trasformazione fisica ed emotiva. Prenota la tua giornata di prova gratuita ora!">
    <meta name="keywords" content="Balla e Snella, fitness donna, wellness, benessere femminile, giornata prova gratuita, Trentino">

    {{-- Open Graph --}}
    <meta property="og:title" content="Giornata di Prova Gratuita - Balla & Snella">
    <meta property="og:description" content="Entra anche tu in questo progetto di rinascita. Prova GRATIS la tua prima lezione!">
    <meta property="og:type" content="website">

    {{-- Tailwind CSS CDN --}}
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- Font Awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap');

        body {
            font-family: 'Poppins', sans-serif;
        }

        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
        }

        .hero-pattern {
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.1'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }
    </style>
</head>
<body class="bg-gray-50">

    {{-- Hero Section --}}
    <section class="gradient-bg hero-pattern text-white py-20 relative overflow-hidden">
        <div class="container mx-auto px-4 relative z-10">
            <div class="text-center max-w-4xl mx-auto">
                <h1 class="text-5xl md:text-6xl font-bold mb-6 leading-tight">
                    Prova <span class="text-pink-200">GRATIS</span> la Tua Prima Lezione!
                </h1>
                <p class="text-xl md:text-2xl mb-8 text-purple-100">
                    Benessere, energia e bellezza al femminile.<br>
                    <span class="font-semibold">"Entra anche tu in questo progetto di rinascita"</span>
                </p>
                <div class="flex flex-wrap justify-center gap-4">
                    <a href="#form-iscrizione" class="bg-white text-purple-700 px-8 py-4 rounded-full text-lg font-bold hover:bg-pink-100 transition transform hover:scale-105 shadow-lg">
                        <i class="fas fa-calendar-check mr-2"></i> Prenota Ora
                    </a>
                    <a href="#come-funziona" class="bg-purple-800 text-white px-8 py-4 rounded-full text-lg font-semibold hover:bg-purple-900 transition border-2 border-white">
                        Scopri di Più <i class="fas fa-arrow-down ml-2"></i>
                    </a>
                </div>
            </div>
        </div>

        {{-- Wave Divider --}}
        <div class="absolute bottom-0 left-0 w-full">
            <svg viewBox="0 0 1440 120" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M0 120L60 105C120 90 240 60 360 45C480 30 600 30 720 37.5C840 45 960 60 1080 67.5C1200 75 1320 75 1380 75L1440 75V120H1380C1320 120 1200 120 1080 120C960 120 840 120 720 120C600 120 480 120 360 120C240 120 120 120 60 120H0Z" fill="#F9FAFB"/>
            </svg>
        </div>
    </section>

    {{-- Come Funziona --}}
    <section id="come-funziona" class="py-16 bg-white">
        <div class="container mx-auto px-4">
            <h2 class="text-4xl font-bold text-center text-purple-800 mb-12">Come Funziona la Giornata di Prova?</h2>

            <div class="grid md:grid-cols-3 gap-8 max-w-5xl mx-auto">
                {{-- Step 1 --}}
                <div class="text-center p-6">
                    <div class="bg-purple-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="text-3xl font-bold text-purple-700">1</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Compila il Form</h3>
                    <p class="text-gray-600">Inserisci i tuoi dati nel form qui sotto. Ci vogliono solo 2 minuti!</p>
                </div>

                {{-- Step 2 --}}
                <div class="text-center p-6">
                    <div class="bg-pink-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="text-3xl font-bold text-pink-700">2</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Ti Contattiamo</h3>
                    <p class="text-gray-600">Ti chiameremo per confermare la data della tua prova e rispondere a ogni domanda.</p>
                </div>

                {{-- Step 3 --}}
                <div class="text-center p-6">
                    <div class="bg-purple-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="text-3xl font-bold text-purple-700">3</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Vieni a Provare!</h3>
                    <p class="text-gray-600">Partecipa alla tua prima lezione GRATIS, senza impegno. Scopri cosa possiamo fare per te!</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Form Iscrizione --}}
    <section id="form-iscrizione" class="py-16 bg-gradient-to-br from-purple-50 to-pink-50">
        <div class="container mx-auto px-4">
            <div class="max-w-2xl mx-auto">
                <div class="bg-white rounded-2xl shadow-2xl p-8 md:p-12">
                    <h2 class="text-3xl font-bold text-purple-800 mb-2 text-center">Prenota la Tua Giornata di Prova</h2>
                    <p class="text-gray-600 text-center mb-8">100% Gratuito, Senza Impegno</p>

                    @if(session('error'))
                        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded">
                            <p class="font-bold">Attenzione</p>
                            <p>{{ session('error') }}</p>
                        </div>
                    @endif

                    <form action="{{ route('landing.registra-prova') }}" method="POST" class="space-y-6">
                        @csrf

                        <div class="grid md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-gray-700 font-semibold mb-2">Nome *</label>
                                <input type="text" name="nome" value="{{ old('nome') }}" required
                                       class="w-full px-4 py-3 rounded-lg border-2 border-gray-300 focus:border-purple-500 focus:outline-none transition"
                                       placeholder="Il tuo nome">
                                @error('nome')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-gray-700 font-semibold mb-2">Cognome *</label>
                                <input type="text" name="cognome" value="{{ old('cognome') }}" required
                                       class="w-full px-4 py-3 rounded-lg border-2 border-gray-300 focus:border-purple-500 focus:outline-none transition"
                                       placeholder="Il tuo cognome">
                                @error('cognome')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Email *</label>
                            <input type="email" name="email" value="{{ old('email') }}" required
                                   class="w-full px-4 py-3 rounded-lg border-2 border-gray-300 focus:border-purple-500 focus:outline-none transition"
                                   placeholder="tua@email.com">
                            @error('email')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Telefono *</label>
                            <input type="tel" name="telefono" value="{{ old('telefono') }}" required
                                   class="w-full px-4 py-3 rounded-lg border-2 border-gray-300 focus:border-purple-500 focus:outline-none transition"
                                   placeholder="+39 123 456 7890">
                            @error('telefono')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Note (opzionale)</label>
                            <textarea name="note" rows="3"
                                      class="w-full px-4 py-3 rounded-lg border-2 border-gray-300 focus:border-purple-500 focus:outline-none transition"
                                      placeholder="Hai domande o preferenze particolari? Scrivile qui...">{{ old('note') }}</textarea>
                        </div>

                        <div class="flex items-start">
                            <input type="checkbox" name="privacy_accepted" id="privacy" required
                                   class="mt-1 mr-3 w-5 h-5 text-purple-600">
                            <label for="privacy" class="text-sm text-gray-700">
                                Accetto la <a href="{{ route('privacy-policy') }}" target="_blank" class="text-purple-600 underline">Privacy Policy</a> e autorizzo il trattamento dei miei dati personali *
                            </label>
                        </div>
                        @error('privacy_accepted')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror

                        <button type="submit" class="w-full bg-gradient-to-r from-purple-600 to-pink-600 text-white py-4 rounded-lg text-lg font-bold hover:from-purple-700 hover:to-pink-700 transition transform hover:scale-105 shadow-lg">
                            <i class="fas fa-paper-plane mr-2"></i> Invia Richiesta Gratuita
                        </button>

                        <p class="text-center text-sm text-gray-500">
                            * Campi obbligatori. I tuoi dati sono al sicuro con noi.
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </section>

    {{-- Footer --}}
    <footer class="bg-gray-800 text-white py-8">
        <div class="container mx-auto px-4 text-center">
            <p class="text-gray-400">© {{ date('Y') }} MA.GIA DONNA - Balla & Snella Trentino</p>
            <p class="text-sm text-gray-500 mt-2">Benessere, energia e bellezza al femminile</p>
        </div>
    </footer>

</body>
</html>
