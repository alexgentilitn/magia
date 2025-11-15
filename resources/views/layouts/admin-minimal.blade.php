<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin') - MA.GIA DONNA</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'viola-magia': '#7B2869',
                        'fucsia-magia': '#E91E8C',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-100">
    <div class="flex">
        <!-- Sidebar -->
        <div class="w-64 bg-gradient-to-br from-viola-magia to-fucsia-magia min-h-screen p-4">
            <div class="text-white mb-8">
                <h1 class="text-2xl font-bold">MA.GIA</h1>
                <p class="text-sm text-pink-200">DONNA</p>
            </div>
            <nav>
                <ul class="space-y-2">
                    <li><a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-2 text-white hover:bg-white hover:bg-opacity-20 rounded"><i class="fas fa-home mr-3"></i> Dashboard</a></li>
                    <li><a href="{{ route('admin.calendario.index') }}" class="flex items-center px-4 py-2 text-white hover:bg-white hover:bg-opacity-20 rounded"><i class="fas fa-calendar mr-3"></i> Calendario</a></li>
                    <li><a href="{{ route('admin.report.index') }}" class="flex items-center px-4 py-2 text-white bg-white bg-opacity-20 rounded"><i class="fas fa-chart-line mr-3"></i> Report</a></li>
                </ul>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="flex-1 p-6">
            @yield('content')
        </div>
    </div>
</body>
</html>
