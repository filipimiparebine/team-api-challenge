<?php

namespace App\Repositories;

use App\Enums\Message;
use Exception;
use App\Models\Player;
use Illuminate\Http\Request;
use Log;

class PlayerRepository
{
    public function create(Request $request): Player
    {
        $player = Player::create($request->only(['name', 'position']));

        if($request->skills) {
            foreach ($request->skills as $skill) {
                $player->skills()->create($skill);
            }
        }

        return $player->load('skills');
    }

    public function getAll()
    {
        return Player::get();
    }

    public function get(int $id)
    {
        return Player::findOrFail($id);
    }

    public function update(Request $request, int $id): Player
    {
        $player = Player::findOrFail($id);

        $player->update($request->only(['name', 'position']));
        $player->skills()->delete();

        foreach ($request->skills as $skill) {
            $player->skills()->create($skill);
        }

        return $player->load('skills');
    }

    public function delete(int $id)
    {
        $player = Player::findOrFail($id);
        $player->delete();

        return Message::PLAYER_DELETED;
    }
}
