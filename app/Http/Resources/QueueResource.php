<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QueueResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return parent::toArray($request);
        return [
            'uuid' => $this->uuid,
            'scan_uuid'=> $this->scan_uuid,
            'store_uuid'=> $this->store_uuid,
            /**
             * @var integer
             */
            'current_number'=> $this->current_number,
            /**
             * @var integer
             */
            'await_number'=> $this->await_number,
            'store_name'=> $this->store_name,
            'note'=> $this->note,
            'mode'=> $this->mode,
            'start_time'=> $this->start_time,
            'end_time'=> $this->end_time,
            /**
             * @var boolean
             */
            'is_pause'=> $this->is_pause,
            /**
             * @var boolean
             */
            'is_close'=> $this->is_close,
            'pause_message' => $this->pause_message,
            'terminal_message' => $this->terminal_message,
        ];
    }
}
