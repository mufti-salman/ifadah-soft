<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subscription extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'institution_id',
        'subscription_plan_id',
        'billing_cycle',
        'status',
        'starts_at',
        'ends_at',
        'trial_starts_at',
        'trial_ends_at',
        'cancelled_at',
        'amount',
        'currency',
        'student_limit',
        'sms_limit',
        'auto_renew',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'trial_starts_at' => 'datetime',
            'trial_ends_at' => 'datetime',
            'cancelled_at' => 'datetime',
            'amount' => 'decimal:2',
            'student_limit' => 'integer',
            'sms_limit' => 'integer',
            'auto_renew' => 'boolean',
        ];
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(
            SubscriptionPlan::class,
            'subscription_plan_id'
        );
    }

    public function institution(): BelongsTo
    {
        return $this->belongsTo(
            Madrasa::class,
            'institution_id'
        );
    }

    public function payments()
    {
        return $this->hasMany(SubscriptionPayment::class);
    }
}