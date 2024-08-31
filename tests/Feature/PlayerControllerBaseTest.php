<?php

// /////////////////////////////////////////////////////////////////////////////
// TESTING AREA
// THIS IS AN AREA WHERE YOU CAN TEST YOUR WORK AND WRITE YOUR TESTS
// /////////////////////////////////////////////////////////////////////////////

namespace Tests\Feature;

use Tests\TestCase;
use App\Enums\PlayerSkill;
use App\Enums\PlayerPosition;
use Illuminate\Foundation\Testing\RefreshDatabase;

abstract class PlayerControllerBaseTest extends TestCase
{
    use RefreshDatabase;

    final const REQ_URI = '/api/player/';
    final const REQ_TEAM_URI = '/api/team/process';
    final const TOKEN = 'SkFabTZibXE1aE14ckpQUUxHc2dnQ2RzdlFRTTM2NFE2cGI4d3RQNjZmdEFITmdBQkE=';


    protected function log($data){
        fwrite(STDERR, print_r($data, TRUE));
    }

    protected function playerData(
        PlayerPosition $position = null,
        PlayerSkill|array $skills = null,
        int $singleSkillValue = null
    ){

        return [
            "name" => fake()->name,
            "position" => $position->value ?? fake()->randomElement(PlayerPosition::cases()),
            "skills" => $this->getSkills($skills, $singleSkillValue)
        ];
    }

    private function getSkills(PlayerSkill|array $skills, int $singleSkillValue = null)
    {
        if($skills instanceof PlayerSkill) {
            return [
                [
                    "skill" => $skills->value ?? fake()->randomElement(PlayerSkill::cases()),
                    "value" => $singleSkillValue ?? fake()->numberBetween(0, 100)
                ]
            ];
        }

        if(is_array($skills)) {
            $playerSkills = [];
            foreach($skills as $skill) {
                $skillName = is_array($skill) ? $skill[0]->value : $skill->value ?? fake()->randomElement(PlayerSkill::cases());
                $skillValue = is_array($skill) && !empty($skill[1]) ? $skill[1] : fake()->numberBetween(0, 100);
                $playerSkills[] = [
                    "skill" => $skillName,
                    "value" => $skillValue
                ];
            }
            return $playerSkills;
        }
    }
}
