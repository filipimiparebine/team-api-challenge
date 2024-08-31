<?php

namespace Database\Seeders;

use App\Models\PlayerSkill;
use Illuminate\Database\Seeder;
use App\Models\Player;


class PlayerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Player::factory()
            ->has(PlayerSkill::factory(), 'skills')
            ->create();
    }
}
