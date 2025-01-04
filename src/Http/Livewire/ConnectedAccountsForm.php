<?php

namespace Joinapi\PoliticalFlow\Http\Livewire;

use Filament\Notifications\Notification;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Joinapi\PoliticalFlow\ConnectedAccount;
use Joinapi\PoliticalFlow\Enums\Feature;
use Joinapi\PoliticalFlow\Pages\User\Profile;
use Joinapi\PoliticalFlow\PoliticalFlow;
use Livewire\Component;
use Livewire\Features\SupportRedirects\Redirector;

class ConnectedAccountsForm extends Component
{
    /**
     * The ID of the currently connected account.
     */
    public string | int $selectedAccountId = '';

    /**
     * Return an array of the enabled Provider enum case values.
     *
     * @return string[]
     */
    public function getProvidersProperty(): array
    {
        return PoliticalFlow::enabledProviders();
    }

    /**
     * Get the current user of the application.
     */
    public function getUserProperty(): ?Authenticatable
    {
        return Auth::user();
    }

    /**
     * Confirm that the user actually wants to remove the selected connected account.
     */
    public function confirmRemove(string | int $accountId): void
    {
        $this->selectedAccountId = $accountId;

        $this->dispatch('open-modal', id: 'confirmingRemove');
    }

    /**
     * Set the providers avatar url as the users profile photo url.
     */
    public function setAvatarAsProfilePhoto(string | int $accountId): RedirectResponse | Redirector
    {
        $user = $this->user;

        $account = $this->user?->connectedAccounts
            ->where('user_id', $user?->getAuthIdentifier())
            ->where('id', $accountId)
            ->first();

        if (is_callable([$user, 'setProfilePhotoFromUrl']) && $account->avatar_path !== null && Feature::ProviderAvatars->isEnabled()) {
            $user->setProfilePhotoFromUrl($account->avatar_path);
        }

        return redirect(Profile::getUrl());
    }

    /**
     * Remove an OAuth Provider.
     */
    public function removeConnectedAccount(string | int $accountId): void
    {
        DB::table('connected_accounts')
            ->where('user_id', $this->user?->getAuthIdentifier())
            ->where('id', $accountId)
            ->delete();

        $this->connectedAccountRemoved();

        $this->dispatch('close-modal', id: 'confirmingRemove');
    }

    /**
     * Cancel the connected account removal.
     */
    public function cancelConnectedAccountRemoval(): void
    {
        $this->dispatch('close-modal', id: 'confirmingRemove');
    }

    /**
     * Get the users connected accounts.
     */
    public function getAccountsProperty(): Collection
    {
        if ($this->user?->connectedAccounts === null) {
            return collect();
        }

        return $this->user->connectedAccounts
            ->map(static function (ConnectedAccount $account) {
                return (object) $account->getSharedData();
            });
    }

    /**
     * Render the component.
     */
    public function render(): View
    {
        return view('political-flow::profile.connected-accounts-form');
    }

    /**
     * The connected account has been removed.
     */
    protected function connectedAccountRemoved(): void
    {
        Notification::make()
            ->title(__('political-flow::default.notifications.connected_account_removed.title'))
            ->success()
            ->body(__('political-flow::default.notifications.connected_account_removed.body'))
            ->send();
    }
}
