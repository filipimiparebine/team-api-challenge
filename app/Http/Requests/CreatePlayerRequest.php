<?php

namespace App\Http\Requests;

use App\Enums\PlayerSkill;
use App\Enums\PlayerPosition;
use Illuminate\Validation\Rules\Enum;

class CreatePlayerRequest extends ValidateRequest
{
    public function rules()
    {
        return [
            'name' => 'required',
            'position' => ['required', 'string', new Enum(PlayerPosition::class)],
            'skills.*.skill' => ['required', 'string', new Enum(PlayerSkill::class)],
            'skills.*.value' => 'required|integer',
        ];
    }
}
