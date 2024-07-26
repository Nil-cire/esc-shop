<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreModel extends Model
{
    use HasFactory;
    protected $table = 'stores';

    protected $primaryKey = 'id'; // 主鍵

    protected $fillable = [
        'uuid',
        'user_id',
        'is_open',
        'is_deleted',
        'is_banned',
        'name',
        'description',
        'type',
        'banner_image_path'
    ];

    protected $casts = [
        // 'rating' => 'decimal:2'
        'is_open' => 'boolean',
        'is_deleted' => 'boolean',
        'is_banned' => 'boolean',
    ];

    public function index()
    {
        return $this->all();
    }

    public function get_by_user($user_uid)
    {
        return $this->where('user_id', $user_uid)->first();
    }

    function get_by_uid($uid) {
        return $this->where('uuid', $uid)->first();
    }

    public function add($userUid, $name, $description, $type)
    {
        // $uuid = $this->uuid;
        $uuid = \Str::orderedUuid();
        $storeData = [
            'uuid' => $uuid,
            'user_id' => $userUid,
            'name' => $name,
            'description' => $description,
            'type' => $type
        ];

        return $this->create($storeData);
    }

    public function updateInfo($storeUid, $storeName, $description = "", $type = null) {
        $infoData = [];

        if ($storeName) {
            $infoData['name'] = $storeName;
        }

        if ($description != null) {
            $infoData['description'] = $description;
        }

        if ($type != null) {
            $infoData['type'] = $type;
        }

        if (!empty($infoData)) {
            return $this->where('uuid', $storeUid)->update($infoData);
        } else {
            return null;
        }

    }

    public function open($storeUid, $open) {
        $this->where('uuid', $storeUid)->update(['is_open'=> $open]);
    }

    public function temporary_delete($storeUid, $delete) {
        $this->where('uuid', $storeUid)->update(['is_deleted'=> $delete]);
    }
}
