<?php

namespace App\Policies;

use App\Models\Political;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PoliticalPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Political $political): bool
    {
        return $user->belongsToPolitical($political);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Political $political): bool
    {
        return $user->ownsPolitical($political);
    }

    /**
     * Determine whether the user can add political employees.
     */
    public function addPoliticalEmployee(User $user, Political $political): bool
    {
        return $user->ownsPolitical($political);
    }

    /**
     * Determine whether the user can update political employee permissions.
     */
    public function updatePoliticalEmployee(User $user, Political $political): bool
    {
        return $user->ownsPolitical($political);
    }

    /**
     * Determine whether the user can remove political employees.
     */
    public function removePoliticalEmployee(User $user, Political $political): bool
    {
        return $user->ownsPolitical($political);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Political $political): bool
    {
        return $user->ownsPolitical($political);
    }
}
