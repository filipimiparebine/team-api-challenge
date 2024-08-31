<?php

namespace App\Http\Requests;

use App\Enums\PlayerSkill;
use App\Enums\PlayerPosition;
use App\Rules\UniquePositionAndSkillRule;
use Illuminate\Validation\Rules\Enum;

class TeamProcessRequest extends ValidateRequest
{
    public function rules()
    {
        return [
            '*.position' => ['required', 'string', new Enum(PlayerPosition::class)],
            '*.mainSkill' => ['required', 'string', new Enum(PlayerSkill::class)],
            '*.numberOfPlayers' => 'required|integer|min:1',
            '*' => [new UniquePositionAndSkillRule],
        ];
    }
}
