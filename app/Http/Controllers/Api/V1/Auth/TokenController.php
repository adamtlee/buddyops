<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;

class TokenController extends Controller
{
    private const TOKEN_LIFETIME_DAYS = 30;

    public function store(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'device_name' => ['required', 'string', 'max:120'],
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if ($user === null || Hash::check($credentials['password'], $user->password) === false) {
            return response()->json(['message' => 'The provided credentials are invalid.'], 401);
        }

        $expiresAt = now()->addDays(self::TOKEN_LIFETIME_DAYS);
        $token = $user->createToken(
            $credentials['device_name'],
            ['api:read'],
            $expiresAt,
        );

        return response()->json([
            'data' => [
                'access_token' => $token->plainTextToken,
                'token_type' => 'Bearer',
                'expires_at' => $expiresAt->toISOString(),
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ],
            ],
        ], 201);
    }

    public function destroy(Request $request): JsonResponse
    {
        $token = $request->user()->currentAccessToken();

        if ($token instanceof PersonalAccessToken) {
            $token->delete();
        }

        return response()->json(null, 204);
    }

    public function ping(Request $request): JsonResponse
    {
        return response()->json([
            'data' => [
                'status' => 'ok',
                'user_id' => $request->user()->id,
            ],
        ]);
    }

    public function me(Request $request): UserResource
    {
        return new UserResource($request->user());
    }
}
