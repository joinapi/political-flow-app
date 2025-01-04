<?php

namespace App\Actions\PoliticalFlow;

use App\Models\Political;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Joinapi\PoliticalFlow\Contracts\DeletesPolitical;
use Joinapi\PoliticalFlow\Contracts\DeletesUsers;
use Laravel\Sanctum\PersonalAccessToken;

class DeleteUser implements DeletesUsers
{
    /**
     * Create a new action instance.
     */
    public function __construct(protected DeletesPolitical $deletesPolitical)
    {
        //
    }

    /**
     * Delete the given user.
     */
    public function delete(User $user): void
    {
        DB::transaction(function () use ($user) {
            $this->deletePolitical($user);
            $user->deleteProfilePhoto();
            $user->tokens->each(static fn (PersonalAccessToken $token) => $token->delete());
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
            $this->deletesPolitical->delete($political);
        });
    }
}
