<?php

namespace App\Services;

use App\Traits\ConsumesExternalServices;
use Log;

class WeatherapiService
{
  use ConsumesExternalServices;

  protected $baseUri;

  protected $apiKey;

  public function __construct()
  {
    $this->baseUri = config('services.weatherapi.base_uri');
    $this->apiKey = config('services.weatherapi.key');
  }

  public function decodeResponse($response)
  {
    return json_decode($response);
  }

  public function getWeatherByCity(string $cityName, ?string $language = 'en')
  {
    return $this->makeRequest(
      'GET',
      '/v1/current.json',
      [
        'q' => $cityName,
        'lang' => $language
      ],
      [],
      [
        'Content-Type' => 'application/json',
        'key' => $this->apiKey
      ],
      $isJsonRequest = true
    );
  }
}
