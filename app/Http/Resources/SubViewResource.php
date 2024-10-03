<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SubViewResource extends JsonResource
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
            'type' => $this->type,
            'frame' => $this->frame,
            'image_name' => $this->image_name,
            'text' => $this->frame,
            'font_name' => $this->frame,
            'font_size' => $this->frame,
            'text_color' => $this->frame,
            'created_at' => $this->created_at
        ];
    }
}
