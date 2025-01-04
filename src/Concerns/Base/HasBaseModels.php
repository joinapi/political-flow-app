<?php

namespace Joinapi\PoliticalFlow\Concerns\Base;

use App\Models\Political;
use App\Models\PoliticalInvitation;
use App\Models\Employeeship;
use App\Models\User;

trait HasBaseModels
{
    /**
     * The user model that should be used by Political.
     */
    public static string $userModel = User::class;

    /**
     * The political model that should be used by Political.
     */
    public static string $politicalModel = Political::class;

    /**
     * The employeeship model that should be used by Political.
     */
    public static string $employeeshipModel = Employeeship::class;

    /**
     * The political invitation model that should be used by Political.
     */
    public static string $politicalInvitationModel = PoliticalInvitation::class;

    /**
     * Get the name of the user model used by the application.
     */
    public static function userModel(): string
    {
        return static::$userModel;
    }

    /**
     * Get the name of the political model used by the application.
     */
    public static function politicalModel(): string
    {
        return static::$politicalModel;
    }

    /**
     * Get the name of the employeeship model used by the application.
     */
    public static function employeeshipModel(): string
    {
        return static::$employeeshipModel;
    }

    /**
     * Get the name of the political invitation model used by the application.
     */
    public static function politicalInvitationModel(): string
    {
        return static::$politicalInvitationModel;
    }

    /**
     * Get a new instance of the user model.
     */
    public static function newUserModel(): mixed
    {
        $model = static::userModel();

        return new $model;
    }

    /**
     * Get a new instance of the political model.
     */
    public static function newPoliticalModel(): mixed
    {
        $model = static::politicalModel();

        return new $model;
    }

    /**
     * Specify the user model that should be used by Political.
     */
    public static function useUserModel(string $model): static
    {
        static::$userModel = $model;

        return new static;
    }

    /**
     * Specify the political model that should be used by Political.
     */
    public static function usePoliticalModel(string $model): static
    {
        static::$politicalModel = $model;

        return new static;
    }

    /**
     * Specify the employeeship model that should be used by Political.
     */
    public static function useEmployeeshipModel(string $model): static
    {
        static::$employeeshipModel = $model;

        return new static;
    }

    /**
     * Specify the political invitation model that should be used by Political.
     */
    public static function usePoliticalInvitationModel(string $model): static
    {
        static::$politicalInvitationModel = $model;

        return new static;
    }

    /**
     * Find a user instance by the given ID.
     */
    public static function findUserByIdOrFail(int $id): mixed
    {
        return static::newUserModel()->where('id', $id)->firstOrFail();
    }

    /**
     * Find a user instance by the given email address or fail.
     */
    public static function findUserByEmailOrFail(string $email): mixed
    {
        return static::newUserModel()->where('email', $email)->firstOrFail();
    }
}
