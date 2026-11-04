<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserCodeSetting extends Model
{
    protected $fillable = [
        'institution_id',
        'role_slug',
        'user_type_id',
        'start_code',
        'manual_start_code',
        'gender_preference',
    ];

    protected $casts = [
        'institution_id' => 'integer',
        'start_code' => 'integer',
        'manual_start_code' => 'integer',
    ];

    public function institution(): BelongsTo
    {
        return $this->belongsTo(
            Madrasa::class,
            'institution_id'
        );
    }

    public function userType(): BelongsTo
{
    return $this->belongsTo(
        UserType::class,
        'user_type_id'
    );
}
}