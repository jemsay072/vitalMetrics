<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Model\User;

class WorkoutTracker extends Model
{
    use HasFactory;
    //

    protected $fillable = [
        'user_id',
        'activity_type',
        'duration_minutes',
        'distance_km',
        'calories_burned',
        'speed_kmh',
        'steps',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

}
