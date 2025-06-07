<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\WeatherCityRequest;
use App\Http\Resources\WeatherCityResource;
use App\Services\WeatherapiService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class WeatherController extends Controller
{
    protected $weatherapiService;
    protected $cacheMinutes;
    protected $cacheKey;

    public function __construct(WeatherapiService $weatherapiService)
    {
        $this->weatherapiService = $weatherapiService;
        $this->cacheMinutes = config('cache.weather_cache.duration');
        $this->cacheKey = config('cache.weather_cache.prefix');
    }

    public function getWeatherByCity(WeatherCityRequest $request)
    {
        try {
            $cityName = $request->city;
            $lang = $request->lang ?? null;
            $cacheKey = $this->generateCacheKey($cityName, $lang);

            // Obtener datos con manejo de cache
            $weatherData = $this->handleCachedWeatherData($cacheKey, $cityName, $lang);

            return response()->json([
                'data' => WeatherCityResource::make($weatherData['weather']),
                'cached' => $weatherData['fromCache'],
                'expires_in' => $this->formatExpirationTime($weatherData['expiresInMinutes']),
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

    private function handleCachedWeatherData(string $cacheKey, string $cityName, ?string $lang): array
    {
        $isExpired = false;
        $cacheInfo = $this->getCacheExpirationInfo($cacheKey);

        // Verificar si el cache existe pero está expirado
        if ($cacheInfo['expiresAt'] && $cacheInfo['expiresInMinutes'] <= 0) {
            $isExpired = true;
            Cache::forget($cacheKey); // Limpiar entrada expirada
        }

        // Obtener datos, se regenerarán automáticamente si el cache está expirado
        $weather = $this->getCachedWeather($cacheKey, $cityName, $lang);

        // Obtener información actualizada del cache
        $updatedCacheInfo = $this->getCacheExpirationInfo($cacheKey);

        return [
            'weather' => $weather,
            'fromCache' => !$isExpired && Cache::has($cacheKey),
            'expiresInMinutes' => $updatedCacheInfo['expiresInMinutes'],
            'cacheStatus' => $isExpired ? 'regenerated' : ($updatedCacheInfo['expiresAt'] ? 'valid' : 'new'),
        ];
    }

    private function generateCacheKey(string $cityName, ?string $lang): string
    {
        return $this->cacheKey . Str::slug($cityName) . '_' . $lang;
    }

    private function getCachedWeather(string $cacheKey, string $cityName, ?string $lang)
    {
        return Cache::remember(
            $cacheKey,
            $this->cacheMinutes * 60,
            function () use ($cityName, $lang) {
                return $this->weatherapiService->getWeatherByCity($cityName, $lang);
            }
        );
    }

    private function getCacheExpirationInfo(string $cacheKey): array
    {
        $cacheEntry = DB::table('cache')
            ->where('key', 'like', '%' . $cacheKey . '%')
            ->first();

        if (!$cacheEntry) {
            return ['expiresAt' => null, 'expiresInMinutes' => 0];
        }

        $expiresAt = Carbon::createFromTimestamp($cacheEntry->expiration);
        $expiresInMinutes = Carbon::now()->diffInMinutes($expiresAt, false);

        return compact('expiresAt', 'expiresInMinutes');
    }

    private function formatExpirationTime(int $minutes): string
    {
        return $minutes > 0 ? "$minutes minutes" : "expired";
    }
}
