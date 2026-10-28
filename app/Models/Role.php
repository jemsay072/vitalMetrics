<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

use App\Models\User;

class Role extends Model
{
    //

    protected $fillable = [
        'name',
        'description'
    ];

    /**
     * A role can belong to many users.
     */

    public function users(): HasMany{
        return $this->hasMany(User::class);
    }

    /**
     * A role handle many to many
     */
    public function permissions(): BelongsToMany{
        return $this->belongsToMany(Permission::class, 'role_permissions')->withTimestamps();
    }
}
