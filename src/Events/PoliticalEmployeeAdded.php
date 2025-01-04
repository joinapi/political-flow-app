<?php

namespace Joinapi\PoliticalFlow\Events;

use Illuminate\Foundation\Events\Dispatchable;

class PoliticalEmployeeAdded
{
    use Dispatchable;

    /**
     * The political instance.
     */
    public mixed $political;

    /**
     * The political employee that was added.
     */
    public mixed $user;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(mixed $political, mixed $user)
    {
        $this->political = $political;
        $this->user = $user;
    }
}
