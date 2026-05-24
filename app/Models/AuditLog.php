<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    // updated_at is not needed for audit logs
    const UPDATED_AT = null;

    protected $table = 'audit_logs';

    protected $fillable = [
        'admin_id',
        'action',
        'target_table',
        'target_id',
        'old_value',
        'new_value',
        'ip_address',
    ];

    protected function casts(): array
    {
        return [
            'old_value' => 'array',
            'new_value' => 'array',
        ];
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
