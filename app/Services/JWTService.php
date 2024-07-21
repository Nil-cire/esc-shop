<?php
namespace App\Services;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JWTService
{
    protected $secret;

    public function __construct()
    {
        $this->secret = env('JWT_SECRET');
    }

    public function encode($uid, string $platform)
    {

        $now = new \DateTimeImmutable();
        // $nowTime = $now->getTimestamp();
        $expiredAt = $now->modify('+14 days');
        // $expiredAtTime = $expiredAt->getTimestamp();
        $alg = 'HS256';

        $payload = [
            'iat' => $now->getTimestamp(),
            'iss' => "esc_$platform",
            'exp' => $expiredAt->getTimestamp(),
            'uid' => $uid
        ];

        $jwtHeader = [
            "alg" => "HS256",
            "typ" => "JWT"
        ];

        $jwt = JWT::encode($payload, $this->secret, $alg);

        return "Bearer $jwt";
    }

    public function decode($fullToken)
    {
        $token = str_replace("Bearer ","", $fullToken);

        $alg = 'HS256';
        // $secretKey = env('JWT_SECRET');
        $payload = JWT::decode($token, new Key($this->secret, $alg));
        return $payload;
    }

    public function verify($token, $userUuid) {
        $payload = $this->decode($token);
        $expire = $payload->exp;
        $now = (new \DateTimeImmutable())->getTimestamp();
        if ($expire < $now) {
            return false;
        }

        $user_uuid = $payload->uid;
        return $user_uuid == $userUuid;
    }

    public function verify_expired($token, $userUuid) {
        $payload = $this->decode($token);

        // check user valid
        $user_uuid = $payload->uid;
        if ($user_uuid != $userUuid) {
            return false;
        } else {
            return true;
        }

        // $expire = $payload->exp;
        // $now = (new \DateTimeImmutable())->getTimestamp();
        // if ($expire < $now) {
        //     return true;
        // }

        // return $user_uuid == $userUuid;
    }

    public function uuidv4()
    {
        $data = random_bytes(16);

        $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}
