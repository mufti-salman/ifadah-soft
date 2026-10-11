<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'institution_id',
        'voucher_no',
        'type',
        'fund_id',
        'payment_method_id',
        'cashier_id',
        'total_amount',
        'date',
        'note'
    ];

    public function items()
    {
        return $this->hasMany(TransactionItem::class);
    }

    public function fund()
    {
        return $this->belongsTo(Fund::class);
    }

    public function paymentMethod()
    {
        return $this->belongsTo(
            PaymentMethod::class,
            'payment_method_id'
        );
    }

    public function cashier()
    {
        return $this->belongsTo(Cashier::class);
    }
}

