<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Exception;

class WeatherService
{
    public function getWeather(string $city): array
    {
        $geoResponse = Http::get('https://geocoding-api.open-meteo.com/v1/search', [
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
        $cityName = $location['name'] . ($location['admin1'] ? ' - ' . $location['admin1'] : '');

        $weatherResponse = Http::get('https://api.open-meteo.com/v1/forecast', [
            'latitude' => $lat,
            'longitude' => $long,
            'current_weather' => true,
            'timezone' => 'auto',
        ]);

        if ($weatherResponse->failed()) {
            throw new Exception('Não foi possivel obter os dados meteorológicos. Por favor, tente novamente.');
        }

        $currentWeather = $weatherResponse->json()['current_weather'];

        $weatherData = [
            'city' => $cityName,
            'temperature' => $currentWeather['temperature'],
            'windspeed' => $currentWeather['windspeed'],
            'winddirection' => $currentWeather['winddirection'],
            'weathercode' => $currentWeather['weathercode'],
        ];
    }    
}