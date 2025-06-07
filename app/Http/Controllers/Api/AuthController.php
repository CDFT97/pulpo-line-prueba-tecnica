<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserStoreRequest;
use App\Http\Resources\UserResource;
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
     *     tags={"User"},
     *     summary="Register",
     *     description="Register new customer/user",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "last_name","email", "phone", "password", "password_confirmation"},
     *             @OA\Property(property="name", type="string", example="John"),
     *             @OA\Property(property="last_name", type="string", example="Doe"),
     *             @OA\Property(property="phone", type="string", example="+521231312"),
     *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="12345678"),
     *             @OA\Property(property="password_confirmation", type="string", format="password", example="12345678")
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
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

            $user = $this->userRepository->create($request->validated());

            $this->userRepository->save($user);
            
            DB::commit();

            return response()->json([
                'user' => UserResource::make($user),
                'message' => "Register successfully"
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return response()->json("Register Failed", Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function login(Request $request)
    {
        $user = $this->userRepository->getByEmail($request->email);

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => "Invalid Credentials"], Response::HTTP_UNAUTHORIZED);
        }

        $token = $user->createToken('api')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => UserResource::make($user),
        ], Response::HTTP_OK);
    }

    public function logout(Request $request)
    {
        auth()->user()->tokens()->delete();
        return response()->json(['message' => 'Logged out successfully'], Response::HTTP_OK);
    }
}
