<?php
declare(strict_types=1);

namespace App\Traits;

use Illuminate\Support\Str;

trait SetSlug
{
    public function setSlugAttribute(string $value): void
    {
        if (static::whereSlug($slug = Str::slug($value))->exists()) {
            $slug = $this->incrementSlug($slug);
        }

        $this->attributes['slug'] = $slug;
    }

    public function incrementSlug(string $slug): string
    {
        $postClassName = 'App\Models\Post';

        if (static::class === $postClassName) {
            $max = static::where('title', 'ILIKE', $this->title)->latest('id')->value('slug');
        } else {
            $max = static::where('name', 'ILIKE', $this->name)->latest('id')->value('slug');
        }

        if ($max && is_numeric($max[-1])) {
            return preg_replace_callback('/(\d+)$/', function ($matches) {
                return $matches[1] + 1;
            }, $max);
        }

        return "{$slug}-2";
    }
}
