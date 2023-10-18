<?php

namespace Tests\Unit\API\Helpers;

use Cintas\Repositories\FacilityRepository;
use Tests\TestCase;

class RegisterOutgoingItemsTest extends TestCase
{

    private $facilityRepository;

    protected function setUp()
    {
        parent::setUp();
        $this->facilityRepository = $this->app->make(FacilityRepository::class);
    }


    /**
     * A basic test example.
     *
     * @return void
     */
    public function testIncomingItems()
    {
        //TODO: Implement unit test
    }
}
