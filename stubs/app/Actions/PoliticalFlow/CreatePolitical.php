<?php

namespace App\Actions\PoliticalFlow;

use App\Models\Political;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Joinapi\PoliticalFlow\Contracts\CreatesPolitical;
use Joinapi\PoliticalFlow\Events\AddingPolitical;
use Joinapi\PoliticalFlow\PoliticalFlow;

class CreatePolitical implements CreatesPolitical
{
    /**
     * Validate and create a new political for the given user.
     *
     * @param  array<string, string>  $input
     *
     * @throws AuthorizationException
     */
    public function create(User $user, array $input): Political
    {
        Gate::forUser($user)->authorize('create', PoliticalFlow::newPoliticalModel());

        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
        ])->validateWithBag('createPolitical');

        AddingPolitical::dispatch($user);

        $user->switchPolitical($political = $user->ownedPoliticals()->create([
            'name' => $input['name'],
            'personal_portal' => false,
        ]));

        return $political;
    }
}
