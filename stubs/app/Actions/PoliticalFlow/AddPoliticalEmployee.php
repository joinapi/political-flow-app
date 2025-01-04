<?php

namespace App\Actions\PoliticalFlow;

use App\Models\Political;
use App\Models\User;
use Closure;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Joinapi\PoliticalFlow\Contracts\AddsPoliticalEmployees;
use Joinapi\PoliticalFlow\Events\AddingPoliticalEmployee;
use Joinapi\PoliticalFlow\Events\PoliticalEmployeeAdded;
use Joinapi\PoliticalFlow\PoliticalFlow;
use Joinapi\PoliticalFlow\Rules\Role;

class AddPoliticalEmployee implements AddsPoliticalEmployees
{
    /**
     * Add a new political employee to the given political.
     *
     * @throws AuthorizationException
     */
    public function add(User $user, Political $political, string $email, ?string $role = null): void
    {
        Gate::forUser($user)->authorize('addPoliticalEmployee', $political);

        $this->validate($political, $email, $role);

        $newPoliticalEmployee = PoliticalFlow::findUserByEmailOrFail($email);

        AddingPoliticalEmployee::dispatch($political, $newPoliticalEmployee);

        $political->users()->attach(
            $newPoliticalEmployee,
            ['role' => $role]
        );

        PoliticalEmployeeAdded::dispatch($political, $newPoliticalEmployee);
    }

    /**
     * Validate the add employee operation.
     */
    protected function validate(Political $political, string $email, ?string $role): void
    {
        Validator::make([
            'email' => $email,
            'role' => $role,
        ], $this->rules(), [
            'email.exists' => __('political-flow::default.errors.email_not_found'),
        ])->after(
            $this->ensureUserIsNotAlreadyOnPolitical($political, $email)
        )->validateWithBag('addPoliticalEmployee');
    }

    /**
     * Get the validation rules for adding a political employee.
     *
     * @return array<string, Rule|array|string>
     */
    protected function rules(): array
    {
        return array_filter([
            'email' => ['required', 'email', 'exists:users'],
            'role' => PoliticalFlow::hasRoles()
                            ? ['required', 'string', new Role]
                            : null,
        ]);
    }

    /**
     * Ensure that the user is not already on the political.
     */
    protected function ensureUserIsNotAlreadyOnPolitical(Political $political, string $email): Closure
    {
        return static function ($validator) use ($political, $email) {
            $validator->errors()->addIf(
                $political->hasUserWithEmail($email),
                'email',
                __('political-flow::default.errors.user_belongs_to_political')
            );
        };
    }
}
