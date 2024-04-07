<?php
declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Str;

class PostCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $bodyLengthLimit = 100;

        return [
            'data' => $this->collection->transform(function($post) use ($bodyLengthLimit) {
                return [
                    'id' => $post->id,
                    'title' => $post->title,
                    'body' => Str::limit($post->body, $bodyLengthLimit, '...'),
                    'comments_count' => $post->comments->count(),
                    'likes_count' => $post->likes->count(),
                ];
            }),
        ];
    }
}
