<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Collection\Collection;

class FavoriteModel extends Model
{
    use HasFactory;
    protected $table = 'favorites';

    protected $primaryKey = 'id'; // 主鍵

    protected $fillable = [
        'user_uid',
        'store_uid',
    ];

    protected $casts = [
        // 'rating' => 'decimal:2'
    ];

    // token
    public function favorite($user_uid, $store_uid)
    {
        return $this->create(
            [
                'user_uid' => $user_uid,
                'store_uid' => $store_uid
            ]
        );
    }

    // token
    public function unfavorite($user_uid, $store_uid)
    {
        return $this->where('user_uid', $user_uid)->where('store_uid', $store_uid)->delete();
    }

    // token
    public function get_favorites($user_uid)
    {
        $data = StoreModel::join('favorites', 'favorites.store_uid', '=', 'stores.uuid')
            ->where('user_uid', $user_uid)
            ->where('is_deleted', false)
            ->where('is_banned', false)
            ->select(['uuid', 'is_open', 'name', 'description', 'type', 'banner_image_path']) 
            ->get();

        return $data;
    }
}
