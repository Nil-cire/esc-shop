<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserModel extends Model
{
    protected $table = 'user';
    use HasFactory;

    protected $primaryKey = 'uuid'; // 主鍵

    protected $fillable = [
        'uuid',
        'name',
        'buy_count',
        'reported_count',
        'join_time'
    ];

    protected $casts = [
        'complete' => 'boolean'
    ];

    public function login() {

    }

    function register_user($platform, $token, $uuid, $name = '新使用者') {
        $now = new \DateTimeImmutable();
        $insertData = ['uuid'=>$uuid, 'name'=>$name, 'buy_count'=>0, 'reported_count'=>0, 'join_time'=>$now->getTimestamp()];
        $this->create($insertData);
    }

    function get_user($uid) 
    {
        return $this->where('uid', $uid)->first();
    }
}
