<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserProfile extends Model
{

    protected $fillable = [
        'phone',
        'birth_date',
        'sex',
        'marital_status',
        'address',
        'city',
        'province',
        'zip_code',
        'avatar',
    ];


    /**
     * User
     */
    public function user():BelongsTo {
        return $this->belongsTo(User::class);
    }
}
