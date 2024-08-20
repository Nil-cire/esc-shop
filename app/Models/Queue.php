<?php

namespace App\Models;

use App\Util\DateTimeFormatter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Str;

class Queue extends Model
{

    public $incrementing = false;
    use HasFactory;
    protected $table = 'queue';  // 資料表名稱
    protected $primaryKey = 'uuid';   // 主鍵
    public $timestamps = false;
    protected $fillable =
        ['uuid', 'scan_uuid', 'store_uuid', 'current_number', 'await_number', 'store_name', 'note', 'mode', 'start_time', 'end_time', 'is_pause', 'pause_message', 'is_close', 'terminal_message'];

    protected $casts = [
        // 'rating' => 'decimal:2'
        'is_pause' => 'boolean',
        'is_close' => 'boolean',
    ];

    public function createQueue(
        $storeName,
        $note,
        $startTime,
        $endTime,
        $mode,
        $storeUuid
    ) {

        $uuid = Str::uuid();
        $scanUuid = Str::orderedUuid();

        $newQueue = [
            'uuid' => $uuid,
            'scan_uuid' => $scanUuid,
            'store_uuid' => $storeUuid,
            'store_name' => $storeName,
            'note' => $note,
            'start_time' => (new DateTimeFormatter)->toMillieTimestamp($startTime),
            'end_time' => (new DateTimeFormatter)->toMillieTimestamp($endTime),
            'mode' => $mode
        ];

        $filteredQueue = array_filter(
            $newQueue,
            function ($value) {
                return $value != null && trim($value) != "";
            }
        );

        return $this->create($filteredQueue);
    }

    public function restartQueue(
        // $storeName,
        $uuid,
        $note,
        $startTime,
        $endTime,
        $mode,
        $update_qr_code
    ) {
        $scanUuid = $update_qr_code ? Str::orderedUuid() : null;
        $newQueue = [
            'scan_uuid' => $scanUuid,
            'note' => $note,
            'start_time' => (new DateTimeFormatter)->toMillieTimestamp($startTime),
            'end_time' => (new DateTimeFormatter)->toMillieTimestamp($endTime),
            'current_number' => intval(0),
            'await_number' => intval(0),
            'mode' => $mode
        ];

        $filteredQueue = array_filter(
            $newQueue,
            function ($value) {
                return $value !== null && trim($value) != "";
            }
        );

        return tap(Queue::where(['uuid' => $uuid])->first())
            ->update($filteredQueue);
    }

    public function nextCustomer(
        $uuid
    )
    {
        return tap(
            Queue::where(['uuid' => $uuid])->first(), 
            function($data) {
                $currentNumber = $data->current_number;
                $update = [
                    'current_number' => intval($currentNumber + 1)
                ];
                $data->update($update);
            }
        );
    }

    public function enqueue(
        $uuid
    )
    {
        return tap(
            Queue::where(['uuid' => $uuid])->first(), 
            function($data) {
                $awaitNumber = $data->await_number;
                $update = [
                    'await_number' => intval($awaitNumber + 1)
                ];
                $data->update($update);
            }
        );
    }

    public function pause(
        $uuid,
        $isPause
    )
    {
        $update = [
            "is_pause" => $isPause
        ];

        return tap(Queue::where(['uuid' => $uuid])->first())
            ->update($update);
    }

    public function close(
        $uuid,
        $isClose
    )
    {
        $update = [
            "is_close" => $isClose
        ];

        return tap(Queue::where(['uuid' => $uuid])->first())
            ->update($update);
    }

    public function updateInfo(
        $uuid,
        $storeName,
        $note,
        $startTime,
        $endTime,
        // $mode,
        $pauseMessage,
        $terminalMessage
    ) {
        $update = [
            'store_name' => $storeName,
            'note' => $note,
            'start_time' => (new DateTimeFormatter)->toMillieTimestamp($startTime),
            'end_time' => (new DateTimeFormatter)->toMillieTimestamp($endTime),
            'pause_message' => $pauseMessage,
            'terminal_message' => $terminalMessage
        ];

        $filteredUpdate = array_filter(
            $update,
            function ($value) {
                return $value !== null && trim($value) != "";
            }
        );

        return tap(Queue::where(['uuid' => $uuid])->first())
            ->update($filteredUpdate);
    }

    public function bindStore(
        $uuid,
        $storeUuid
    ) {
        $update = [
            "store_uuid" => $storeUuid
        ];

        return tap(Queue::where(['uuid' => $uuid])->first())
            ->update($update);
    }

    public function remove(
        $uuid
    ) {
        return tap(Queue::where(['uuid' => $uuid])->first())
            ->delete();
    }
}
