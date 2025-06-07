<?php

namespace Tests\Feature;

use App\Models\City;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class WeatherControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected string $token;

    protected function setUp(): void
    {
        parent::setUp();

        // Crear usuario y token para autenticación
        $this->user = User::factory()->create();
        $this->token = $this->user->createToken('test-token')->plainTextToken;
    }

    #[Test]
    public function get_weather_by_city_requires_authentication()
    {
        $response = $this->postJson('/api/get-weather-by-city', [
            'city' => 'Madrid'
        ]);

        $response->assertStatus(401);
    }

    #[Test]
    public function get_weather_by_city_requires_city_parameter()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token
        ])->postJson('/api/get-weather-by-city');

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['city']);
    }

    #[Test]
    public function get_weather_by_city_returns_valid_structure()
    {
        // Mock de la respuesta de la API externa
        Http::fake([
            'api.weatherapi.com/v1/*' => Http::response([
                'location' => [
                    'name' => 'Madrid',
                    'region' => 'Madrid',
                    'country' => 'Spain',
                    'tz_id' => 'Europe/Madrid',
                    'localtime_epoch' => time(),
                ],
                'current' => [
                    'temp_c' => 22.5,
                    'temp_f' => 72.5,
                    'condition' => ['text' => 'Sunny'],
                    'wind_kph' => 10.5,
                    'wind_mph' => 6.5,
                    'humidity' => 50,
                ]
            ], 200)
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token
        ])->postJson('/api/get-weather-by-city', [
            'city' => 'Madrid',
            'lang' => 'es'
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'city' => [
                        'name',
                        'region',
                        'country',
                        'tz_id'
                    ],
                    'temp_c',
                    'temp_f',
                    'weather',
                    'wind_kph',
                    'wind_mph',
                    'humidity',
                    'local_time',
                    'parsed_localtime'
                ],
                'cached',
                'expires_in',
                'cache_status',
                'is_favorite',
                'city_id'
            ]);
    }

    #[Test]
    public function get_weather_by_city_creates_search_history()
    {
        Http::fake([
            'api.weatherapi.com/v1/*' => Http::response([/*...*/], 200)
        ]);

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token
        ])->postJson('/api/get-weather-by-city', [
            'city' => 'Barcelona'
        ]);

        $this->assertDatabaseHas('cities', [
            'name' => 'Barcelona'
        ]);

        $this->assertDatabaseHas('user_searches', [
            'user_id' => $this->user->id
        ]);
    }

    #[Test]
    public function get_weather_by_city_handles_api_failure()
    {
        Http::fake([
            'api.weatherapi.com/v1/*' => Http::response([
                'error' => 'City not found'
            ], 404)
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token
        ])->postJson('/api/get-weather-by-city', [
            'city' => 'InvalidCity'
        ]);

        $response->assertStatus(500)
            ->assertJson([
                'message' => 'Error al obtener el clima'
            ]);
    }


    #[Test]
    public function recent_searches_requires_authentication()
    {
        $response = $this->getJson('/api/recent-searches');

        $response->assertStatus(401);
    }

    #[Test]
    public function recent_searches_returns_empty_when_no_searches()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token
        ])->getJson('/api/recent-searches');

        $response->assertStatus(200)
            ->assertJson([
                'data' => []
            ]);
    }

    #[Test]
    public function recent_searches_returns_correct_structure()
    {
        $city = City::factory()->create();
        $this->user->searches()->create(['city_id' => $city->id]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token
        ])->getJson('/api/recent-searches');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'region',
                        'country',
                        'timezone',
                        'created_at',
                        'updated_at'
                    ]
                ]
            ]);
    }

    #[Test]
    public function favorites_requires_authentication()
    {
        $response = $this->getJson('/api/favorites');

        $response->assertStatus(401);
    }

    #[Test]
    public function favorites_returns_empty_when_no_favorites()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token
        ])->getJson('/api/favorites');

        $response->assertStatus(200)
            ->assertJson([
                'data' => []
            ]);
    }

    #[Test]
    public function favorites_returns_user_favorites()
    {
        // Crear 3 ciudades
        $cities = City::factory()->count(3)->create();

        // Marcar como favoritas para el usuario
        $cities->each(function ($city) {
            $this->user->favorites()->create(['city_id' => $city->id]);
        });

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token
        ])->getJson('/api/favorites');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'region',
                        'country',
                        'timezone',
                        'created_at',
                        'updated_at'
                    ]
                ]
            ]);
    }

    #[Test]
    public function favorites_only_returns_current_user_favorites()
    {
        // Crear otro usuario
        $otherUser = User::factory()->create();

        // Ciudad favorita del usuario actual
        $userCity = City::factory()->create();
        $this->user->favorites()->create(['city_id' => $userCity->id]);

        // Ciudad favorita de otro usuario
        $otherCity = City::factory()->create();
        $otherUser->favorites()->create(['city_id' => $otherCity->id]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token
        ])->getJson('/api/favorites');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $userCity->id);
    }

    # -------
    #[Test]
    public function toggle_favorite_requires_authentication()
    {
        $response = $this->postJson('/api/toggle-favorite', [
            'city_id' => 1
        ]);

        $response->assertStatus(401);
    }

    #[Test]
    public function toggle_favorite_requires_city_id_parameter()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token
        ])->postJson('/api/toggle-favorite');

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['city_id']);
    }

    #[Test]
    public function toggle_favorite_requires_valid_city_id()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token
        ])->postJson('/api/toggle-favorite', [
            'city_id' => 999 // ID inexistente
        ]);

        $response->assertStatus(422);
    }

    #[Test]
    public function can_add_city_to_favorites()
    {
        $city = City::factory()->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token
        ])->postJson('/api/toggle-favorite', [
            'city_id' => $city->id
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Ciudad agregada a favoritos',
                'is_favorite' => true
            ]);

        $this->assertDatabaseHas('user_favorites', [
            'user_id' => $this->user->id,
            'city_id' => $city->id
        ]);
    }

    #[Test]
    public function can_remove_city_from_favorites()
    {
        $city = City::factory()->create();
        $this->user->favorites()->create(['city_id' => $city->id]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token
        ])->postJson('/api/toggle-favorite', [
            'city_id' => $city->id
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Ciudad removida de favoritos',
                'is_favorite' => false
            ]);

        $this->assertDatabaseMissing('user_favorites', [
            'user_id' => $this->user->id,
            'city_id' => $city->id
        ]);
    }

}
