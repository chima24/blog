<?php
declare(strict_types=1);

namespace App\Models;

use App\Traits\Likeable;
use App\Traits\SetSlug;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Post extends Model
{
    use HasFactory, SetSlug, Likeable;

    protected $fillable = ['title', 'slug', 'body', 'category_id'];

    /**
     * Get the user that owns the post.
     *
     * @return BelongsTo
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id', 'id');
    }

    /**
     * Get the comments for the post.
     *
     * @return HasMany
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Get full url of the blog post.
     *
     * @return string
     */
    public function path(): string
    {
        return url("/posts/{$this->slug}");
    }

    /**
     * Retrieve post by slug column in the db table.
     *
     * @return string
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
