<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupportTicketReply extends Model
{
    protected $fillable = [
        'support_ticket_id',
        'user_id',
        'message',
        'attachment_path',
    ];

    /**
     * Reply-টি যে Ticket-এর অধীনে রয়েছে।
     */
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(SupportTicket::class, 'support_ticket_id');
    }

    /**
     * Reply যিনি দিয়েছেন।
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
