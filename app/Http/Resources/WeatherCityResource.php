<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class WeatherCityResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'city' => [
                'name' => $this->location->name,
                'region' => $this->location->region,
                'country' => $this->location->country,
                'tz_id' => $this->location->tz_id,
            ],
            'temp_c' => $this->current->temp_c,
            'temp_f' => $this->current->temp_f,
            'weather' => $this->current->condition->text,
            'wind_kph' => $this->current->wind_kph,
            'wind_mph' => $this->current->wind_mph,
            'humidity' => $this->current->humidity,
            'local_time' => $this->location->localtime_epoch,
            'parsed_localtime' => Carbon::createFromTimestamp($this->location->localtime_epoch)
                ->setTimezone($this->location->tz_id) // Ajusta a la zona horaria de la ciudad
                ->format('d/m/Y H:i:s'),// Parse the time to a 24h format
        ];
    }
}
