<?php

namespace App\Helpers;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class AuditHelper
{
    /**
     * Write an audit log entry.
     */
    public static function log(
        string $action,
        string $table,
        int $targetId,
        array $oldValue = [],
        array $newValue = [],
        string $ip = null
    ): void {
        AuditLog::create([
            'admin_id'     => Auth::id(),
            'action'       => $action,
            'target_table' => $table,
            'target_id'    => $targetId,
            'old_value'    => $oldValue ?: null,
            'new_value'    => $newValue ?: null,
            'ip_address'   => $ip ?? request()->ip(),
        ]);
    }
}
