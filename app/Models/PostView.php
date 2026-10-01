<?php

namespace App\Models;

use Database\Factories\PostViewFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PostView extends Model
{
    /** @use HasFactory<PostViewFactory> */
    use HasFactory;

    protected $fillable = [
        'post_id',
        'user_id',
        'user_ip',
        'user_agent',
    ];

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
