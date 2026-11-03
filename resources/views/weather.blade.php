@extends('layouts.app')

@section('title', 'Previsão do Tempo - Portal de Dados')

@section('content')
    <div class="bg-white p-4 rounded-2xl shadow-sm border border-slate-200">
        <form action="{{ route('weather.search') }}" method="POST" class="space-y-4">
            @csrf
            <div class="flex gap-2">
                <input 
                type="text" 
                name="city" 
                placeholder="Digite o nome da cidade" 
                class="flex-1 px-4 py-2 border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 text-slate-700 text-sm"
                required
                >
                <button
                type="submit"
                class="shrink-0 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl text-sm transition duration-200 shadow-sm"
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
    </div>

    @if(isset($weather))
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
                <span class="text-slate-400 text-sm font-medium">Temperatura atual</span>
                <h2 class="text-3xl font-bold text-slate-800 mt-1">{{ $weather['current']['temperature'] }}°C</h2>
                <p class="text-sm text-blue-600 font-semibold mt-1">{{ $weather['city'] }}</p>
            </div>

            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
                <span class="text-slate-400 text-sm font-medium">Umidade</span>
                <h2 class="text-3xl font-bold text-slate-800 mt-1">{{ $weather['current']['humidity'] }}%</h2>
                <p class="text-sm text-slate-400 mt-1">Umidade relativa do ar</p>
            </div>

            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
                <span class="text-slate-400 text-sm font-medium">Vento</span>
                <h2 class="text-3xl font-bold text-slate-800 mt-1">{{ $weather['current']['windspeed'] }} km/h</h2>
                <p class="text-sm text-slate-400 mt-1">Velocidade atual</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 bg-white p-3 rounded-2xl shadow-sm border border-slate-200">
                <div id="map" class="h-80 w-full rounded-xl"></div>
            </div>
            
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 space-y-4">
                <h3 class="font-bold text-slate-800 border-b pb-3">Previsão para hoje</h3>
                <div class="space-y-3">
                    @foreach($weather['hourly'] as $hour)
                        <div class="flex justify-between items-center text-sm py-1 border-b border-slate-100 last:border-none">
                            <span class="text-slate-500 font-medium">{{ $hour['time'] }}</span>
                            <span class="font-bold text-slate-800">{{ $hour['temp'] }}°C</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 space-y-4">
            <h3 class="font-bold text-slate-800">Previsão para os próximos dias</h3>

            <div class="flex gap-4 overflow-x-auto pb-2 pt-1 snap-x scrollbar-thin">
                @foreach ($weather['forecast'] as $day)
                    <div class="min-w-[140px] flex-1 p-4 bg-slate-50 rounded-xl text-center border border-slate-200 flex flex-col justify-between shrink-0 snap-start shadow-sm">
                        <span class="text-xs font-bold text-slate-600 uppercase tracking-wider">
                            {{ $day['date'] }}
                        </span>
                        
                        <div class="my-2">
                            <span class="text-2xl font-black text-rose-500">{{ $day['max_temp'] }}°</span>
                            <span class="text-slate-300 mx-1">/</span>
                            <span class="text-lg font-bold text-blue-500">{{ $day['min_temp'] }}°</span>
                        </div>

                        <div class="text-xs text-blue-600 font-semibold">
                            ☔ {{ $day['rain_prob'] }}%
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <script>
            const lat = {{ $weather['current']['lat'] }};
            const lon = {{ $weather['current']['lon'] }};
            
            const map = L.map('map').setView([lat, lon], 12);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '© OpenStreetMap'
            }).addTo(map);
            
            L.marker([lat, lon]).addTo(map)
                .bindPopup('<b>{{ $weather["city"] }}</b><br>{{ $weather["current"]["temperature"] }}°C')
                .openPopup();
        </script>
    @endif
@endsection   

