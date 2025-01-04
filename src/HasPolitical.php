<?php

namespace Joinapi\PoliticalFlow;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;

trait HasPolitical
{
    /**
     * Determine if the given political is the current political.
     */
    public function isCurrentPolitical(mixed $political): bool
    {
        return $political->id === $this->currentPolitical->id;
    }

    /**
     * Get the current political of the user's political-flow.
     */
    public function currentPolitical(): BelongsTo
    {
        if ($this->current_political_id === null && $this->id) {
            $this->switchPolitical($this->personalPolitical());
        }

        return $this->belongsTo(PoliticalFlow::politicalModel(), 'current_political_id');
    }

    /**
     * Switch the user's political-flow to the given political.
     */
    public function switchPolitical(mixed $political): bool
    {
        if (! $this->belongsToPolitical($political)) {
            return false;
        }

        $this->forceFill([
            'current_political_id' => $political->id,
        ])->save();

        $this->setRelation('currentPolitical', $political);

        return true;
    }

    /**
     * Get all the politicals the user owns or belongs to.
     */
    public function allPoliticals(): Collection
    {
        return $this->ownedPoliticals->merge($this->politicals)->sortBy('name');
    }

    /**
     * Get all the politicals the user owns.
     */
    public function ownedPoliticals(): HasMany
    {
        return $this->hasMany(PoliticalFlow::politicalModel());
    }

    /**
     * Get all the politicals the user belongs to.
     */
    public function politicals(): BelongsToMany
    {
        return $this->belongsToMany(PoliticalFlow::politicalModel(), PoliticalFlow::employeeshipModel())
            ->withPivot('role')
            ->withTimestamps()
            ->as('employeeship');
    }

    /**
     * Get the user's "personal" political.
     */
    public function personalPolitical(): mixed
    {
        return $this->ownedPoliticals->where('personal_political', true)->first();
    }

    /**
     * Determine if the user owns the given political.
     */
    public function ownsPolitical(mixed $political): bool
    {
        if ($political === null) {
            return false;
        }

        return $this->id === $political->{$this->getForeignKey()};
    }

    /**
     * Determine if the user belongs to the given political.
     */
    public function belongsToPolitical(mixed $political): bool
    {
        if ($political === null) {
            return false;
        }

        return $this->ownsPolitical($political) || $this->politicals()->contains(static function ($t) use ($political) {
            return $t->id === $political->id;
        });
    }

    /**
     * Get the role that the user has on the political.
     */
    public function politicalRole(mixed $political): ?Role
    {
        if ($this->ownsPolitical($political)) {
            return new OwnerRole;
        }

        if (! $this->belongsToPolitical($political)) {
            return null;
        }

        $role = $political->users
            ->where('id', $this->id)
            ->first()
            ->employeeship
            ->role;

        return $role ? PoliticalFlow::findRole($role) : null;
    }

    /**
     * Determine if the user has the given role on the given political.
     */
    public function hasPoliticalRole(mixed $political, string $role): bool
    {
        if ($this->ownsPolitical($political)) {
            return true;
        }

        return $this->belongsToPolitical($political) && PoliticalFlow::findRole($political->users->where(
            'id',
            $this->id
        )->first()->employeeship->role)?->key === $role;
    }

    /**
     * Get the user's permissions for the given political.
     */
    public function politicalPermissions(mixed $political): array
    {
        if ($this->ownsPolitical($political)) {
            return ['*'];
        }

        if (! $this->belongsToPolitical($political)) {
            return [];
        }

        return (array) $this->politicalRole($political)?->permissions;
    }

    /**
     * Determine if the user has the given permission on the given political.
     */
    public function hasPoliticalPermission(mixed $political, string $permission): bool
    {
        if ($this->ownsPolitical($political)) {
            return true;
        }

        if (! $this->belongsToPolitical($political)) {
            return false;
        }

        if ($this->currentAccessToken() !== null &&
            ! $this->tokenCan($permission) &&
            in_array(HasApiTokens::class, class_uses_recursive($this), true)) {
            return false;
        }

        $permissions = $this->politicalPermissions($political);

        return in_array($permission, $permissions, true) ||
            in_array('*', $permissions, true) ||
            (Str::endsWith($permission, ':create') && in_array('*:create', $permissions, true)) ||
            (Str::endsWith($permission, ':update') && in_array('*:update', $permissions, true));
    }
}
