<?php

namespace Tests\Unit\PDF;

use Cintas\Events\OutScanRegistered;
use Cintas\Models\Actions\OutScanAction;
use Cintas\Repositories\FacilityRepository;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class OutscanLabelTest extends TestCase
{

    /**
     * @var FacilityRepository
     */
    private $repository;


    protected function setUp()
    {
        parent::setUp();

        $this->repository = $this->app->make(FacilityRepository::class);
    }

    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testPdfLabelGeneration()
    {
        //TODO: Add generation of new outscan to test
        $model = OutScanAction::query()->orderBy('updated_at')->first();
        event(new OutScanRegistered($model));

        $this->assertTrue(true);
    }
}
