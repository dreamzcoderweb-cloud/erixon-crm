<?php

namespace App\Traits;

use App\Models\User;
use Illuminate\Http\JsonResponse;

trait HasApiPermissionCheck
{
    /**
     * Check if user has given permission or is an Admin/Super Admin.
     */
    protected function hasPermission(?User $user, string $permission): bool
    {
        if (!$user) {
            return false;
        }

        if ($user->isAdmin() || $user->isSuperAdmin()) {
            return true;
        }

        return $user->can($permission);
    }

    /**
     * Standardized 403 Forbidden response for API permission failure.
     */
    protected function permissionDeniedResponse(string $module = 'this module'): JsonResponse
    {
        return response()->json([
            'status'  => false,
            'message' => "Access denied. You do not have permission to access {$module}.",
        ], 403);
    }
}
