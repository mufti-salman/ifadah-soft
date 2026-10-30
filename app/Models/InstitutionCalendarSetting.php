<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InstitutionCalendarSetting extends Model
{
    use HasFactory;

    public const MODE_ENGLISH = 'english';
    public const MODE_BANGLA = 'bangla';
    public const MODE_HIJRI = 'hijri';

    public const AVAILABLE_MODES = [
        self::MODE_ENGLISH,
        self::MODE_BANGLA,
        self::MODE_HIJRI,
    ];

    protected $fillable = [
        'institution_id',
        'calendar_mode',
        'hijri_adjustment',
    ];

    protected $casts = [
        'institution_id' => 'integer',
        'hijri_adjustment' => 'integer',
    ];

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Madrasa::class, 'institution_id');
    }
}