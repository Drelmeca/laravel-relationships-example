<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** notes para explanation 
 * 
 * A user's profile in the one-to-one relationship.
 * So each user can have only one profile and then sng each profile belongs to exactly one user.
 */
class OneToOneProfile extends Model
{
    use HasFactory;

    protected $table = 'profiles';

    protected $fillable = ['user_id', 'bio', 'website'];

    /** The user who owns this profile. */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
