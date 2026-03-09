<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Domain\Auth\Actions\LoginAction;
use App\Domain\Auth\Actions\LogoutAction;
use App\Domain\Auth\Actions\RegisterTenantAction;
use App\Domain\Auth\DTOs\LoginData;
use App\Domain\Auth\DTOs\RegisterTenantData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\Auth\AuthenticatedResource;
use App\Http\Resources\Auth\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function register(
        RegisterRequest $request,
        RegisterTenantAction $action,
    ): JsonResponse {
        $result = $action->execute(
            RegisterTenantData::fromArray($request->validated())
        );

        $result['user']->load('tenants');

        return response()->json([
            'message' => 'Registration successful.',
            'data' => new AuthenticatedResource($result['user'], $result['token']),
        ], 201);
    }

    public function login(
        LoginRequest $request,
        LoginAction $action
    ): JsonResponse {
        $result = $action->execute(
            LoginData::fromArray($request->validated())
        );

        $result['user']->load('tenants');

        return response()->json([
            'message' => 'Login successful.',
            'data' => new AuthenticatedResource($result['user'], $result['token']),
        ]);
    }

    public function logout(
        Request $request,
        LogoutAction $action
    ): JsonResponse {
        $action->execute($request->user());

        return response()->json([
            'message' => 'Logged out successfully.',
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        $request->user()->load('tenants');

        return response()->json([
            'data' => new UserResource($request->user()),
        ]);
    }
}
