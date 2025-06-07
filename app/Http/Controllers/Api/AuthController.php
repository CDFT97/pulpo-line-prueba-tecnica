<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserStoreRequest;
use App\Http\Resources\UserResource;
use App\Models\Role;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{

    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @OA\post(
     *     path="/register",
     *     tags={"Auth"},
     *     summary="Register",
     *     description="Register new customer/user",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","email", "password", "password_confirmation"},
     *             @OA\Property(property="name", type="string", example="John"),
     *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="Abcd1234#"),
     *             @OA\Property(property="password_confirmation", type="string", format="password", example="Abcd1234#")
     *         )
     *     ),
     *     @OA\Response(
     *         response="201",
     *         description="Successful response",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/UserResource")
     *         )
     *     )
     * )
     */
    public function register(UserStoreRequest $request)
    {
        try {
            DB::beginTransaction();

            $userData = $request->validated();
            $userData['role_id'] = Role::ROLE_FREE['id'];
            
            $user = $this->userRepository->create($userData);

            $this->userRepository->save($user);

            DB::commit();

            return response()->json([
                'user' => UserResource::make($user),
                'message' => __('messages.auth.registered')
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return response()->json([
                'message' => __('messages.errors.internal_server_error')
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\Post(
     *     path="/login",
     *     tags={"Auth"},
     *     summary="Iniciar sesión", 
     *     description="Autentica al usuario y devuelve un token de acceso.",
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody( 
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "password"},
     *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *             @OA\Property(property="password", type="string", example="Abcd1234#"),
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Respuesta exitosa",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/UserResource")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Credenciales inválidas",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthorized")
     *         )
     *     ),
     *     @OA\Response( 
     *         response=422,
     *         description="Errores de validación",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(property="errors", type="object", example={"email": {"The email field is required."}})
     *         )
     *     )
     * )
     */
    public function login(Request $request)
    {
        try {
            $user = $this->userRepository->getByEmail($request->email);
    
            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json(['message' => __('messages.errors.invalid_credentials')], Response::HTTP_UNAUTHORIZED);
            }
    
            $token = $user->createToken('api')->plainTextToken;
    
            return response()->json([
                'token' => $token,
                'user' => UserResource::make($user),
            ], Response::HTTP_OK);

        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            return response()->json([
                'message' => __('messages.errors.internal_server_error')
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\Post(
     *      path="/logout", 
     *      operationId="logoutUser", 
     *      tags={"Auth"},
     *      summary="Cerrar la sesión del usuario",
     *      description="Elimina los tokens de autenticación del usuario actual, cerrando su sesión.",
     *      security={{"bearerAuth": {}}},
     *      @OA\Response(
     *          response=200,
     *          description="Sesión cerrada exitosamente",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Logged out successfully")
     *          )
     *      ),
     *      @OA\Response(
     *         response=401,
     *         ref="#/components/responses/UnauthorizedError"
     *      ),
     * )
     */
    public function logout(Request $request)
    {
        auth()->user()->tokens()->delete();
        return response()->json(['message' => __('messages.auth.logged_out')], Response::HTTP_OK);
    }
}
