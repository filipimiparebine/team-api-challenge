<?php

// /////////////////////////////////////////////////////////////////////////////
// TESTING AREA
// THIS IS AN AREA WHERE YOU CAN TEST YOUR WORK AND WRITE YOUR TESTS
// /////////////////////////////////////////////////////////////////////////////

namespace Tests\Feature;


class PlayerControllerUpdateTest extends PlayerControllerBaseTest
{
    public function test_update()
    {
        $createPlayer = [
            "name" => "test",
            "position" => "defender"
        ];
        $res = $this->postJson(self::REQ_URI, $createPlayer);
        $createdPlayer = json_decode($res->getContent());

        $updatePlayer = [
            "name" => "test2",
            "position" => "midfielder",
            "skills" => [
                0 => [
                    "skill" => "attack",
                    "value" => 60
                ],
                1 => [
                    "skill" => "speed",
                    "value" => 80
                ]
            ]
        ];

        $res = $this->putJson(self::REQ_URI . $createdPlayer->id, $updatePlayer);
        $updatedPlayer = json_decode($res->getContent());

        $this->assertNotEmpty($updatedPlayer);
        $this->assertEquals($updatePlayer['name'], $updatedPlayer->name);
        $this->assertEquals($updatePlayer['position'], $updatedPlayer->position);
        $this->assertCount(2, $updatedPlayer->skills);
    }
}
