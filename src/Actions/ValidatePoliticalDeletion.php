<?php

namespace Joinapi\PoliticalFlow\Actions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;

class ValidatePoliticalDeletion
{
    /**
     * Validate that the political can be deleted by the given user.
     *
     * @throws AuthorizationException
     */
    public function validate(mixed $user, mixed $political): void
    {
        Gate::forUser($user)->authorize('delete', $political);

        if ($political->personal_portal) {
            throw ValidationException::withMessages([
                'political' => __('political-flows::default.errors.political_deletion'),
            ])->errorBag('deletePolitical');
        }
    }
}
