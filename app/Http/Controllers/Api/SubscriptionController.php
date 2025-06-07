<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class SubscriptionController extends Controller
{

    /**
     * @OA\Get(
     *     path="/upgrade-to-premium",
     *     tags={"Subscription"},
     *     summary="Actualizar usuario a premium",
     *     description="Actualiza el rol del usuario autenticado a premium, otorgando acceso a funcionalidades exclusivas",
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Actualización exitosa a premium",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Ahora tienes acceso a todas las funcionalidades premium"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autenticado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Permiso denegado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Ya tienes una cuenta premium")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error del servidor",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Ocurrió un error al actualizar tu cuenta"),
     *             @OA\Property(property="error", type="string", example="Detalles del error")
     *         )
     *     )
     * )
     */
    public function upgradeToPremium(Request $request)
    {
        try {
            
            $user = auth()->user();
            
            if ($user->role->name !== 'free') {
                return response()->json([
                    'message' => 'You are already a premium user'
                ], Response::HTTP_FORBIDDEN);
            }

            $user->update([
                'role_id' => Role::ROLE_PREMIUM['id']
            ]);

            return response()->json([
                'message' => 'Now you are a premium user'
            ], Response::HTTP_OK);
        } catch (\Throwable $th) {
            Log::error($th);
            return response()->json([
                'message' => 'Error al actualizar el nivel de usuario',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
