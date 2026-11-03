<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Portal de Dados')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
</head>
<body class="bg-slate-100 min-h-screen">

    <div class="flex min-h-screen">
        <aside class="w-64 bg-slate-900 text-white p-6 space-y-6 shrink-0 hidden md:block">
            <h1 class="text-xl font-bold tracking-wide">🌐 Portal de Dados</h1>
            <nav class="space-y-2">
                <a href="{{ route('weather.search') }}" class="flex items-center space-x-3 bg-blue-600 text-white p-3 rounded-xl font-medium shadow-sm transition">
                    <span>🌤️</span>
                    <span>Clima</span>
                </a>
            </nav>
        </aside>

        <main class="flex-1 p-6 md:p-8 space-y-6 overflow-x-hidden">
            
            <div class="bg-blue-600 text-white p-4 rounded-2xl shadow-md flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <span class="text-3xl">🌩️</span>
                    <h2 class="text-2xl font-bold tracking-tight">Clima</h2>
                </div>
                
                <div class="hidden lg:flex items-center space-x-6 text-sm font-medium text-blue-100">
                    <span class="text-white border-b-2 border-white pb-1 font-semibold cursor-pointer">Visão Geral</span>
                    <span class="hover:text-white cursor-pointer transition">Mapa</span>
                    <span class="hover:text-white cursor-pointer transition">Dados</span>
                    <span class="hover:text-white cursor-pointer transition">Gráficos</span>
                    <span class="hover:text-white cursor-pointer transition">Relatórios</span>
                </div>
            </div>

            @yield('content')

        </main>
    </div>

</body>
</html>