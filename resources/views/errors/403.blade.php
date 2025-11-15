<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Accesso Negato | MA.GIA DONNA</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-yellow-100 to-orange-100 min-h-screen flex items-center justify-center p-4">
    <div class="text-center max-w-2xl">
        <h1 class="text-9xl font-bold text-orange-600 mb-4">403</h1>
        <h2 class="text-3xl font-bold text-gray-800 mb-4">Accesso Negato</h2>
        <p class="text-xl text-gray-600 mb-8">Non hai i permessi per accedere a questa pagina.</p>
        <a href="{{ route('home') }}" class="inline-block bg-orange-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-orange-700 transition">
            Torna alla Home
        </a>
    </div>
</body>
</html>
