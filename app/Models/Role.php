<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    use SoftDeletes;

    protected static function booted()
    {
        static::deleting(function ($role) {
            if (!str_contains($role->name, '_d')) {
                $suffix = '_d' . $role->id;
                $maxLen = 125 - strlen($suffix);
                $role->name = substr($role->name, 0, $maxLen) . $suffix;
                $role->saveQuietly();
            }
        });
    }
}
