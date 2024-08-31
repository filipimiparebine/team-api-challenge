<?php

// /////////////////////////////////////////////////////////////////////////////
// TESTING AREA
// THIS IS AN AREA WHERE YOU CAN TEST YOUR WORK AND WRITE YOUR TESTS
// /////////////////////////////////////////////////////////////////////////////

namespace Tests\Feature;

use App\Enums\Message;

class PlayerControllerDeleteTest extends PlayerControllerBaseTest
{

    public function test_delete_player()
    {
        $this->postJson(self::REQ_URI, [
            "name" => "test",
            "position" => "defender"
        ]);

        $res = $this->delete(self::REQ_URI . '1', [], [
            'Authorization' => 'Bearer ' . self::TOKEN
        ]);
        $body = json_decode($res->getContent());

        $this->assertNotEmpty($body);
        $this->assertEquals(Message::PLAYER_DELETED, Message::from($body->message));
    }
}
