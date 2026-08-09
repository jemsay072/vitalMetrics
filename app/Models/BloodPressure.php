<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Carbon;
use App\Model\User;

class BloodPressure extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'systolic',
        'diastolic',
        'pulse',
        'terms',
        'risk_level',
        'notes',
        'reading_time'
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    //Automatic serialize these custom attributes to JSON For Alphine.js

    protected $appends = [
        'formatted_time',
        'formatted_date',
        'formatted_reading',
    ];

    /**
     * Converts time
     */
    protected function formattedTime(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->reading_time
                ? Carbon::createFromFormat('H:i:s', $this->reading_time)->format('g:i A')
                : ''
        );
    }

    /**
     * Extract date created_at
     */
    protected function formattedDate(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->created_at
                ? $this->created_at->format('M d, Y')
                : ''
        );
    }

    /**
     * Combined Time & Date
     */

    protected function formattedReading(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->formatted_time && $this->formatted_date
                ? "{$this->formatted_time} ({$this->formatted_date})"
                : ''
        );
    }
}
