<?php

namespace Joinapi\PoliticalFlow\Contracts;

use Illuminate\Contracts\Auth\Authenticatable;
use Laravel\Socialite\Contracts\User;
use Joinapi\PoliticalFlow\ConnectedAccount;

interface UpdatesConnectedAccounts
{
    /**
     * Update a given connected account.
     */
    public function update(Authenticatable $user, ConnectedAccount $connectedAccount, string $provider, User $providerUser): ConnectedAccount;
}
