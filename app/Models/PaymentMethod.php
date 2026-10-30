<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PaymentMethod extends Model
{
    protected $fillable = [
        'institution_id',
        'user_id',
        'name',
        'icon',
        'account_type',
        'account_number',
        'mobile1',
        'mobile2',
        'address',
    ];

    /**
     * User who created the payment method.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Transactions associated with this payment method.
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }
}