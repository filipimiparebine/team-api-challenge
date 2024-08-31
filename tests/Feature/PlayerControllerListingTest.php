<?php

// /////////////////////////////////////////////////////////////////////////////
// TESTING AREA
// THIS IS AN AREA WHERE YOU CAN TEST YOUR WORK AND WRITE YOUR TESTS
// /////////////////////////////////////////////////////////////////////////////

namespace Tests\Feature;

class PlayerControllerListingTest extends PlayerControllerBaseTest
{
    public function test_list_players()
    {
        $this->postJson(self::REQ_URI, [
            "name" => "test",
            "position" => "defender"
        ]);
        $this->postJson(self::REQ_URI, [
            "name" => "test",
            "position" => "defender"
        ]);
        $res = $this->get(self::REQ_URI);
        $body = json_decode($res->getContent());

        $this->assertNotEmpty($body);
        $this->assertCount(2, $body);
    }
}
