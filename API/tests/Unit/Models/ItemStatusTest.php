<?php

namespace Tests\Unit\Models;

use Carbon\Carbon;
use Cintas\Models\Items\Item;
use Tests\TestCase;

class ItemStatusTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testStatusAtTimestamp()
    {
        $item = Item::findOrFail('cjpl2rdpp002r90qy01029ikt');
        $timestamp = Carbon::now();
        $status = $item->status_history()->atTimestamp($timestamp)->first();

        $this->assertEquals($item->last_status->cuid, $status->cuid, 'Status at timestamp is not working correctly!');
    }
}
