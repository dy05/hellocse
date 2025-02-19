<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\Administrator;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Hash;
use OpenApi\Annotations as OA;

class AuthController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware('auth:sanctum', only: ['logout'])
        ];
    }

    /**
     * @OA\Post(
     *     path="/api/auth/register",
     *     summary="Register a new administrator",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "email", "password"},
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="john.doe@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="secret")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User created successfully!"),
     *             @OA\Property(property="user", ref="#/components/schemas/Administrator"),
     *             @OA\Property(property="token", type="string", example="token_value")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Error message")
     *         )
     *     )
     * )
     * @param RegisterRequest $request
     * @return JsonResponse
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        try {
            // create new administrator
            $admin = Administrator::query()
                ->create($request->only(['name', 'password', 'email']));
            return response()->json([
                'message' => 'User created successfully !',
                'user' => $admin,
                'token' => $admin->createToken('user' . $admin->id . '-' . $request->userAgent()),
            ], 201);
        } catch (Exception $exc) {
            return response()->json(['error' => $exc->getMessage()], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/auth/login",
     *     summary="Authenticate an administrator",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "password"},
     *             @OA\Property(property="email", type="string", format="email", example="john.doe@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="secret")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User login successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User login successfully!"),
     *             @OA\Property(property="user", ref="#/components/schemas/Administrator"),
     *             @OA\Property(property="token", type="string", example="token_value")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Invalid credentials",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Invalid credentials")
     *         )
     *     )
     * )
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $admin = Administrator::query()
            ->where(['email' => $request->input('email')])
            ->first();

        if (!$admin || !Hash::check($request->input('password'), $admin->password)) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        return response()->json([
            'message' => 'User login successfully !',
            'user' => $admin,
            'token' => $admin->createToken('user' . $admin->id . '-' . $request->userAgent()),
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/auth/logout",
     *     summary="Logout the authenticated administrator",
     *     tags={"Authentication"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="User logged out successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User logged out successfully!")
     *         )
     *     )
     * )
     */
    public function logout(Request $request): JsonResponse
    {
        // delete just actual sanctum token
        $request->user()->tokens()
            ->where(['id' => $request->user()->currentAccessToken()->id])
            ->delete();

        return response()->json([
            'message' => 'User logged out successfully !'
        ]);
    }
}
