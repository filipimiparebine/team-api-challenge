<?php

namespace App\Http\Controllers;

use App\Traits\ResponseTrait;
use App\Repositories\TeamRepository;
use App\Http\Requests\TeamProcessRequest;

class TeamController extends Controller
{
    use ResponseTrait;

    public function __construct(
        protected TeamRepository $team
    ) {}

    public function process(TeamProcessRequest $request)
    {
        return $this->sendResponse(fn() => $this->team->process($request));
    }
}
