<?php

namespace App\Actions\PoliticalFlow;

use App\Models\Political;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Joinapi\PoliticalFlow\Contracts\DeletesPolitical;
use Joinapi\PoliticalFlow\Contracts\DeletesUsers;

class DeleteUser implements DeletesUsers
{
    /**
     * The political deleter implementation.
     */
    protected DeletesPolitical $deletesPoliticals;

    /**
     * Create a new action instance.
     */
    public function __construct(DeletesPolitical $deletesPoliticals)
    {
        $this->deletesPoliticals = $deletesPoliticals;
    }

    /**
     * Delete the given user.
     */
    public function delete(User $user): void
    {
        DB::transaction(function () use ($user) {
            $this->deletePolitical($user);
            $user->deleteProfilePhoto();
            $user->connectedAccounts->each(static fn ($account) => $account->delete());
            $user->tokens->each(static fn ($token) => $token->delete());
            $user->delete();
        });
    }

    /**
     * Delete the companies and political associations attached to the user.
     */
    protected function deletePolitical(User $user): void
    {
        $user->politicals()->detach();

        $user->ownedPoliticals->each(function (Political $political) {
            $this->deletesPoliticals->delete($political);
        });
    }
}
