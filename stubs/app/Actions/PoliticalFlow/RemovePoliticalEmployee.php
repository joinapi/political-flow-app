<?php

namespace App\Actions\PoliticalFlow;

use App\Models\Political;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use Joinapi\PoliticalFlow\Contracts\RemovesPoliticalEmployees;
use Joinapi\PoliticalFlow\Events\PoliticalEmployeeRemoved;

class RemovePoliticalEmployee implements RemovesPoliticalEmployees
{
    /**
     * Remove the political employee from the given political.
     *
     * @throws AuthorizationException
     */
    public function remove(User $user, Political $political, User $politicalEmployee): void
    {
        $this->authorize($user, $political, $politicalEmployee);

        $this->ensureUserDoesNotOwnPolitical($politicalEmployee, $political);

        $political->removeUser($politicalEmployee);

        PoliticalEmployeeRemoved::dispatch($political, $politicalEmployee);
    }

    /**
     * Authorize that the user can remove the political employee.
     *
     * @throws AuthorizationException
     */
    protected function authorize(User $user, Political $political, User $politicalEmployee): void
    {
        if (! Gate::forUser($user)->check('removePoliticalEmployee', $political) &&
            $user->id !== $politicalEmployee->id) {
            throw new AuthorizationException;
        }
    }

    /**
     * Ensure that the currently authenticated user does not own the political.
     */
    protected function ensureUserDoesNotOwnPolitical(User $politicalEmployee, Political $political): void
    {
        if ($politicalEmployee->id === $political->owner->id) {
            throw ValidationException::withMessages([
                'political' => [__('political-flow::default.errors.cannot_leave_political')],
            ])->errorBag('removePoliticalEmployee');
        }
    }
}
