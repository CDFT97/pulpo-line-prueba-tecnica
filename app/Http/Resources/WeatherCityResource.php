<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class WeatherCityResource extends JsonResource
{
    /**
     * @OA\Schema(
     *     schema="WeatherCityResource",
     *     type="object",
     *     @OA\Property(
     *         property="city",
     *         type="object",
     *         @OA\Property(
     *             property="name",
     *             type="string",
     *             description="Nombre de la ciudad",
     *             example="Maturín"
     *         ),
     *         @OA\Property(
     *             property="region",
     *             type="string",
     *             description="Región o estado de la ciudad",
     *             example="Monagas"
     *         ),
     *         @OA\Property(
     *             property="country",
     *             type="string",
     *             description="País donde se encuentra la ciudad",
     *             example="Venezuela"
     *         ),
     *         @OA\Property(
     *             property="tz_id",
     *             type="string",
     *             description="Zona horaria de la ciudad",
     *             example="America/Caracas"
     *         )
     *     ),
     *     @OA\Property(
     *         property="temp_c",
     *         type="number",
     *         format="float",
     *         description="Temperatura en grados Celsius",
     *         example=28
     *     ),
     *     @OA\Property(
     *         property="temp_f",
     *         type="number",
     *         format="float",
     *         description="Temperatura en grados Fahrenheit",
     *         example=82.4
     *     ),
     *     @OA\Property(
     *         property="weather",
     *         type="string",
     *         description="Descripción del clima actual",
     *         example="Light rain shower"
     *     ),
     *     @OA\Property(
     *         property="wind_kph",
     *         type="number",
     *         format="float",
     *         description="Velocidad del viento en kilómetros por hora",
     *         example=15.1
     *     ),
     *     @OA\Property(
     *         property="wind_mph",
     *         type="number",
     *         format="float",
     *         description="Velocidad del viento en millas por hora",
     *         example=9.4
     *     ),
     *     @OA\Property(
     *         property="humidity",
     *         type="integer",
     *         description="Porcentaje de humedad",
     *         example=100
     *     ),
     *     @OA\Property(
     *         property="local_time",
     *         type="integer",
     *         description="Hora local en formato epoch/unix timestamp",
     *         example=1749319106
     *     ),
     *     @OA\Property(
     *         property="parsed_localtime",
     *         type="string",
     *         description="Hora local formateada (DD/MM/YYYY HH:MM:SS)",
     *         example="07/06/2025 13:58:26"
     *     )
     * )
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
                ->format('d/m/Y H:i:s'), // Parse the time to a 24h format
        ];
    }
}
