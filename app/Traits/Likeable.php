<?php
declare(strict_types=1);

namespace App\Traits;

use App\Models\Like;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait Likeable
{
    /**
     * Get the likes for the model.
     *
     * @return MorphMany
     */
    public function likes(): MorphMany
    {
        return $this->morphMany(Like::class, 'likeable');
    }

    public function like(): ?Model
    {
        if ( !$this->likes()->where(['user_id' => auth()->id()])->exists() && !($this->owner->id === auth()->id())) {
            return $this->likes()->create(['user_id' => auth()->id()]);
        }

        return null;
    }

    public function unlike(): ?int
    {
        if ($this->likes()->where(['user_id' => auth()->id()])->exists() && $this->owner->id === auth()->id()) {
            return $this->likes()->delete();
        }

        return null;
    }
}
