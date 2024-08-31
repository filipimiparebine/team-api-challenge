<?php

namespace App\Repositories;

use Exception;
use App\Models\Player;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

class TeamRepository
{
    public function process(Request $request)
    {
        $requirements = $request->all();
        $selectedPlayers = collect();

        foreach($requirements as $requirement) {
            $playersAdded = 0;
            $skilledPlayers = $this->getSkillBasedPlayers($requirement);

            if($skilledPlayers->isNotEmpty()) {
                $skilledPlayers->map(function($player) use (&$selectedPlayers, &$playersAdded){
                    if ($selectedPlayers->has($player->id)) return;
                    $selectedPlayers->put($player->id, $player);
                    $playersAdded++;
                });
            }

            if($playersAdded != $requirement['numberOfPlayers']) {
                $except = $selectedPlayers->keys()->all();
                $limit = $requirement['numberOfPlayers'] - $playersAdded;
                $topPlayers = $this->getTopPlayers($requirement, $except, $limit);
                $topPlayers->map(function($player) use (&$selectedPlayers, &$playersAdded){
                    $selectedPlayers->put($player->id, $player);
                    $playersAdded++;
                });
            }

            if($selectedPlayers->isEmpty() || $playersAdded != $requirement['numberOfPlayers']) {
                throw new Exception("Insufficient number of players for position: {$requirement['position']}");
            }

        }
        return $selectedPlayers->values();
    }

    private function getSkillBasedPlayers(array $requirement)
    {
        return Player::select('players.*')
            ->wherePosition($requirement['position'])
            ->join('player_skills', 'players.id', '=', 'player_skills.player_id')
            ->where('player_skills.skill', $requirement['mainSkill'])
            ->orderBy('player_skills.value', 'desc')
            ->take($requirement['numberOfPlayers'])
            ->get();
    }

    private function getTopPlayers(array $requirement, array $except, int $limit)
    {
        return Player::select('players.*')
            ->wherePosition($requirement['position'])
            ->whereNotIn('players.id', $except)
            ->join('player_skills', 'players.id', '=', 'player_skills.player_id')
            ->orderBy('player_skills.value', 'desc')
            ->take($limit)
            ->get();
    }
}
