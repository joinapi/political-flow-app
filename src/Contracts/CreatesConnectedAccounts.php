<?php

namespace Joinapi\PoliticalFlow\Contracts;

use Illuminate\Contracts\Auth\Authenticatable;
use Laravel\Socialite\Contracts\User as ProviderUser;
use Joinapi\PoliticalFlow\ConnectedAccount;

interface CreatesConnectedAccounts
{
    /**
     * Create a connected account for a given user.
     */
    public function create(Authenticatable $user, string $provider, ProviderUser $providerUser): ConnectedAccount;
}
