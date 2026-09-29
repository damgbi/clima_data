<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clima</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 min-h-screen fle items-center justify-center p-4">
    <div class="bg-white p-8 rounded-2xl shadow-x1 w-full max-w-md">
        <h1 class="text-2xl font-bold text-gray-800 text-center mb-6">🌤️ Previsão do Tempo</h1>
        <form action="{{ route('weather.search') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <input 
                type="text" 
                name="city" 
                placeholder="Digite o nome da cidade" 
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none  focus:ring-2 focus:ring-blue-500"
                required
                >
            </div>
            <button
            type="submit"
            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-lg transition duration-200"
            >
                Buscar
            </button>
        </form>

        @if($errors->any())
            <div class="mt-4 p-3 bg-red-100 text-red-700 rounded-lg text-sm text-center">
                {{ $errors->first() }}
            </div>
        @endif

        @if(isset($weather))
            <div class="mt-6 p-6 bg-blue-50 rounded-xl text-center border border-blue-100">
                <h2 class="text-xl font-semibold text-gray-800 mb-2">{{ $weather['city'] }}</h2>
                <div class="text-5xl font-extrabold text-blue-600 my-4">
                    {{ $weather['temperature'] }}°C
                </div>
                <div class="text-sm text-gray-600 space-y-1">
                    <p>💨 Vento: <strong>{{ $weather['windspeed'] }} km/h</strong></p>
                    <p>🕒 Atualizado às: {{ \Carbon\Carbon::parse($weather['time'])->format('H:i') }}</p>
                </div>
            </div>
        @endif
    </div>
</body>
</html>    

