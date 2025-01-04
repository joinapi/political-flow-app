<?php

namespace Joinapi\PoliticalFlow\Actions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Joinapi\PoliticalFlow\Events\PoliticalEmployeeUpdated;
use Joinapi\PoliticalFlow\PoliticalFlow;
use Joinapi\PoliticalFlow\Rules\Role;

class UpdatePoliticalEmployeeRole
{
    /**
     * Update the role for the given political employee.
     *
     * @throws AuthorizationException
     */
    public function update(mixed $user, mixed $political, int $politicalEmployeeId, string $role): void
    {
        Gate::forUser($user)->authorize('updatePoliticalEmployee', $political);

        Validator::make(compact('role'), [
            'role' => ['required', 'string', new Role],
        ])->validate();

        $political->users()->updateExistingPivot($politicalEmployeeId, compact('role'));

        PoliticalEmployeeUpdated::dispatch($political->fresh(), PoliticalFlow::findUserByIdOrFail($politicalEmployeeId));
    }
}
