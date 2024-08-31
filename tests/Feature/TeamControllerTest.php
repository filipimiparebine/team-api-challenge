<?php

// /////////////////////////////////////////////////////////////////////////////
// TESTING AREA
// THIS IS AN AREA WHERE YOU CAN TEST YOUR WORK AND WRITE YOUR TESTS
// /////////////////////////////////////////////////////////////////////////////

namespace Tests\Feature;

use App\Enums\PlayerSkill;
use App\Enums\PlayerPosition;
use Illuminate\Http\Response;
use App\Rules\UniquePositionAndSkillRule;

class TeamControllerTest extends PlayerControllerBaseTest
{
    public function test_skill_based_team()
    {
        $player1Data = $this->playerData(PlayerPosition::MIDFIELDER, PlayerSkill::SPEED);
        $player2Data = $this->playerData(PlayerPosition::DEFENDER, PlayerSkill::STRENGTH, 100);
        $player3Data = $this->playerData(PlayerPosition::DEFENDER, PlayerSkill::STRENGTH, 10);
        $player4Data = $this->playerData(PlayerPosition::DEFENDER, PlayerSkill::STRENGTH, 90);

        $this->postJson(self::REQ_URI, $player1Data);
        $this->postJson(self::REQ_URI, $player2Data);
        $this->postJson(self::REQ_URI, $player3Data);
        $this->postJson(self::REQ_URI, $player4Data);

        $requirements = [
            [
                'position' => PlayerPosition::MIDFIELDER,
                'mainSkill' => PlayerSkill::SPEED,
                'numberOfPlayers' => 1
            ],
            [
                'position' => PlayerPosition::DEFENDER,
                'mainSkill' => PlayerSkill::STRENGTH,
                'numberOfPlayers' => 2
            ],
        ];


        $res = $this->postJson(self::REQ_TEAM_URI, $requirements);
        $body = collect(json_decode($res->getContent(), true));

        $this->assertNotEmpty($body);

        $midfielders = $body->filter(fn($player) => PlayerPosition::from($player['position']) == PlayerPosition::MIDFIELDER)->values();
        $defenders = $body->filter(fn($player) => PlayerPosition::from($player['position']) == PlayerPosition::DEFENDER)->values();

        $this->assertCount(1, $midfielders);
        $this->assertCount(2, $defenders);

        $this->assertEquals(PlayerSkill::STRENGTH, PlayerSkill::from($defenders[0]['skills'][0]['skill']));
        $this->assertEquals(100, $defenders[0]['skills'][0]['value']);

        $this->assertEquals(PlayerSkill::STRENGTH, PlayerSkill::from($defenders[1]['skills'][0]['skill']));
        $this->assertEquals(90, $defenders[1]['skills'][0]['value']);
    }

    public function test_same_position_and_skill()
    {
        $requirements = [
            [
                'position' => PlayerPosition::MIDFIELDER,
                'mainSkill' => PlayerSkill::SPEED,
                'numberOfPlayers' => 1
            ],
            [
                'position' => PlayerPosition::MIDFIELDER,
                'mainSkill' => PlayerSkill::SPEED,
                'numberOfPlayers' => 2
            ],
        ];


        $res = $this->postJson(self::REQ_TEAM_URI, $requirements);
        $body = json_decode($res->getContent());

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $res->getStatusCode());
        $this->assertStringContainsString(UniquePositionAndSkillRule::MESSAGE, $body->message);
    }

    public function test_same_position_different_skill()
    {
        $player1Data = $this->playerData(PlayerPosition::MIDFIELDER, PlayerSkill::SPEED);
        $player2Data = $this->playerData(PlayerPosition::MIDFIELDER, PlayerSkill::SPEED);
        $player3Data = $this->playerData(PlayerPosition::MIDFIELDER, PlayerSkill::STAMINA);
        $player4Data = $this->playerData(PlayerPosition::MIDFIELDER, PlayerSkill::SPEED);

        $this->postJson(self::REQ_URI, $player1Data);
        $this->postJson(self::REQ_URI, $player2Data);
        $this->postJson(self::REQ_URI, $player3Data);
        $this->postJson(self::REQ_URI, $player4Data);

        $requirements = [
            [
                'position' => PlayerPosition::MIDFIELDER,
                'mainSkill' => PlayerSkill::SPEED,
                'numberOfPlayers' => 1
            ],
            [
                'position' => PlayerPosition::MIDFIELDER,
                'mainSkill' => PlayerSkill::STAMINA,
                'numberOfPlayers' => 2
            ],
        ];


        $res = $this->postJson(self::REQ_TEAM_URI, $requirements);
        $body = json_decode($res->getContent());

        $this->assertEquals(Response::HTTP_OK, $res->getStatusCode());
        $this->assertCount(3, $body);
    }

    public function test_top_player()
    {
        $player1Data = $this->playerData(PlayerPosition::MIDFIELDER, PlayerSkill::SPEED);
        $player2Data = $this->playerData(PlayerPosition::MIDFIELDER, PlayerSkill::SPEED);
        $player4Data = $this->playerData(PlayerPosition::DEFENDER, PlayerSkill::SPEED, 90);
        $player5Data = $this->playerData(PlayerPosition::DEFENDER, PlayerSkill::STRENGTH, 20);
        $player6Data = $this->playerData(PlayerPosition::DEFENDER, PlayerSkill::STAMINA, 95);

        $player1 = json_decode($this->postJson(self::REQ_URI, $player1Data)->getContent());
        $player2 = json_decode($this->postJson(self::REQ_URI, $player2Data)->getContent());
        $this->postJson(self::REQ_URI, $player4Data);
        $this->postJson(self::REQ_URI, $player5Data);
        $this->postJson(self::REQ_URI, $player6Data);

        $requirements = [
            [
                'position' => PlayerPosition::MIDFIELDER,
                'mainSkill' => PlayerSkill::SPEED,
                'numberOfPlayers' => 1
            ],
            [
                'position' => PlayerPosition::DEFENDER,
                'mainSkill' => PlayerSkill::DEFENSE,
                'numberOfPlayers' => 1
            ],
        ];

        $res = $this->postJson(self::REQ_TEAM_URI, $requirements);
        $body = collect(json_decode($res->getContent(), true));

        $this->assertEquals(Response::HTTP_OK, $res->getStatusCode());
        $this->assertCount(2, $body);

        $midfielders = $body->filter(fn($player) => PlayerPosition::from($player['position']) == PlayerPosition::MIDFIELDER)->values();
        $defenders = $body->filter(fn($player) => PlayerPosition::from($player['position']) == PlayerPosition::DEFENDER)->values();

        $this->assertCount(1, $midfielders);
        $this->assertCount(1, $defenders);

        $player = $player1->skills[0]->value > $player2->skills[0]->value ? $player1 : $player2;
        $this->assertEquals(PlayerSkill::SPEED, PlayerSkill::from($midfielders[0]['skills'][0]['skill']));
        $this->assertEquals($player->skills[0]->value, $midfielders[0]['skills'][0]['value']);

        $this->assertEquals(PlayerSkill::STAMINA, PlayerSkill::from($defenders[0]['skills'][0]['skill']));
        $this->assertEquals(95, $defenders[0]['skills'][0]['value']);
    }

    public function test_top_player_multiple_skills()
    {
        $player1Data = $this->playerData(PlayerPosition::MIDFIELDER, PlayerSkill::SPEED);
        $player2Data = $this->playerData(PlayerPosition::MIDFIELDER, PlayerSkill::SPEED);
        $player4Data = $this->playerData(PlayerPosition::DEFENDER, [[PlayerSkill::STAMINA, 90], [PlayerSkill::SPEED, 100], [PlayerSkill::STRENGTH, 20]]);
        $player5Data = $this->playerData(PlayerPosition::DEFENDER, [[PlayerSkill::STAMINA, 80], [PlayerSkill::SPEED, 80], [PlayerSkill::STRENGTH, 80]]);
        $player6Data = $this->playerData(PlayerPosition::DEFENDER, [[PlayerSkill::STAMINA, 95], [PlayerSkill::SPEED, 90], [PlayerSkill::STRENGTH, 50]]);

        $player1 = json_decode($this->postJson(self::REQ_URI, $player1Data)->getContent());
        $player2 = json_decode($this->postJson(self::REQ_URI, $player2Data)->getContent());
        $this->postJson(self::REQ_URI, $player4Data);
        $this->postJson(self::REQ_URI, $player5Data);
        $this->postJson(self::REQ_URI, $player6Data);

        $requirements = [
            [
                'position' => PlayerPosition::MIDFIELDER,
                'mainSkill' => PlayerSkill::SPEED,
                'numberOfPlayers' => 1
            ],
            [
                'position' => PlayerPosition::DEFENDER,
                'mainSkill' => PlayerSkill::DEFENSE,
                'numberOfPlayers' => 1
            ],
        ];

        $res = $this->postJson(self::REQ_TEAM_URI, $requirements);
        $body = collect(json_decode($res->getContent(), true));

        $this->assertEquals(Response::HTTP_OK, $res->getStatusCode());
        $this->assertCount(2, $body);

        $midfielders = $body->filter(fn($player) => PlayerPosition::from($player['position']) == PlayerPosition::MIDFIELDER)->values();
        $defenders = $body->filter(fn($player) => PlayerPosition::from($player['position']) == PlayerPosition::DEFENDER)->values();

        $this->assertCount(1, $midfielders);
        $this->assertCount(1, $defenders);

        $player = $player1->skills[0]->value > $player2->skills[0]->value ? $player1 : $player2;
        $this->assertEquals(PlayerSkill::SPEED, PlayerSkill::from($midfielders[0]['skills'][0]['skill']));
        $this->assertEquals($player->skills[0]->value, $midfielders[0]['skills'][0]['value']);

        $this->assertEquals(PlayerSkill::SPEED, PlayerSkill::from($defenders[0]['skills'][1]['skill']));
        $this->assertEquals(100, $defenders[0]['skills'][1]['value']);
    }
}
