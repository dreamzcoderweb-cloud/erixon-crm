<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditLogger
{
    /**
     * Create a manual audit log entry.
     */
    public static function log(
        string $event,
        string $module,
        string $description,
        ?array $oldValues = null,
        ?array $newValues = null,
        $auditable = null,
        ?int $userId = null
    ): ?AuditLog {
        try {
            return AuditLog::create([
                'user_id'        => $userId ?? Auth::id(),
                'event'          => $event,
                'auditable_type' => $auditable ? get_class($auditable) : null,
                'auditable_id'   => $auditable ? $auditable->getKey() : null,
                'module'         => $module,
                'description'    => $description,
                'old_values'     => $oldValues,
                'new_values'     => $newValues,
                'ip_address'     => Request::ip(),
                'user_agent'     => Request::userAgent(),
                'url'            => Request::fullUrl(),
            ]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Manual AuditLog failed: ' . $e->getMessage());
            return null;
        }
    }
}
