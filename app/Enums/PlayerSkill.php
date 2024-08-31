<?php

namespace App\Enums;

use App\Traits\EnumTrait;

enum PlayerSkill: string
{
    use EnumTrait;

    case DEFENSE = 'defense';
    case ATTACK = 'attack';
    case SPEED = 'speed';
    case STRENGTH = 'strength';
    case STAMINA = 'stamina';
}
