<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class SubscriptionController extends Controller
{
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
