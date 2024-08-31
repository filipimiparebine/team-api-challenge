<?php


// /////////////////////////////////////////////////////////////////////////////
// TESTING AREA
// THIS IS AN AREA WHERE YOU CAN TEST YOUR WORK AND WRITE YOUR TESTS
// /////////////////////////////////////////////////////////////////////////////

namespace Tests\Feature;


class PlayerControllerCreateTest extends PlayerControllerBaseTest
{
    public function test_create_player()
    {
        $data = [
            "name" => "test",
            "position" => "defender",
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

        $res = $this->postJson(self::REQ_URI, $data);
        $body = json_decode($res->getContent());

        $this->assertNotEmpty($body);
        $this->assertEquals($data['name'], $body->name);
        $this->assertEquals($data['position'], $body->position);
        $this->assertCount(2, $body->skills);
    }
}
