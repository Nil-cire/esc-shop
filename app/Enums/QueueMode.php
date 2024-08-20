<?php

namespace App\Enums;

enum QueueMode: string
{
    case Normal = 'normal';
    case OneShot = 'one_shot'
}
