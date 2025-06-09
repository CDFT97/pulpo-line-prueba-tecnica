<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CityFavoriteRequest;
use App\Http\Requests\WeatherCityRequest;
use App\Http\Resources\WeatherCityResource;
use App\Repositories\CityRepository;
use App\Repositories\UserFavoriteRepository;
use App\Repositories\UserSearchRepository;
use App\Services\WeatherapiService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use App\Traits\HandlesWeatherCache;

class WeatherController extends Controller
{
    use HandlesWeatherCache;

    protected $cityRepository;
    protected $userSearchRepository;
    protected $userFavoriteRepository;

    public function __construct(
        WeatherapiService $weatherapiService,
        CityRepository $cityRepository,
        UserSearchRepository $userSearchRepository,
        UserFavoriteRepository $userFavoriteRepository
    ) {
        $this->initializeCacheSettings($weatherapiService);
        $this->cityRepository = $cityRepository;
        $this->userSearchRepository = $userSearchRepository;
        $this->userFavoriteRepository = $userFavoriteRepository;
    }

    /**
     * @OA\Post(
     *     path="/get-weather-by-city",
     *     tags={"Weather"},
     *     summary="Obtener clima por ciudad", 
     *     description="Obtiene el clima de una ciudad determinada y registra la búsqueda. Se puede especificar el idioma de respuesta con el parámetro opcional 'lang' (valores aceptados: en, es).",
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody( 
     *         required=true,
     *         @OA\JsonContent(
     *             required={"city"},
     *             @OA\Property(
     *                 property="city",
     *                 type="string",
     *                 example="Madrid",
     *                 description="Nombre de la ciudad a consultar"
     *             ),
     *             @OA\Property(
     *                 property="lang",
     *                 type="string",
     *                 example="es",
     *                 description="Código de idioma para la respuesta (opcional). Valores aceptados: en, es",
     *                 enum={"en", "es"}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Respuesta exitosa",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", ref="#/components/schemas/WeatherCityResource"),
     *             @OA\Property(
     *                 property="cached", 
     *                 type="boolean", 
     *                 example=true,
     *                 description="Indica si los datos provienen de caché"
     *             ),
     *             @OA\Property(
     *                 property="expires_in", 
     *                 type="string", 
     *                 example="24 minutes",
     *                 description="Tiempo restante para que expire el caché"
     *             ),
     *             @OA\Property(
     *                 property="cache_status", 
     *                 type="string", 
     *                 example="valid",
     *                 description="Estado del caché (valid, regenerated, new)"
     *             ),
     *             @OA\Property(
     *                 property="is_favorite", 
     *                 type="boolean", 
     *                 example=false,
     *                 description="Indica si la ciudad está marcada como favorita"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Solicitud incorrecta",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message", 
     *                 type="string", 
     *                 example="El parámetro city es requerido"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autorizado",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message", 
     *                 type="string", 
     *                 example="Credenciales inválidas"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error del servidor",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message", 
     *                 type="string", 
     *                 example="Error al obtener el clima"
     *             ),
     *             @OA\Property(
     *                 property="error", 
     *                 type="string", 
     *                 example="Detalles del error"
     *             )
     *         )
     *     )
     * )
     */
    public function getWeatherByCity(WeatherCityRequest $request)
    {
        try {
            $validatedData = $request->validated();

            $weatherData = $this->handleWeatherCache(
                $validatedData['city'],
                $validatedData['lang'] ?? null
            );

            // Registrar la ciudad y la búsqueda
            $city = $this->cityRepository->firstOrCreate(
                [
                    'name' => $weatherData['weather']->location->name,
                    'country' => $weatherData['weather']->location->country
                ],
                [
                    'region' => $weatherData['weather']->location->region,
                    'timezone' => $weatherData['weather']->location->tz_id,
                ]
            );

            // Registrar la búsqueda del usuario
            $this->userSearchRepository->logSearch(auth()->user()->id, $city->id);

            // Verificar si es favorita
            $isFavorite = $this->userFavoriteRepository->isFavorite(
                $request->user()->id,
                $city->id
            );

            return response()->json([
                'data' => WeatherCityResource::make($weatherData['weather']),
                'cached' => $weatherData['fromCache'],
                'expires_in' => $weatherData['expiresIn'],
                'cache_status' => $weatherData['cacheStatus'],
                'is_favorite' => $isFavorite,
                'city_id' => $city->id
            ], Response::HTTP_OK);
        } catch (\Throwable $th) {
            Log::error($th);

            if ($th instanceof \GuzzleHttp\Exception\ClientException) {
                $response = $th->getResponse();
                $statusCode = $response->getStatusCode();
                $errorBody = json_decode($response->getBody()->getContents(), true);
                
                if ($statusCode === 400 && isset($errorBody['error']['code']) && $errorBody['error']['code'] === 1006) {
                    return response()->json([
                        'message' => __('messages.errors.weather_api_errors.location_not_found'),
                        'timestamp' => now()->toDateTimeString(),
                    ], Response::HTTP_NOT_FOUND);
                }

                // Errores generales de la api
                return response()->json([
                    'message' => __('messages.errors.weather_api_error'),
                    'details' => $errorBody['error']['message'] ?? 'Unknown error',
                    'timestamp' => now()->toDateTimeString(),
                ], $statusCode);
            }

            return response()->json([
                'message' => __('messages.errors.internal_server_error'),
                'timestamp' => now()->toDateTimeString(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\Post(
     *     path="/toggle-favorite",
     *     tags={"Weather"},
     *     summary="Alternar ciudad favorita",
     *     description="Añade o elimina una ciudad de favoritos",
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"city_id"},
     *             @OA\Property(property="city_id", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Operación exitosa",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Ciudad agregada a favoritos"),
     *             @OA\Property(property="is_favorite", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Ciudad no encontrada"
     *     )
     * )
     */
    public function toggleFavorite(CityFavoriteRequest $request)
    {
        try {

            $result = $this->userFavoriteRepository->toggleFavorite(
                $request->user()->id,
                $request->city_id
            );

            $message = $result['action'] === 'added' ? __('messages.favorites.added') : __('messages.favorites.removed');

            return response()->json([
                'message' => $message,
                'is_favorite' => $result['action'] === 'added'
            ], Response::HTTP_OK);
        } catch (\Throwable $th) {
            Log::error($th);
            return response()->json([
                'message' => __('messages.errors.internal_server_error'),
                'is_favorite' => false
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\Get(
     *     path="/recent-searches",
     *     tags={"Weather"},
     *     summary="Obtener búsquedas recientes",
     *     description="Devuelve las últimas ciudades buscadas por el usuario",
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de búsquedas recientes",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/CityResource")
     *             )
     *         )
     *     )
     * )
     */
    public function getRecentSearches(Request $request)
    {
        try {
            $searches = $this->userSearchRepository
                ->getUserRecentSearches($request->user()->id)
                ->pluck('city');

            return response()->json([
                'data' => $searches
            ], Response::HTTP_OK);
        } catch (\Throwable $th) {
            Log::error($th);
            return response()->json([
                'message' => __('messages.errors.internal_server_error'),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\Get(
     *     path="/favorites",
     *     tags={"Weather"},
     *     summary="Obtener ciudades favoritas",
     *     description="Devuelve las ciudades marcadas como favoritas por el usuario",
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de ciudades favoritas",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/CityResource")
     *             )
     *         )
     *     )
     * )
     */
    public function getFavorites(Request $request)
    {
        try {
            $favorites = $this->userFavoriteRepository
                ->getUserFavorites($request->user()->id)
                ->pluck('city');

            return response()->json([
                'data' => $favorites
            ], Response::HTTP_OK);
        } catch (\Throwable $th) {
            Log::error($th);
            return response()->json([
                'message' => __('messages.errors.internal_server_error'),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
