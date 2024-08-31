<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;


class UniquePositionAndSkillRule implements Rule
{

    const MESSAGE = 'Each position and skill combination must be unique.';

    public function passes($attribute, $value)
    {
        $combinations = collect(request()->all())
            ->map(fn($item) => $item['position'] . '-' . $item['mainSkill']);

        return $combinations->unique()->count() === $combinations->count();
    }

    public function message()
    {
        return self::MESSAGE;
    }
}
