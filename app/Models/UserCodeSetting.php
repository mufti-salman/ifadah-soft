<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserCodeSetting extends Model
{
    protected $fillable = [
        'institution_id',
        'role_slug',
        'start_code',
    ];

    protected $casts = [
        'institution_id' => 'integer',
        'start_code' => 'integer',
    ];

    public function institution(): BelongsTo
    {
        return $this->belongsTo(
            Madrasa::class,
            'institution_id'
        );
    }
}