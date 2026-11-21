<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** notes para explanation 
 *
 * Each post belongs to one user; and also pwedi pud si user can have many posts. 
 */
class OneToManyPost extends Model
{
    use HasFactory;

    protected $table = 'posts';

    protected $fillable = ['user_id', 'title', 'body'];

    /** The user who wrote this post. */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
