<?php
declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'owner_name'    => $this->owner->name,
            'owner_surname' => $this->owner->surname,
            'title'         => $this->title,
            'body'          => $this->body,
            'comments'      => $this->comments,
        ];
    }
}
