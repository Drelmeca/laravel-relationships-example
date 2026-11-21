<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/** notes para explanation 
 *
 * A role can be assigned to many users, and a user can have many roles. 
 */

class ManyToManyRole extends Model
{
    use HasFactory;

    protected $table = 'roles';

    protected $fillable = ['name'];

    /** Users assigned this role. */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'role_user', 'role_id', 'user_id')
            ->withTimestamps();
    }
}
