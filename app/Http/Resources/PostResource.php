<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        // return parent::toArray($request);
        return [
            'id' => $this->id,
            'title' => $this->post_title,
            'status' => $this->post_status,
            'categories' => TermResource::collection($this->postTerm),
            'sub_views' => SubViewResource::collection($this->subView),
            'created_at' => $this->created_at
        ];
    }
}
