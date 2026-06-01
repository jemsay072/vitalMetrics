<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Model\User;

class WeightTracker extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'weight',
        'measurement_date',
        'notes',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }
}
