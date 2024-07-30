<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UsersResource extends JsonResource
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
            "name"=> $this->name,
            /**
             * @var integer
             */
            "buy_count"=> $this->buy_count,
            /**
             * @var integer
             */
            "reported_count"=> $this->reported_count,
            /**
             * @var boolean
             */
            "register_completed"=> $this->register_completed
        ];
    }
}
