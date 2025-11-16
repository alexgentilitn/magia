<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Privacy Policy - MA.GIA DONNA</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <div class="max-w-4xl mx-auto px-4 py-12">
        <header class="mb-8">
            <h1 class="text-4xl font-bold mb-2 text-purple-800">Privacy Policy</h1>
            <p class="text-gray-600">MA.GIA DONNA - Centro Wellness & Fitness</p>
            <p class="text-sm text-gray-500 mt-2">Ultimo aggiornamento: {{ now()->format('d/m/Y') }} - Versione 1.0</p>
        </header>

        <div class="bg-white rounded-lg shadow-md p-8 space-y-6">
            <section>
                <h2 class="text-2xl font-semibold mb-4 text-pink-600">1. Titolare del Trattamento</h2>
                <p class="text-gray-700">
                    Il titolare del trattamento dei dati personali è MA.GIA DONNA.<br>
                    <strong>Email:</strong> <a href="mailto:privacy@magiadonna.it" class="text-blue-600 hover:underline">privacy@magiadonna.it</a>
                </p>
            </section>

            <section>
                <h2 class="text-2xl font-semibold mb-4 text-pink-600">2. Dati Raccolti</h2>
                <p class="text-gray-700 mb-3">Raccogliamo:</p>
                <ul class="list-disc list-inside text-gray-700 space-y-2 ml-4">
                    <li>Dati anagrafici (nome, cognome, CF, data nascita)</li>
                    <li>Dati contatto (email, telefono, indirizzo)</li>
                    <li>Dati sensibili salute (parametri corporei, certificati medici)</li>
                    <li>Dati utilizzo sito e preferenze</li>
                </ul>
            </section>

            <section>
                <h2 class="text-2xl font-semibold mb-4 text-pink-600">3. I Tuoi Diritti (GDPR)</h2>
                <p class="text-gray-700 mb-3">Hai diritto a:</p>
                <ul class="list-disc list-inside text-gray-700 space-y-2 ml-4">
                    <li>Accesso ai tuoi dati</li>
                    <li>Rettifica e cancellazione</li>
                    <li>Portabilità dati</li>
                    <li>Revoca consensi in qualsiasi momento</li>
                </ul>
                <p class="text-gray-700 mt-4">
                    Esercita i tuoi diritti dalla tua <a href="{{ route('cliente.privacy') }}" class="text-blue-600 hover:underline font-semibold">Area Privacy</a>
                </p>
            </section>

            <section class="bg-gray-100 p-6 rounded-lg">
                <h2 class="text-xl font-semibold mb-3 text-purple-800">Contatti</h2>
                <p class="text-gray-700">
                    Per domande: <a href="mailto:privacy@magiadonna.it" class="text-blue-600 hover:underline">privacy@magiadonna.it</a><br>
                    Reclami al Garante: <a href="https://www.garanteprivacy.it" target="_blank" class="text-blue-600 hover:underline">www.garanteprivacy.it</a>
                </p>
            </section>
        </div>

        <div class="mt-8 text-center">
            <a href="{{ url()->previous() }}" class="inline-block px-6 py-3 bg-pink-600 rounded-lg text-white font-semibold hover:bg-pink-700 transition">
                ← Torna Indietro
            </a>
        </div>
    </div>
</body>
</html>
