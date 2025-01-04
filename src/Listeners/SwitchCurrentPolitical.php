<?php

namespace Joinapi\PoliticalFlow\Listeners;

use Filament\Events\TenantSet;
use Joinapi\PoliticalFlow\PoliticalFlow;
use Joinapi\PoliticalFlow\HasPolitical;

class SwitchCurrentPolitical
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(TenantSet $event): void
    {
        $tenant = $event->getTenant();

        /** @var HasPolitical $user */
        $user = $event->getUser();

        if (PoliticalFlow::switchesCurrentPolitical() === false || ! in_array(HasPolitical::class, class_uses_recursive($user), true)) {
            return;
        }

        if (! $user->switchPolitical($tenant)) {
            $user->switchPolitical($user->personalPolitical());
        }
    }
}
