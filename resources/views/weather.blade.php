<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clima</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 min-h-screen flex flex-col justify-center items-center p-4">
    <div class="bg-white p-8 rounded-2xl shadow-x1 w-full max-w-md">
        <h1 class="text-2xl font-bold text-gray-800 text-center mb-6">🌤️ Previsão do Tempo</h1>
        <form action="{{ route('weather.search') }}" method="POST" class="space-y-4">
            @csrf
            <div class="flex gap-2">
                <input 
                type="text" 
                name="city" 
                placeholder="Digite o nome da cidade" 
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none  focus:ring-2 focus:ring-blue-500"
                required
                >
                <button
                type="submit"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-lg transition duration-200"
                >
                    Buscar
                </button>
            </div>
        </form>

        @if($errors->any())
            <div class="mt-4 p-3 bg-red-100 text-red-700 rounded-lg text-sm text-center">
                {{ $errors->first() }}
            </div>
        @endif

        @if(isset($weather))
            <h2 class="text-xl font-bold text-gray-800 text-center mb-4">{{ $weather['city'] }}</h2>

            <div class="p-6 bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-xl text-center mb-6 shadow-md">
                <span class="text-xs uppercase font-semibold tracking-wider opacity-80">Agora em {{ $weather['city'] }}</span>
                <div class="text-5xl font-black my-2">{{ $weather['current']['temperature'] }}°C</div>
                <div class="flex justify-center gap-6 text-sm opacity-90 mt-3">
                    <p>💨 Vento: <strong>{{ $weather['current']['windspeed'] }} km/h</strong></p>
                    <p>🧭 Direção: <strong>{{ $weather['current']['winddirection'] }}°</strong></p>
                    <p>🕒 Atualizado: <strong>{{ $weather['current']['time'] }}</strong></p>
                </div>
            </div>

            <h3 class="text-md font-semibold text-gray-700 mb-3">Previsão para os próximos 7 dias:</h3>

            <div class="flex gap-3 overflow-x-auto pb-4 pt-1 snap-x scrollbar-thin">
                @foreach ($weather['forecast'] as $day)
                    <div class="min-w-[130px] flex-1 p-4 bg-slate-50 rounded-xl text-center border border-slate-200 flex flex-col justify-between shrink-0 snap-start shadow-sm">
                        <span class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                            {{ $day['date'] }}
                        </span>
                        
                        <div class="my-1">
                            <span class="text-2xl font-black text-red-500">{{ $day['max_temp'] }}°</span>
                            <span class="text-gray-400 mx-1">/</span>
                            <span class="text-lg font-bold text-blue-500">{{ $day['min_temp'] }}°</span>
                        </div>

                        <div class="text-xs text-blue-600 font-medium mt-1">
                            ☔ {{ $day['rain_prob'] }}% de chuva
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</body>
</html>    

