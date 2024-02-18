<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public const TOKEN_NAME = 'app_api_token';

    /**
     * Login
     */
    public function login(Request $request): JsonResponse
    {
        $user = Account::where('email', $request->email)->first();
        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages(
                [
                    'email' => ['The provided credentials are incorrect.'],
                ]
            );
        }

        return response()->json(
            [
                'accessToken' => $user->createToken(self::TOKEN_NAME)->plainTextToken,
            ]
        );
    }

    /**
     * User Info
     */
    public function user(Request $request): JsonResponse
    {
        return response()->json(['user' => $request->user()], 200);
    }

    /**
     * Logout
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'ログアウトしました。'], 200);
    }
}
