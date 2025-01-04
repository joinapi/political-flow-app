<?php

namespace Joinapi\PoliticalFlow\Concerns\Base;

use Joinapi\PoliticalFlow\Contracts\AddsPoliticalEmployees;
use Joinapi\PoliticalFlow\Contracts\CreatesPolitical;
use Joinapi\PoliticalFlow\Contracts\CreatesNewUsers;
use Joinapi\PoliticalFlow\Contracts\DeletesPolitical;
use Joinapi\PoliticalFlow\Contracts\DeletesUsers;
use Joinapi\PoliticalFlow\Contracts\InvitesPoliticalEmployees;
use Joinapi\PoliticalFlow\Contracts\RemovesPoliticalEmployees;
use Joinapi\PoliticalFlow\Contracts\UpdatesPoliticalNames;
use Joinapi\PoliticalFlow\Contracts\UpdatesUserPasswords;
use Joinapi\PoliticalFlow\Contracts\UpdatesUserProfileInformation;

trait HasBaseActionBindings
{
    /**
     * Register a class / callback that should be used to create new users.
     */
    public static function createUsersUsing(string $class): void
    {
        app()->singleton(CreatesNewUsers::class, $class);
    }

    /**
     * Register a class / callback that should be used to update user profile information.
     */
    public static function updateUserProfileInformationUsing(string $class): void
    {
        app()->singleton(UpdatesUserProfileInformation::class, $class);
    }

    /**
     * Register a class / callback that should be used to update user passwords.
     */
    public static function updateUserPasswordsUsing(string $class): void
    {
        app()->singleton(UpdatesUserPasswords::class, $class);
    }

    /**
     * Register a class / callback that should be used to create politicals.
     */
    public static function createPoliticalUsing(string $class): void
    {
        app()->singleton(CreatesPolitical::class, $class);
    }

    /**
     * Register a class / callback that should be used to update political names.
     */
    public static function updatePoliticalNamesUsing(string $class): void
    {
        app()->singleton(UpdatesPoliticalNames::class, $class);
    }

    /**
     * Register a class / callback that should be used to add political employees.
     */
    public static function addPoliticalEmployeesUsing(string $class): void
    {
        app()->singleton(AddsPoliticalEmployees::class, $class);
    }

    /**
     * Register a class / callback that should be used to add political employees.
     */
    public static function invitePoliticalEmployeesUsing(string $class): void
    {
        app()->singleton(InvitesPoliticalEmployees::class, $class);
    }

    /**
     * Register a class / callback that should be used to remove political employees.
     */
    public static function removePoliticalEmployeesUsing(string $class): void
    {
        app()->singleton(RemovesPoliticalEmployees::class, $class);
    }

    /**
     * Register a class / callback that should be used to delete politicals.
     */
    public static function deletePoliticalUsing(string $class): void
    {
        app()->singleton(DeletesPolitical::class, $class);
    }

    /**
     * Register a class / callback that should be used to delete users.
     */
    public static function deleteUsersUsing(string $class): void
    {
        app()->singleton(DeletesUsers::class, $class);
    }
}
