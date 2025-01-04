<?php

namespace Joinapi\PoliticalFlow\Events;

use Illuminate\Foundation\Events\Dispatchable;

class AddingPolitical
{
    use Dispatchable;

    /**
     * The political owner.
     */
    public mixed $owner;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(mixed $owner)
    {
        $this->owner = $owner;
    }
}
