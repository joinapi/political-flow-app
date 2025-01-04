<?php

namespace App\Actions\PoliticalFlow;

use App\Models\Political;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Joinapi\PoliticalFlow\Contracts\UpdatesPoliticalNames;

class UpdatePoliticalName implements UpdatesPoliticalNames
{
    /**
     * Validate and update the given political's name.
     *
     * @param  array<string, string>  $input
     *
     * @throws AuthorizationException
     */
    public function update(User $user, Political $political, array $input): void
    {
        Gate::forUser($user)->authorize('update', $political);

        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
        ])->validateWithBag('updatePoliticalName');

        $political->forceFill([
            'name' => $input['name'],
        ])->save();
    }
}
