<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SupportTicket extends Model
{
    protected $fillable = [
        'ticket_number',
        'institution_id',
        'user_id',
        'assigned_to',
        'subject',
        'category',
        'priority',
        'status',
    ];

    /**
     * যে প্রতিষ্ঠান টিকিটটি তৈরি করেছে।
     */
    public function institution(): BelongsTo
    {
        return $this->belongsTo(Madrasa::class, 'institution_id');
    }

    /**
     * টিকিটটি যিনি তৈরি করেছেন।
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * টিকিটে যাকে Assign করা হয়েছে।
     */
    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * টিকিটের সব Reply।
     */
    public function replies(): HasMany
    {
        return $this->hasMany(SupportTicketReply::class, 'support_ticket_id');
    }
}
