<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "uuid"=> $this->uuid,
            "store_uid"=> $this->store_uid,
            "name"=> $this->name,
            "description"=> $this->description,
            "spec"=> $this->spec,
            "note"=> $this->note,
            /**
            * @var float
            */
            "price"=> $this->price,
            /**
             * @var float
             */
            "special_price"=> $this->special_price,
            "special_price_start"=> $this->special_price_start,
            "special_price_end"=> $this->special_price_end,
            /**
             * @var integer
             */
            "stock"=> $this->stock,
            "image_url"=> $this->image_url,
            "link"=> $this->link,
            /**
             * @var boolean
             */
            "is_enable"=> $this->is_enable
        ];
    }
}
