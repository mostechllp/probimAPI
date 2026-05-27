<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Api\ApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;

class LoginController extends ApiController
{
    /**
     * Login (Single endpoint for all users)
     */
    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return $this->error('Validation Error', 422, $validator->errors());
        }

        $loginField = filter_var($request->username, FILTER_VALIDATE_EMAIL)
            ? 'email'
            : 'username';

        if (
            !$token = auth('api')->attempt([
                $loginField => $request->username,
                'password' => $request->password
            ])
        ) {
            return $this->error('Unauthorized - Invalid credentials', 401);
        }

        $user = auth('api')->user();

        if ($user->status !== 'active') {
            auth('api')->logout();
            return $this->error('Account is inactive', 403);
        }

        return $this->respondWithToken($token, $user);
    }

    /**
     * Response with token + user + employee + roles
     */
    protected function respondWithToken($token, $user): JsonResponse
    {
        $employee = $user->employee;
        $role = $user->role;

        return $this->success([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,

            'user' => [
                'id' => $user->id,
                'username' => $user->username,
                'email' => $user->email,
                'status' => $user->status,
                'avatar' => $employee ? $employee->avatar_url : $user->avatar_url,
                'type' => $user->type,
                'role' => $role ? [
                    'id' => $role->id,
                    'name' => $role->name,
                ] : null,
                'employee' => $employee ? [
                    'id' => $employee->id,
                    'name' => trim(($employee->first_name ?? '') . ' ' . ($employee->last_name ?? '')),
                    'employee_id' => $employee->employee_id,
                ] : null,
                'permissions' => $this->formatPermissions($user),
                'sidebar_modules' => $this->formatSidebarModules($user),
            ]
        ], 'Login successful');
    }

    /**
     * Logout
     */
    public function logout(): JsonResponse
    {
        auth('api')->logout();
        return $this->success(null, 'Successfully logged out');
    }

    /**
     * Get logged-in user
     */
    public function me(): JsonResponse
    {
        if (!auth('api')->check()) {
            return $this->error('Unauthenticated', 401);
        }

        $user = auth('api')->user();
        return $this->respondWithToken(null, $user);
    }

    public function getMyPermissions(): JsonResponse
    {
        $user = auth('api')->user();
        return $this->success($this->formatPermissions($user));
    }

    public function getMySidebarModules(): JsonResponse
    {
        $user = auth('api')->user();
        return $this->success($this->formatSidebarModules($user));
    }

    protected function formatPermissions($user)
    {
        if (!$user->role)
            return [];

        if ($user->role->name === 'Admin') {
            return ['all' => true];
        }

        return $user->role->permissions->mapWithKeys(function ($p) {
            return [
                $p->module->slug => [
                    'read' => (bool) $p->can_read,
                    'edit' => (bool) $p->can_edit,
                    'delete' => (bool) $p->can_delete,
                ]
            ];
        });
    }

    protected function formatSidebarModules($user)
    {
        if (!$user->role)
            return [];

        if ($user->role->name === 'Admin') {
            return \App\Models\Module::where('status', 'active')->get();
        }

        return $user->role->permissions()
            ->where('can_read', true)
            ->with([
                'module' => function ($q) {
                    $q->where('status', 'active');
                }
            ])
            ->get()
            ->pluck('module')
            ->filter();
    }
}