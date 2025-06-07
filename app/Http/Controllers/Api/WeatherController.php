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

    /**
     * @OA\Post(
     *     path="/get-weather-by-city",
     *     tags={"Weather"},
     *     summary="Obtener clima por ciudad", 
     *     description="Obtiene el clina de una ciudad determinada",
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody( 
     *         required=true,
     *         @OA\JsonContent(
     *             required={"city"},
     *             @OA\Property(property="city", type="string", example="Madrid"),
     *         )
     *     ),
     *     @OA\Parameter(
     *      name="lang",
     *      in="query",
     *      required=false,
     *      @OA\Schema(type="string", example="es", description="Código de idioma (opcional)")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Respuesta exitosa",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/WeatherCityResource")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Credenciales inválidas",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthorized")
     *         )
     *     ),
     * )
     */
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
