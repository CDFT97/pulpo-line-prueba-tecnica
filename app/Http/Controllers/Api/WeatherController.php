<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\WeatherCityRequest;
use App\Http\Resources\WeatherCityResource;
use App\Services\WeatherapiService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use App\Traits\HandlesWeatherCache;

class WeatherController extends Controller
{
    use HandlesWeatherCache;

    public function __construct(WeatherapiService $weatherapiService)
    {
        $this->initializeCacheSettings($weatherapiService);
    }

    public function getWeatherByCity(WeatherCityRequest $request)
    {
        try {
            $weatherData = $this->handleWeatherCache(
                $request->city,
                $request->lang ?? null
            );

            return response()->json([
                'data' => WeatherCityResource::make($weatherData['weather']),
                'cached' => $weatherData['fromCache'],
                'expires_in' => $weatherData['expiresIn'],
                'cache_status' => $weatherData['cacheStatus'],
            ], Response::HTTP_OK);
            
        } catch (\Throwable $th) {
            Log::error($th);
            return response()->json([
                'message' => 'Error al obtener el clima',
                'timestamp' => now()->toDateTimeString(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
