<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use App\Models\WorkoutTracker;
use App\Models\Role;
use App\Models\UserProfile;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'status',
        'role_id',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Check Permission
     */
    public function hasPermission(string $permission) : bool{
        // check active role
        if( !$this->role || $this->role->status !== 'active' ) return false;

        // check full access
        if( $this->isAdmin() ) return true;


        // Check assigned permission
        return $this->role
            ->permissions()
            ->where('name', $permission)
            ->exists();
    }

    /**
     * Role
     */
    public function role(): BelongsTo {
        return $this->belongsTo(Role::class);
    }

    /**
     * Workout Connect
     */
    public function workouts(){
        return $this->hasMany(WorkoutTracker::class);
    }

    /**
     * Checking if user is an Admin
     */
    public function isAdmin(): bool{
        return $this->role?->name === 'Admin'
                && $this->role?->status === 'active';
    }

    /**
     * Checking if user is a regular user
     */
    public function isUser(): bool{
        return $this->role?->name === 'User'
                && $this->role?->status === 'active';
    }

    /**
     * User Profile
     */
    public function profile():HasOne {
        return $this->hasOne(UserProfile::class);
    }


    /**
     * Getting the initials of the user name using STR Helper
     */
    public function getInitialsAttribute(): string{
        $words = explode(' ', $this->name);
        $initials = '';

        foreach ($words as $word) {
            $initials .= strtoupper(substr($word, 0, 1));
        }

        return substr($initials, 0, 2);
    }

    /**
     * Generate permanent color per name
     */
    public function getAvatarColorAttribute(): string{
        $colors = [
            'bg-red-500 border-red-600',
            'bg-orange-500 border-orange-600',
            'bg-amber-500 border-amber-600',
            'bg-emerald-500 border-emerald-600',
            'bg-teal-500 border-teal-600',
            'bg-blue-500 border-blue-600',
            'bg-indigo-500 border-indigo-600',
            'bg-purple-500 border-purple-600',
            'bg-pink-500 border-pink-600',
            'bg-rose-500 border-rose-600',
        ];

        $hash = abs(crc32($this->name));
        return $colors[$hash % count($colors)];
    }
}
