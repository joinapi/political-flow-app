<?php

namespace Joinapi\PoliticalFlow\Http\Livewire;

use Filament\Notifications\Notification;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Component;
use Joinapi\PoliticalFlow\Actions\UpdatePoliticalEmployeeRole;
use Joinapi\PoliticalFlow\Contracts\AddsPoliticalEmployees;
use Joinapi\PoliticalFlow\Contracts\InvitesPoliticalEmployees;
use Joinapi\PoliticalFlow\Contracts\RemovesPoliticalEmployees;
use Joinapi\PoliticalFlow\PoliticalFlow;
use Joinapi\PoliticalFlow\RedirectsActions;
use Joinapi\PoliticalFlow\Role;

class PoliticalEmployeeManager extends Component
{
    use RedirectsActions;

    /**
     * The political instance.
     */
    public mixed $political;

    /**
     * The user that is having their role managed.
     */
    public mixed $managingRoleFor;

    /**
     * The current role for the user that is having their role managed.
     */
    public string $currentRole;

    /**
     * The ID of the political employee being removed.
     */
    public ?int $politicalEmployeeIdBeingRemoved = null;

    /**
     * The "add political employee" form state.
     *
     * @var array<string, mixed>
     */
    public $addPoliticalEmployeeForm = [
        'email' => '',
        'role' => null,
    ];

    /**
     * Mount the component.
     */
    public function mount(mixed $political): void
    {
        $this->political = $political;
    }

    /**
     * Add a new political employee to a political.
     */
    public function addPoliticalEmployee(InvitesPoliticalEmployees $inviter, AddsPoliticalEmployees $adder): void
    {
        $this->resetErrorBag();

        if (PoliticalFlow::sendsPoliticalInvitations()) {
            $inviter->invite(
                $this->user,
                $this->political,
                $this->addPoliticalEmployeeForm['email'],
                $this->addPoliticalEmployeeForm['role']
            );
        } else {
            $adder->add(
                $this->user,
                $this->political,
                $this->addPoliticalEmployeeForm['email'],
                $this->addPoliticalEmployeeForm['role']
            );
        }

        if (PoliticalFlow::hasNotificationsFeature()) {
            if (method_exists($inviter, 'employeeInvitationSent')) {
                $inviter->employeeInvitationSent(
                    $this->user,
                    $this->political,
                    $this->addPoliticalEmployeeForm['email'],
                    $this->addPoliticalEmployeeForm['role']
                );
            } else {
                $email = $this->addPoliticalEmployeeForm['email'];
                $this->employeeInvitationSent($email);
            }
        }

        $this->addPoliticalEmployeeForm = [
            'email' => '',
            'role' => null,
        ];

        $this->political = $this->political->fresh();
    }

    /**
     * Cancel a pending political employee invitation.
     */
    public function cancelPoliticalInvitation(int $invitationId): void
    {
        if (! empty($invitationId)) {
            $model = PoliticalFlow::politicalInvitationModel();

            $model::whereKey($invitationId)->delete();
        }

        $this->political = $this->political->fresh();
    }

    /**
     * Allow the given user's role to be managed.
     */
    public function manageRole(int $userId): void
    {
        $this->dispatch('open-modal', id: 'currentlyManagingRole');
        $this->managingRoleFor = PoliticalFlow::findUserByIdOrFail($userId);
        $this->currentRole = $this->managingRoleFor->politicalRole($this->political)->key;
    }

    /**
     * Save the role for the user being managed.
     *
     * @throws AuthorizationException
     */
    public function updateRole(UpdatePoliticalEmployeeRole $updater): void
    {
        $updater->update(
            $this->user,
            $this->political,
            $this->managingRoleFor->id,
            $this->currentRole
        );

        $this->political = $this->political->fresh();

        $this->dispatch('close-modal', id: 'currentlyManagingRole');
    }

    /**
     * Stop managing the role of a given user.
     */
    public function stopManagingRole(): void
    {
        $this->dispatch('close-modal', id: 'currentlyManagingRole');
    }

    /**
     * Confirm that the currently authenticated user should leave the political.
     */
    public function confirmLeavingPolitical(): void
    {
        $this->dispatch('open-modal', id: 'confirmingLeavingPolitical');
    }

    /**
     * Remove the currently authenticated user from the political.
     */
    public function leavePolitical(RemovesPoliticalEmployees $remover): Response | Redirector | RedirectResponse
    {
        $remover->remove(
            $this->user,
            $this->political,
            $this->user
        );

        $this->dispatch('close-modal', id: 'confirmingLeavingPolitical');

        $this->political = $this->political->fresh();

        return $this->redirectPath($remover);
    }

    /**
     * Cancel leaving the political.
     */
    public function cancelLeavingPolitical(): void
    {
        $this->dispatch('close-modal', id: 'confirmingLeavingPolitical');
    }

    /**
     * Confirm that the given political employee should be removed.
     */
    public function confirmPoliticalEmployeeRemoval(int $userId): void
    {
        $this->dispatch('open-modal', id: 'confirmingPoliticalEmployeeRemoval');
        $this->politicalEmployeeIdBeingRemoved = $userId;
    }

    /**
     * Remove a political employee from the political.
     */
    public function removePoliticalEmployee(RemovesPoliticalEmployees $remover): void
    {
        $remover->remove(
            $this->user,
            $this->political,
            $user = PoliticalFlow::findUserByIdOrFail($this->politicalEmployeeIdBeingRemoved)
        );

        $this->dispatch('close-modal', id: 'confirmingPoliticalEmployeeRemoval');

        $this->politicalEmployeeIdBeingRemoved = null;

        $this->political = $this->political->fresh();
    }

    /**
     * Cancel the removal of a political employee.
     */
    public function cancelPoliticalEmployeeRemoval(): void
    {
        $this->dispatch('close-modal', id: 'confirmingPoliticalEmployeeRemoval');
    }

    /**
     * Get the current user of the application.
     */
    public function getUserProperty(): ?Authenticatable
    {
        return Auth::user();
    }

    /**
     * Get the available political employee roles.
     */
    public function getRolesProperty(): array
    {
        return collect(PoliticalFlow::$roles)->transform(static function ($role) {
            return with($role->jsonSerialize(), static function ($data) {
                return (new Role(
                    $data['key'],
                    $data['name'],
                    $data['permissions']
                ))->description($data['description']);
            });
        })->values()->all();
    }

    /**
     * Render the component.
     */
    public function render(): View
    {
        return view('political-flow::politicals.political-employee-manager');
    }

    public function employeeInvitationSent($email): void
    {
        Notification::make()
            ->title(__('political-flow::default.notifications.political_invitation_sent.title'))
            ->success()
            ->body(Str::inlineMarkdown(__('political-flow::default.notifications.political_invitation_sent.body', compact('email'))))
            ->send();
    }
}
