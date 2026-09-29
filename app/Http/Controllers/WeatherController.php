<?php

namespace App\Http\Controllers;

use App\Services\WeatherService;
use Illuminate\Http\Request;
use Exception;

class WeatherController extends Controller
{
    public function __construct(
        protected WeatherService $weatherService
    ) {}

    public function index() {
        return view('weather');
    }

    public function getWeather(Request $request) {
        $request-> validate([
            'city' => 'required|string|max:100',
        ]);

        try {
            $weatherData = $this->weatherService->getWeather($request->input('city'));
            return view('weather', ['weather' => $weatherData]);
        } catch (Exception $e) {
            return back()->withErrors(['city' => $e->getMessage()]);
        }
    }
}
