<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grazie! - Balla & Snella | MA.GIA DONNA</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-purple-100 to-pink-100 min-h-screen flex items-center justify-center p-4">

    <div class="max-w-2xl w-full bg-white rounded-3xl shadow-2xl p-12 text-center">
        {{-- Success Icon --}}
        <div class="mb-8">
            <div class="inline-flex items-center justify-center w-24 h-24 bg-green-100 rounded-full">
                <i class="fas fa-check-circle text-6xl text-green-500"></i>
            </div>
        </div>

        {{-- Title --}}
        <h1 class="text-4xl md:text-5xl font-bold text-purple-800 mb-4">
            Grazie per la Tua Richiesta!
        </h1>

        {{-- Subtitle --}}
        <p class="text-xl text-gray-600 mb-8">
            La tua iscrizione alla <span class="font-semibold text-purple-600">Giornata di Prova Gratuita</span> è stata ricevuta con successo.
        </p>

        {{-- Info Box --}}
        <div class="bg-purple-50 rounded-xl p-6 mb-8 text-left">
            <h3 class="text-lg font-bold text-purple-800 mb-3 flex items-center">
                <i class="fas fa-info-circle mr-2"></i> Cosa Succede Ora?
            </h3>
            <ul class="space-y-3 text-gray-700">
                <li class="flex items-start">
                    <i class="fas fa-phone text-purple-600 mt-1 mr-3"></i>
                    <span><strong>Ti contatteremo a breve</strong> per confermare la data della tua giornata di prova</span>
                </li>
                <li class="flex items-start">
                    <i class="fas fa-calendar-check text-purple-600 mt-1 mr-3"></i>
                    <span>Fisseremo insieme l'<strong>orario migliore per te</strong></span>
                </li>
                <li class="flex items-start">
                    <i class="fas fa-heart text-purple-600 mt-1 mr-3"></i>
                    <span>Potrai provare <strong>gratuitamente e senza impegno</strong> la tua prima lezione</span>
                </li>
            </ul>
        </div>

        {{-- CTA --}}
        <div class="space-y-4">
            <a href="{{ route('home') }}" class="inline-block bg-gradient-to-r from-purple-600 to-pink-600 text-white px-8 py-4 rounded-full text-lg font-bold hover:from-purple-700 hover:to-pink-700 transition transform hover:scale-105 shadow-lg">
                <i class="fas fa-home mr-2"></i> Torna alla Home
            </a>
        </div>

        {{-- Contact Info --}}
        <div class="mt-12 pt-8 border-t border-gray-200">
            <p class="text-gray-600 mb-2">Hai domande urgenti?</p>
            <p class="text-purple-700 font-semibold">Contattaci direttamente!</p>
            {{-- Qui potresti aggiungere telefono o email se disponibili --}}
        </div>
    </div>

</body>
</html>
