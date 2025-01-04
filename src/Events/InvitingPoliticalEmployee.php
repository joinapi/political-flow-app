<?php

namespace Joinapi\PoliticalFlow\Events;

use Illuminate\Foundation\Events\Dispatchable;

class InvitingPoliticalEmployee
{
    use Dispatchable;

    /**
     * The political instance.
     */
    public mixed $political;

    /**
     * The email address of the invitee.
     */
    public string $email;

    /**
     * The role of the invitee.
     */
    public ?string $role = null;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(mixed $political, string $email, ?string $role = null)
    {
        $this->political = $political;
        $this->email = $email;
        $this->role = $role;
    }
}
