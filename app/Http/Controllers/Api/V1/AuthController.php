<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\RoleEnum;
use App\Http\Controllers\Controller;
use App\Http\Helpers\ApiResponse;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use OpenApi\Attributes as OA;

#[OA\Info(
    version: "1.0.0",
    description: "REST API documentation for HostYourHackathon platform",
    title: "HostYourHackathon API"
)]
#[OA\SecurityScheme(
    securityScheme: "bearerAuth",
    type: "http",
    bearerFormat: "JWT",
    scheme: "bearer"
)]
class AuthController extends Controller
{
    #[OA\Post(
        path: "/api/v1/auth/register",
        operationId: "registerUser",
        summary: "Register a new user",
        tags: ["Auth"]
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ["name", "email", "password", "password_confirmation"],
            properties: [
                new OA\Property(property: "name", type: "string"),
                new OA\Property(property: "email", type: "string", format: "email"),
                new OA\Property(property: "password", type: "string", format: "password"),
                new OA\Property(property: "password_confirmation", type: "string", format: "password")
            ]
        )
    )]
    #[OA\Response(response: 200, description: "Successful operation", content: new OA\JsonContent())]
    #[OA\Response(response: 403, description: "Registration disabled")]
    #[OA\Response(response: 422, description: "Validation failed")]
    public function register(Request $request, \App\Services\SettingService $settings): JsonResponse
    {
        if (!$settings->get('allow_registration', true)) {
            return ApiResponse::error('Registration is currently disabled', [], 403);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', \Illuminate\Validation\Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $user->assignRole(RoleEnum::Participant->value);

        $token = $user->createToken('auth_token')->plainTextToken;

        return ApiResponse::success([
            'user' => new UserResource($user),
            'token' => $token,
        ], 'User registered successfully');
    }

    #[OA\Post(
        path: "/api/v1/auth/login",
        operationId: "loginUser",
        summary: "Login user and generate token",
        tags: ["Auth"]
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ["email", "password"],
            properties: [
                new OA\Property(property: "email", type: "string", format: "email"),
                new OA\Property(property: "password", type: "string", format: "password")
            ]
        )
    )]
    #[OA\Response(response: 200, description: "Successful operation", content: new OA\JsonContent())]
    #[OA\Response(response: 401, description: "Unauthenticated")]
    #[OA\Response(response: 422, description: "Validation failed")]
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return ApiResponse::error('Invalid credentials', [], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return ApiResponse::success([
            'user' => new UserResource($user),
            'token' => $token,
        ], 'Logged in successfully');
    }

    #[OA\Post(
        path: "/api/v1/auth/logout",
        operationId: "logoutUser",
        summary: "Logout user and revoke token",
        security: [["bearerAuth" => []]],
        tags: ["Auth"]
    )]
    #[OA\Response(response: 200, description: "Successful operation", content: new OA\JsonContent())]
    #[OA\Response(response: 401, description: "Unauthenticated")]
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return ApiResponse::success(null, 'Logged out');
    }

    #[OA\Get(
        path: "/api/v1/auth/me",
        operationId: "getAuthenticatedUser",
        summary: "Get authenticated user details",
        security: [["bearerAuth" => []]],
        tags: ["Auth"]
    )]
    #[OA\Response(response: 200, description: "Successful operation", content: new OA\JsonContent())]
    #[OA\Response(response: 401, description: "Unauthenticated")]
    public function me(Request $request): JsonResponse
    {
        return ApiResponse::success(new UserResource($request->user()));
    }
}
