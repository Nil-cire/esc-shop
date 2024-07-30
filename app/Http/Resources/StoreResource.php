<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StoreResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            /**
             * @var boolean
             */
            'is_open' => $this->is_open,
            'name' => $this->name,
            'description' => $this->description,
            'type' => $this->type,
            'banner_image_path' => $this->banner_image_path,
        ];
    }
}
