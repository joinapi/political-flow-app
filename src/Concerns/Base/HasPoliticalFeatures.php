<?php

namespace Joinapi\PoliticalFlow\Concerns\Base;

use Closure;
use Joinapi\PoliticalFlow\HasPolitical;

trait HasPoliticalFeatures
{
    /**
     * The event listener to register.
     */
    protected static bool $switchesCurrentPolitical = false;

    /**
     * Determine if the political is supporting political features.
     */
    public static bool $hasPoliticalFeatures = false;

    /**
     * Determine if invitations are sent to political employees.
     */
    public static bool $sendsPoliticalInvitations = false;

    /**
     * Determine if the application supports switching current political.
     */
    public function switchCurrentPolitical(bool $condition = true): static
    {
        static::$switchesCurrentPolitical = $condition;

        return $this;
    }

    /**
     * Determine if the political is supporting political features.
     */
    public function political(bool | Closure | null $condition = true, bool $invitations = false): static
    {
        static::$hasPoliticalFeatures = $condition instanceof Closure ? $condition() : $condition;
        static::$sendsPoliticalInvitations = $invitations;

        return $this;
    }

    /**
     * Determine if the application switches the current political.
     */
    public static function switchesCurrentPolitical(): bool
    {
        return static::$switchesCurrentPolitical;
    }

    /**
     * Determine if Political is supporting political features.
     */
    public static function hasPoliticalFeatures(): bool
    {
        return static::$hasPoliticalFeatures;
    }

    /**
     * Determine if invitations are sent to political employees.
     */
    public static function sendsPoliticalInvitations(): bool
    {
        return static::hasPoliticalFeatures() && static::$sendsPoliticalInvitations;
    }

    /**
     * Determine if a given user model utilizes the "HasPoliticals" trait.
     */
    public static function userHasPoliticalFeatures(mixed $user): bool
    {
        return (array_key_exists(HasPolitical::class, class_uses_recursive($user)) ||
                method_exists($user, 'currentPolitical')) &&
            static::hasPoliticalFeatures();
    }
}
