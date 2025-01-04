<?php

namespace App\Actions\PoliticalFlow;

use Illuminate\Contracts\Auth\Authenticatable;
use Joinapi\PoliticalFlow\ConnectedAccount;
use Joinapi\PoliticalFlow\Contracts\CreatesConnectedAccounts;
use Joinapi\PoliticalFlow\PoliticalFlow;
use Laravel\Socialite\Contracts\User as ProviderUser;

class CreateConnectedAccount implements CreatesConnectedAccounts
{
    /**
     * Create a connected account for a given user.
     */
    public function create(Authenticatable $user, string $provider, ProviderUser $providerUser): ConnectedAccount
    {
        return PoliticalFlow::connectedAccountModel()::forceCreate([
            'user_id' => $user->getAuthIdentifier(),
            'provider' => strtolower($provider),
            'provider_id' => $providerUser->getId(),
            'name' => $providerUser->getName(),
            'nickname' => $providerUser->getNickname(),
            'email' => $providerUser->getEmail(),
            'avatar_path' => $providerUser->getAvatar(),
            'token' => $providerUser->token,
            'secret' => $providerUser->tokenSecret ?? null,
            'refresh_token' => $providerUser->refreshToken ?? null,
            'expires_at' => property_exists($providerUser, 'expiresIn') ? now()->addSeconds($providerUser->expiresIn) : null,
        ]);
    }
}
