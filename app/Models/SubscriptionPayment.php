<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubscriptionPayment extends Model
{
    protected $fillable = [
        'subscription_id',
        'institution_id',
        'payment_method',
        'sender_number',
        'transaction_id',
        'amount',
        'currency',
        'status',
        'proof_path',
        'notes',
        'rejection_reason',
        'verified_by',
        'submitted_at',
        'verified_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'submitted_at' => 'datetime',
            'verified_at' => 'datetime',
        ];
    }

    /**
     * The subscription this payment belongs to.
     */
    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    /**
     * The institution that submitted this payment.
     */
    public function institution(): BelongsTo
    {
        return $this->belongsTo(Madrasa::class, 'institution_id');
    }

    /**
     * The admin who verified this payment.
     */
    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}