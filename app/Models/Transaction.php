<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'institution_id',
        'created_by',
        'voucher_no',
        'type',
        'fund_id',
        'payment_method_id',
        'total_amount',
        'date',
        'note',
    ];

    /**
     * Transaction items.
     */
    public function items(): HasMany
    {
        return $this->hasMany(TransactionItem::class);
    }

    /**
     * Transaction fund.
     */
    public function fund(): BelongsTo
    {
        return $this->belongsTo(Fund::class);
    }

    /**
     * Payment method used for this transaction.
     */
    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(
            PaymentMethod::class,
            'payment_method_id'
        );
    }

    /**
     * User who created the transaction.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'created_by'
        );
    }
}
