<?php

namespace Joinapi\PoliticalFlow\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

abstract class PoliticalEvent
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * The political instance.
     */
    public mixed $political;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(mixed $political)
    {
        $this->political = $political;
    }
}
