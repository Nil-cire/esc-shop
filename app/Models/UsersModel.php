<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsersModel extends Model
{
    use HasFactory;
    protected $table = 'users';

    protected $primaryKey = 'id'; // 主鍵

    protected $fillable = [
        'uuid',
        'name',
        'google_id',
        'apple_id',
        'facebook_id',
        'buy_count',
        'reported_count',
        'register_completed',
        'email'
    ];

    protected $casts = [
        // 'complete' => 'boolean'
    ];

    function get_user_by_email(string $email)
    {
        return $this->where('email', $email)->first();
    }

    function get_user_by_platform($platform, $platformToken)
    {
        $platformKey = "{$platform}_id";
        // $hashToken = \Hash::make($platformToken);
        $hashToken = \Crypt::encryptString($platformToken);
        return $this->where($platformKey, $hashToken)->first();
    }

    function add_user($email, $uuid, $name, $platform, $platformId)
    {
        $user = [
            'email' => $email,
            'uuid' => $uuid,
            'name' => $name,
            'register_completed' => true
        ];

        $hashId = \Hash::make($platformId);
        // echo "hash token = {$hashToken} \n";
        // if (\Hash::check($platformToken, $hashToken)) {
        //     echo "success \n";
        // } else {
        //     echo "fail \n";
        // }
        // $hashToken = \Crypt::encryptString($platformToken);

        switch ($platform) {
            case 'google':
                $user['google_id'] = $hashId;
                break;
            // more platform here
            default:
                throw new \Exception('platform not support', 400);
        }

        return $this->create($user);
    }

    function update_user($uuid, $name)
    {
        $user = [
            'name' => $name
        ];

        $updateUser = tap(\DB::table($this->getTable())->where('uuid', $uuid))
            ->update(array_filter($user, function ($value) {
                return $value !== null && (trim($value) !== ''); }))
            ->first();

        return $updateUser;
    }
}
