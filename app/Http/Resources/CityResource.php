<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CityResource extends JsonResource
{

    /**
     * @OA\Schema(
     *     schema="CityResource",
     *     type="object",
     *     @OA\Property(
     *         property="id",
     *         type="integer",
     *         description="ID único de la ciudad",
     *         example=1
     *     ),
     *     @OA\Property(
     *         property="name",
     *         type="string",
     *         description="Nombre de la ciudad",
     *         example="Madrid"
     *     ),
     *     @OA\Property(
     *         property="region",
     *         type="string",
     *         description="Región o estado de la ciudad",
     *         example="Madrid"
     *     ),
     *     @OA\Property(
     *         property="country",
     *         type="string",
     *         description="País donde se encuentra la ciudad",
     *         example="Spain"
     *     ),
     *     @OA\Property(
     *         property="timezone",
     *         type="string",
     *         description="Zona horaria de la ciudad",
     *         example="Europe/Madrid"
     *     ),
     *     @OA\Property(
     *         property="created_at",
     *         type="string",
     *         format="date-time",
     *         description="Fecha de creación del registro",
     *         example="2023-01-01T00:00:00.000000Z"
     *     ),
     *     @OA\Property(
     *         property="updated_at",
     *         type="string",
     *         format="date-time",
     *         description="Fecha de última actualización",
     *         example="2023-01-01T00:00:00.000000Z"
     *     )
     * )
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'region' => $this->region,
            'country' => $this->country,
            'timezone' => $this->timezone,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
