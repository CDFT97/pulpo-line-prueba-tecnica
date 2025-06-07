<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ExampleResource;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

/**
 * @OA\Info(
 *  title="Pulpoline Test API",
 *  description="Api creada para obtener los datos climaticos de ciudades",
 *  version="1.0"
 * )
 * 
 * @OA\SecurityScheme(
 *      securityScheme="bearerAuth",
 *      in="header",
 *      name="bearerAuth",
 *      type="http",
 *      scheme="bearer",
 *      bearerFormat="Sanctum"
 * )
 * 
 * @OA\Server(
 *  url="http://localhost:8000/api"
 * )
 */

class ExampleController extends Controller
{
}
