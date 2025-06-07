<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * @OA\Schema(
     *   schema="UserResource",
     *   type="object",
     *   @OA\Property(
     *       property="id",
     *       type="integer",
     *       description="The unique identifier of the product",
     *       example=1
     *   ),
     *   @OA\Property(
     *       property="name",
     *       type="string",
     *       description="Name of User",
     *       example="Pedro"
     *   ),
     *   @OA\Property(
     *       property="email",
     *       type="string",
     *       description="Email of User",
     *       example="pedro@gmail.com"
     *   ),
     * )
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
        ];
    }
}
