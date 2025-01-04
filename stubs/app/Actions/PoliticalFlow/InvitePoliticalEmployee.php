<?php

namespace App\Actions\PoliticalFlow;

use App\Models\Political;
use App\Models\User;
use Closure;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Joinapi\PoliticalFlow\Contracts\InvitesPoliticalEmployees;
use Joinapi\PoliticalFlow\Events\InvitingPoliticalEmployee;
use Joinapi\PoliticalFlow\PoliticalFlow;
use Joinapi\PoliticalFlow\Mail\PoliticalInvitation;
use Joinapi\PoliticalFlow\Rules\Role;

class InvitePoliticalEmployee implements InvitesPoliticalEmployees
{
    /**
     * Invite a new political employee to the given political.
     *
     * @throws AuthorizationException
     */
    public function invite(User $user, Political $political, string $email, ?string $role = null): void
    {
        Gate::forUser($user)->authorize('addPoliticalEmployee', $political);

        $this->validate($political, $email, $role);

        InvitingPoliticalEmployee::dispatch($political, $email, $role);

        $invitation = $political->politicalInvitations()->create([
            'email' => $email,
            'role' => $role,
        ]);

        Mail::to($email)->send(new PoliticalInvitation($invitation));
    }

    /**
     * Validate the invite employee operation.
     */
    protected function validate(Political $political, string $email, ?string $role): void
    {
        Validator::make([
            'email' => $email,
            'role' => $role,
        ], $this->rules($political), [
            'email.unique' => __('political-flow::default.errors.employee_already_invited'),
        ])->after(
            $this->ensureUserIsNotAlreadyOnPolitical($political, $email)
        )->validateWithBag('addPoliticalEmployee');
    }

    /**
     * Get the validation rules for inviting a political employee.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    protected function rules(Political $political): array
    {
        return array_filter([
            'email' => [
                'required', 'email',
                Rule::unique('political_invitations')->where(static function (Builder $query) use ($political) {
                    $query->where('political_id', $political->id);
                }),
            ],
            'role' => PoliticalFlow::hasRoles()
                            ? ['required', 'string', new Role]
                            : null,
        ]);
    }

    /**
     * Ensure that the employee is not already on the political.
     */
    protected function ensureUserIsNotAlreadyOnPolitical(Political $political, string $email): Closure
    {
        return static function ($validator) use ($political, $email) {
            $validator->errors()->addIf(
                $political->hasUserWithEmail($email),
                'email',
                __('political-flow::default.errors.employee_already_belongs_to_political')
            );
        };
    }
}
