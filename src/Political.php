<?php

namespace Joinapi\PoliticalFlow;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

abstract class Political extends Model
{
    /**
     * Get the owner of the political.
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(PoliticalFlow::userModel(), 'user_id');
    }

    /**
     * Get all the political's users including its owner.
     */
    public function allUsers(): Collection
    {
        return $this->users->merge([$this->owner]);
    }

    /**
     * Get all the users that belong to the political.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(PoliticalFlow::userModel(), PoliticalFlow::employeeshipModel())
            ->withPivot('role')
            ->withTimestamps()
            ->as('employeeship');
    }

    /**
     * Determine if the given user belongs to the political.
     */
    public function hasUser(mixed $user): bool
    {
        return $this->users->contains($user) || $user->ownsPolitical($this);
    }

    /**
     * Determine if the given email address belongs to a user on the political.
     */
    public function hasUserWithEmail(string $email): bool
    {
        return $this->allUsers()->contains(static function ($user) use ($email) {
            return $user->email === $email;
        });
    }

    /**
     * Determine if the given user has the given permission on the political.
     */
    public function userHasPermission(mixed $user, string $permission): bool
    {
        return $user->hasPoliticalPermission($this, $permission);
    }

    /**
     * Get all the pending user invitations for the political.
     */
    public function politicalInvitations(): HasMany
    {
        return $this->hasMany(PoliticalFlow::politicalInvitationModel());
    }

    /**
     * Remove the given user from the political.
     */
    public function removeUser(mixed $user): void
    {
        if ($user->current_political_id === $this->id) {
            $user->forceFill([
                'current_political_id' => null,
            ])->save();
        }

        $this->users()->detach($user);
    }

    /**
     * Purge all the political's resources.
     */
    public function purge(): void
    {
        $this->owner()->where('current_political_id', $this->id)
            ->update(['current_political_id' => null]);

        $this->users()->where('current_political_id', $this->id)
            ->update(['current_political_id' => null]);

        $this->users()->detach();

        $this->delete();
    }
}
