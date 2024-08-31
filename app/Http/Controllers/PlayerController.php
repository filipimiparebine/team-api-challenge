<?php

// /////////////////////////////////////////////////////////////////////////////
// PLEASE DO NOT RENAME OR REMOVE ANY OF THE CODE BELOW.
// YOU CAN ADD YOUR CODE TO THIS FILE TO EXTEND THE FEATURES TO USE THEM IN YOUR WORK.
// /////////////////////////////////////////////////////////////////////////////

namespace App\Http\Controllers;

use App\Traits\ResponseTrait;
use App\Repositories\PlayerRepository;
use App\Http\Requests\CreatePlayerRequest;
use App\Http\Requests\UpdatePlayerRequest;

class PlayerController extends Controller
{
    use ResponseTrait;

    public function __construct(
        protected PlayerRepository $player
    ) {}

    public function index()
    {
        return $this->sendResponse(fn() => $this->player->getAll());
    }

    public function show(int $id)
    {
        return $this->sendResponse(fn() => $this->player->get($id));
    }

    public function store(CreatePlayerRequest $request)
    {
        return $this->sendResponse(fn() => $this->player->create($request));
    }

    public function update(UpdatePlayerRequest $request, int $id)
    {
        return $this->sendResponse(fn() => $this->player->update($request, $id));
    }

    public function destroy(int $id)
    {
        return $this->sendResponse(fn() => $this->player->delete($id));
    }
}
