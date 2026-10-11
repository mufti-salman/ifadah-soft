<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Traits\BelongsToInstitution;

class Fund extends Model
{
    use HasFactory;
    use BelongsToInstitution;

    protected $fillable = [
        'institution_id',
        'user_id',
        'name',
        'type',
        'balance',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
    ];

    /*
    |--------------------------------------------------------------------------
    | User
    |--------------------------------------------------------------------------
    */

    public function user()
    {
        return $this->belongsTo(User::class);
    }


    /*
    |--------------------------------------------------------------------------
    | Institution
    |--------------------------------------------------------------------------
    */

    public function institution()
    {
        return $this->belongsTo(Madrasa::class, 'institution_id');
    }


    /*
    |--------------------------------------------------------------------------
    | Transactions
    |--------------------------------------------------------------------------
    */

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }


    /*
    |--------------------------------------------------------------------------
    | Ledgers
    |--------------------------------------------------------------------------
    */

    public function ledgers()
    {
        return $this->hasMany(Ledger::class);
    }
}
