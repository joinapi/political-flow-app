<?php

namespace App\Actions\PoliticalFlow;

use App\Models\Political;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Joinapi\PoliticalFlow\Contracts\CreatesConnectedAccounts;
use Joinapi\PoliticalFlow\Contracts\CreatesUserFromProvider;
use Joinapi\PoliticalFlow\Enums\Feature;
use Joinapi\PoliticalFlow\PoliticalFlow;
use Laravel\Socialite\Contracts\User as ProviderUserContract;

class CreateUserFromProvider implements CreatesUserFromProvider
{
    /**
     * The creates connected accounts instance.
     */
    public CreatesConnectedAccounts $createsConnectedAccounts;

    /**
     * Create a new action instance.
     */
    public function __construct(CreatesConnectedAccounts $createsConnectedAccounts)
    {
        $this->createsConnectedAccounts = $createsConnectedAccounts;
    }

    /**
     * Create a new user from a social provider user.
     */
    public function create(string $provider, ProviderUserContract $providerUser): User
    {
        return DB::transaction(function () use ($providerUser, $provider) {
            return tap(User::create([
                'name' => $providerUser->getName(),
                'email' => $providerUser->getEmail(),
            ]), function (User $user) use ($providerUser, $provider) {
                $user->markEmailAsVerified();

                if ($this->shouldSetProfilePhoto($providerUser)) {
                    $user->setProfilePhotoFromUrl($providerUser->getAvatar());
                }

                $user->switchConnectedAccount(
                    $this->createsConnectedAccounts->create($user, $provider, $providerUser)
                );

                $this->createPolitical($user);
            });
        });
    }

    private function shouldSetProfilePhoto(ProviderUserContract $providerUser): bool
    {
        return Feature::ProviderAvatars->isEnabled() &&
            PoliticalFlow::managesProfilePhotos() &&
            $providerUser->getAvatar();
    }

    /**
     * Create a personal political for the user.
     */
    protected function createPolitical(User $user): void
    {
        $user->ownedPoliticals()->save(Political::forceCreate([
            'user_id' => $user->id,
            'name' => explode(' ', $user->name, 2)[0] . "'s Political",
            'personal_political' => true,
        ]));
    }
}
