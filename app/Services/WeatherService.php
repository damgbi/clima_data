<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Exception;

class WeatherService
{
    public function getWeather(string $city): array
    {
        $cacheKey = 'weather_' . str()->slug($city);

        return Cache::remember($cacheKey, now()->addMinutes(30), function () use ($city) {
            $apiKey = config('services.weatherapi.key');

            if(!$apiKey) {
                throw new Exception('Chave da API de clima não foi configurado.');
            }

            $certPath = 'C:/laragon/bin/php/php-8.3.30-Win32-vs16-x64/extras/ssl/cacert.pem';

            $client = Http::when(app()->environment('local') && file_exists($certPath), function ($http) use ($certPath) {
                return $http->withOptions(['verify' => $certPath]);
            });

            $response = $client->get('https://api.weatherapi.com/v1/forecast.json', [
                'key' => $apiKey,
                'q' => $city,
                'days' => 7,
                'lang' => 'pt',
                'aqi' => 'no',
                'alerts' => 'no',
            ]);

            if ($response->failed()) {
                Log::error('Erro WeatherAPI:', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                if($response->status() === 400 || $response->status() === 404) {
                    throw new Exception('Cidade não encontrada. Por favor, verifique o nome da cidade e tente novamente.');
                }

                throw new Exception('Não foi possivel obter os dados meteorológicos. Por favor, tente novamente.');
            }

            $data = $response -> json();
            $location = $data['location'];
            $current = $data['current'];
            $todayHours = $data['forecast']['forecastday'][0]['hour'];

            $forecastDays = $data['forecast']['forecastday'];

            $hourlyForecast = [];
            foreach ($todayHours as $hourData) {
                $time = Carbon::parse($hourData['time'])->format('H');
                if (in_array($time, ['09', '12', '15', '18'])) {
                    $hourlyForecast[] = [
                        'time' => $time . 'h',
                        'temp' => round($hourData['temp_c']),
                        'condition_code' => $hourData['condition']['code'],
                    ];
                }
            }

            $cityName = $location['name'] . (!empty($location['region']) ? ' - ' . $location['region'] : '');

            $forecast = [];

            foreach ($forecastDays as $day) {
                $forecast[] = [
                    'date' => Carbon::parse($day['date'])->locale('pt_BR')->translatedFormat('d/m (D)'),
                    'max_temp' => round($day['day']['maxtemp_c']),
                    'min_temp' => round($day['day']['mintemp_c']),
                    'rain_prob' => $day['day']['daily_chance_of_rain'] ?? 0,
                    'weathercode' => $day['day']['condition']['code'],
                ];
            }

            return [
                'city' => $cityName,
                'current' => [
                    'temperature' => round($current['temp_c']),
                    'humidity' => $current['humidity'],
                    'windspeed' => $current['wind_kph'],
                    'lat' => $location['lat'],
                    'lon' => $location['lon']
                ],
                'hourly' => $hourlyForecast,
                'forecast' => $forecast
            ];
        });    
    }    
}