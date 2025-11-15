<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin') - MA.GIA DONNA</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="flex">
        <!-- Sidebar -->
        <div class="w-64 bg-gradient-to-br from-purple-900 to-pink-600 min-h-screen p-4">
            <h1 class="text-white font-bold text-xl">MA.GIA DONNA</h1>
        </div>

        <!-- Main Content -->
        <div class="flex-1 p-6">
            <h1 style="background: yellow; color: red; padding: 20px;">LAYOUT MINIMAL FUNZIONA</h1>
            @yield('content')
        </div>
    </div>
</body>
</html>
