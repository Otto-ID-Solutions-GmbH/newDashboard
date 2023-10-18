<?php

namespace Cintas\Events;

use Cintas\Models\Actions\OutScanAction;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OutScanRegistered
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $outScanAction;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(OutScanAction $outScanAction)
    {
        //
        $this->outScanAction = $outScanAction;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
