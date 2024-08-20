<?php
namespace App\Util;
use Carbon\Carbon;

class DateTimeFormatter {

    public function toMillieTimestamp($timestamp) {
        // $originalDateString = '2024-08-18T11:21:42Z';

        // 创建 DateTime 对象，并设置时区为 UTC
        $dateTime = new \DateTime($timestamp, new \DateTimeZone('UTC'));
        
        // 设置微秒部分
        // $dateTime->format('Y-m-d\TH:i:s.u\Z');
        
        // 将 DateTime 对象格式化为带有微秒的字符串
        return Carbon::parse($dateTime->format('Y-m-d\TH:i:s.u\Z'));
    }

}
