<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use Override;

use App\Models\User;

class UserPreferences extends Model
{
    //fillable
    protected $fillable = [
        'theme',
        'weight_unit',
        'date_format',
        'dashboard_landing',
        'notifications_enabled',
        'email_notifications',
    ];

    protected function casts(): array
    {
        return [
            'notifications_enabled',
            'email_notifications',
        ];
    }

    public function user(): BelongsTo{
        return $this->belongsTo(User::class);
    }
}
