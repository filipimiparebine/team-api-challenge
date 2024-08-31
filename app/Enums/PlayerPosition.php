<?php

namespace App\Enums;

use App\Traits\EnumTrait;

enum PlayerPosition: string
{
    use EnumTrait;

    case DEFENDER = 'defender';
    case MIDFIELDER = 'midfielder';
    case FORWARD = 'forward';
}
