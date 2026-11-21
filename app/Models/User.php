<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * A user owns one profile,then pwedi pud siya mo write ug many posts, and can have many roles.
 */
class User extends Authenticatable
{
    use HasFactory;

    protected $fillable = ['name', 'email', 'password'];

    protected $hidden = ['password', 'remember_token'];

    /** One-to-one: the related profile stores this user's unique user_id. */
    public function profile(): HasOne
    {
        return $this->hasOne(OneToOneProfile::class);
    }

    /** One-to-many: each post stores this user's user_id. */
    public function posts(): HasMany
    {
        return $this->hasMany(OneToManyPost::class);
    }

    /** Many-to-many: users and roles are connected through the role_user pivot table. */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(ManyToManyRole::class, 'role_user', 'user_id', 'role_id')
            ->withTimestamps();
    }
}
