<?php

namespace App\Traits;

use App\Services\WeatherapiService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

trait HandlesWeatherCache
{
  protected $weatherapiService;
  protected $cacheMinutes;
  protected $cacheKey;

  protected function initializeCacheSettings(WeatherapiService $weatherapiService): void
  {
    $this->weatherapiService = $weatherapiService;
    $this->cacheMinutes = config('cache.weather_cache.duration');
    $this->cacheKey = config('cache.weather_cache.prefix');
  }

  public function handleWeatherCache(string $cityName, ?string $lang): array
  {
    if (!isset($this->weatherapiService)) {
      throw new \RuntimeException('WeatherCache trait not properly initialized');
    }

    $cacheKey = $this->generateCacheKey($cityName, $lang);
    return $this->handleCachedWeatherData($cacheKey, $cityName, $lang);
  }

  protected function handleCachedWeatherData(string $cacheKey, string $cityName, ?string $lang): array
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
      'expiresIn' => $this->formatExpirationTime($updatedCacheInfo['expiresInMinutes']),
      'cacheStatus' => $isExpired ? 'regenerated' : ($updatedCacheInfo['expiresAt'] ? 'valid' : 'new'),
    ];
  }

  protected function generateCacheKey(string $cityName, ?string $lang): string
  {
    return $this->cacheKey . Str::slug($cityName) . '_' . $lang;
  }

  protected function getCachedWeather(string $cacheKey, string $cityName, ?string $lang)
  {
    return Cache::remember(
      $cacheKey,
      $this->cacheMinutes * 60,
      function () use ($cityName, $lang) {
        return $this->weatherapiService->getWeatherByCity($cityName, $lang);
      }
    );
  }

  protected function getCacheExpirationInfo(string $cacheKey): array
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

  protected function formatExpirationTime(int $minutes): string
  {
    return $minutes > 0 ? "$minutes minutes" : "expired";
  }
}
