<?php

namespace App\Docs;

/**
 * @OA\Response(
 *     response="UnauthorizedError",
 *     description="No autorizado (token inválido o ausente)",
 *     @OA\JsonContent(
 *         @OA\Property(property="message", type="string", example="Acceso no autorizado. Por favor, proporcione un token de autenticación válido."),
 *         @OA\Property(property="status", type="string", example="error"),
 *         @OA\Property(property="code", type="integer", example=40101),
 *         @OA\Property(property="timestamp", type="string", format="date-time", example="2024-06-06T12:30:00.000000Z")
 *     )
 * )
 *
 * // Puedes añadir más Responses comunes aquí
 */
class Responses {}