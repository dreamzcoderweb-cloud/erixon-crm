<?php

namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;

trait Auditable
{
    /**
     * Boot the auditable trait for a model.
     */
    public static function bootAuditable(): void
    {
        static::created(function ($model) {
            $model->recordAuditLog('created');
        });

        static::updated(function ($model) {
            $model->recordAuditLog('updated');
        });

        static::deleted(function ($model) {
            $model->recordAuditLog('deleted');
        });
    }

    /**
     * Record an audit log entry for this model.
     */
    public function recordAuditLog(string $event): void
    {
        try {
            $ignoredAttributes = array_merge(
                $this->auditIgnore ?? [],
                ['password', 'remember_token', 'two_factor_secret', 'two_factor_recovery_codes', 'updated_at']
            );

            $oldValues = null;
            $newValues = null;

            if ($event === 'created') {
                $rawValues = $this->attributesToArray();
                $newValues = array_diff_key($rawValues, array_flip($ignoredAttributes));
            } elseif ($event === 'updated') {
                $changes = $this->getChanges();
                // Exclude ignored fields
                $changes = array_diff_key($changes, array_flip($ignoredAttributes));

                if (empty($changes)) {
                    return; // No meaningful changes to log
                }

                $newValues = $changes;
                $oldValues = [];

                foreach ($changes as $key => $newValue) {
                    $oldValues[$key] = $this->getOriginal($key);
                }
            } elseif ($event === 'deleted') {
                $rawValues = $this->attributesToArray();
                $oldValues = array_diff_key($rawValues, array_flip($ignoredAttributes));
            }

            $module = $this->getAuditModuleName();
            $description = $this->getAuditDescription($event, $oldValues, $newValues);

            AuditLog::create([
                'user_id'        => Auth::id(),
                'event'          => $event,
                'auditable_type' => get_class($this),
                'auditable_id'   => $this->getKey(),
                'module'         => $module,
                'description'    => $description,
                'old_values'     => $oldValues,
                'new_values'     => $newValues,
                'ip_address'     => Request::ip(),
                'user_agent'     => Request::userAgent(),
                'url'            => Request::fullUrl(),
            ]);
        } catch (\Throwable $e) {
            // Log silently to avoid breaking the core business transactions
            \Illuminate\Support\Facades\Log::warning('AuditLog creation failed: ' . $e->getMessage());
        }
    }

    /**
     * Get a user-friendly module name.
     */
    public function getAuditModuleName(): string
    {
        if (property_exists($this, 'auditModule') && !empty($this->auditModule)) {
            return $this->auditModule;
        }

        $className = class_basename($this);
        return Str::headline(Str::plural($className));
    }

    /**
     * Get a human-readable action description.
     */
    public function getAuditDescription(string $event, ?array $oldValues, ?array $newValues): string
    {
        $label = class_basename($this);
        $identifier = $this->name ?? $this->lead_title ?? $this->title ?? $this->customer_name ?? $this->lead_name ?? $this->subject ?? ('#' . $this->getKey());

        $actionVerb = match ($event) {
            'created' => 'Created',
            'updated' => 'Updated',
            'deleted' => 'Deleted',
            default   => ucfirst($event),
        };

        $desc = "{$actionVerb} {$label} '{$identifier}'";

        if ($event === 'updated' && !empty($newValues)) {
            $changedKeys = array_keys($newValues);
            if (count($changedKeys) <= 4) {
                $desc .= ' (' . implode(', ', $changedKeys) . ')';
            } else {
                $desc .= ' (' . count($changedKeys) . ' fields modified)';
            }
        }

        return $desc;
    }
}
