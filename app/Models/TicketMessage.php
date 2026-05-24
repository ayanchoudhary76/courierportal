<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketMessage extends Model
{
    protected $fillable = [
        'ticket_id',
        'sender_role',
        'sender_id',
        'message',
        'is_internal',
    ];

    protected function casts(): array
    {
        return [
            'sender_role' => 'string',
            'is_internal' => 'boolean',
        ];
    }

    public function ticket()
    {
        return $this->belongsTo(SupportTicket::class, 'ticket_id');
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
}
