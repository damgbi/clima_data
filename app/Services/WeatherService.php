<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use Exception;

class WeatherService
{
    public function getWeather(string $city): array
    {
        $certPath = 'C:/laragon/bin/php/php-8.3.30-Win32-vs16-x64/extras/ssl/cacert.pem';

        $client = Http::when(app()->environment('local') && file_exists($certPath), function ($http) use ($certPath) {
            return $http->withOptions(['verify' => $certPath]);
        });

        $geoResponse = $client->get('https://geocoding-api.open-meteo.com/v1/search', [
            'name' => $city,
            'count' => 1,
            'language' => 'pt',
            'format' => 'json',
        ]);

        if ($geoResponse->failed() || empty($geoResponse->json()['results'])) {
            throw new Exception('Cidade não encontrada. Por favor, tente novamente.');
        }

        $location = $geoResponse -> json()['results'][0];
        $lat = $location['latitude'];
        $long = $location['longitude'];
        $cityName = $location['name'] . (!empty($location['admin1']) ? ' - ' . $location['admin1'] : '');

        $timezone = $location['timezone'] ?? 'America/Sao_Paulo';

        $weatherResponse = $client->get('https://api.open-meteo.com/v1/forecast', [
            'latitude' => $lat,
            'longitude' => $long,
            'current_weather' => true,
            'daily' => 'temperature_2m_max,temperature_2m_min,precipitation_probability_max,weathercode',
            'timezone' => $timezone,
        ]);

        if ($weatherResponse->failed()) {
            \Illuminate\Support\Facades\Log::error('Erro Open-Meteo:', [
                'status' => $weatherResponse->status(),
                'body'   => $weatherResponse->body(),
            ]);

            throw new Exception('Não foi possivel obter os dados meteorológicos. Por favor, tente novamente.');
        }

        $data = $weatherResponse->json();
        $current = $data['current_weather'];
        $daily = $data['daily'];
        $forecast = [];

        foreach ($daily['time'] as $index => $date) {
            $forecast[] = [
                'date' => Carbon::parse($date)->locale('pt_BR')->translatedFormat('d/m (D)'),
                'max_temp' => round($daily['temperature_2m_max'][$index]),
                'min_temp' => round ($daily['temperature_2m_min'][$index]),
                'rain_prob' => $daily['precipitation_probability_max'][$index],
                'weathercode' => $daily['weathercode'][$index]
            ];
        }

        return [
            'city' => $cityName,
            'current' => [
                'temperature' => round($current['temperature']),
                'windspeed' => $current['windspeed'],
                'winddirection' => $current['winddirection'],
                'time' => Carbon::parse($current['time'])->format('H:i'),
            ],
            'forecast' => $forecast
        ];
    }    
}