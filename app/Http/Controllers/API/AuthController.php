<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\Administrator;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Request;

class AuthController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware('auth:sanctum', only: ['logout'])
        ];
    }

    /**
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
                'data' => $admin,
                'token' => $admin->createToken('user' . $admin->id . '-' . now()),
            ], 201);
        } catch (Exception $exc) {
            return response()->json(['error' => $exc->getMessage()], 500);
        }
    }

    /**
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
            'token' => $admin->createToken('user' . $admin->id . '-' . now()),
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        return response()->json([
            'json' => $request->user()->currentAccessToken()->id
        ]);
        // delete just actual sanctum token
        $request->user()->tokens()->delete($request->user()->currentAccessToken()->id);

        return response()->json([
            'message' => 'User logged out successfully !'
        ]);
    }
}
