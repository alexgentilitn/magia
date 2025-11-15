<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Pagina Non Trovata | MA.GIA DONNA</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-purple-100 to-pink-100 min-h-screen flex items-center justify-center p-4">
    <div class="text-center max-w-2xl">
        <h1 class="text-9xl font-bold text-purple-600 mb-4">404</h1>
        <h2 class="text-3xl font-bold text-gray-800 mb-4">Pagina Non Trovata</h2>
        <p class="text-xl text-gray-600 mb-8">Ops! La pagina che stai cercando non esiste.</p>
        <a href="{{ route('home') }}" class="inline-block bg-purple-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-purple-700 transition">
            Torna alla Home
        </a>
    </div>
</body>
</html>
