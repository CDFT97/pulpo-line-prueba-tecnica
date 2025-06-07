<?php

namespace Tests\Unit\Services;

use App\Services\WeatherapiService;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class WeatherapiServiceTest extends TestCase
{
    protected $service;

    protected function setUp(): void
    {
        parent::setUp();

        // Configurar mock de Guzzle
        $this->mockHandler = new MockHandler();
        $client = new Client(['handler' => HandlerStack::create($this->mockHandler)]);

        // Inyectar cliente mockeado al servicio (necesitarías modificar el servicio)
        $this->service = new WeatherapiService($client);

        config([
            'services.weatherapi.base_uri' => 'https://api.weatherapi.com',
            'services.weatherapi.key' => 'test-api-key'
        ]);
    }

    #[Test]
    public function it_can_be_instantiated()
    {
        $this->assertInstanceOf(WeatherapiService::class, $this->service);
    }

    #[Test]
    public function it_gets_weather_by_city_successfully()
    {
        // Configurar respuesta mock
        $mockResponse = [
            'location' => [
                'name' => 'Madrid',
                'region' => 'Madrid',
                'country' => 'Spain'
            ],
            'current' => [
                'temp_c' => 22.5,
                'condition' => ['text' => 'Sunny']
            ]
        ];

        $this->mockHandler->append(new Response(200, [], json_encode($mockResponse)));

        $response = $this->service->getWeatherByCity('Madrid', 'es');

        $this->assertEquals('Madrid', $response->location->name);
    }
}
