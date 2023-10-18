<?php

namespace Tests\Feature\API;

use Cintas\Models\Facility\Facility;
use Tests\TestCase;

class GetCustomersTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testGetCustomersForFacility()
    {
        $start = microtime(true);
        $facilityCuid = Facility::all()->first()->cuid;
        $response = $this->get("api/facilities/$facilityCuid/customers");
        $time = microtime(true) - $start;

        $response->assertStatus(200);

        $response->assertJsonStructure([
            "data" => [
                ["cuid", "name", "label"]
            ]
        ]);
    }
}
