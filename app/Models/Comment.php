<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = ['post_id', 'user_id', 'name', 'email', 'body', 'approved'];

    protected function casts(): array
    {
        return [
            'approved' => 'boolean',
        ];
    }

    // --- Relationships ---

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // --- Scopes ---

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('approved', true);
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('approved', false);
    }
}
