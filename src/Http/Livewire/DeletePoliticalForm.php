<?php

namespace Joinapi\PoliticalFlow\Http\Livewire;

use Filament\Notifications\Notification;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Joinapi\PoliticalFlow\Actions\ValidatePoliticalDeletion;
use Joinapi\PoliticalFlow\Contracts\DeletesPolitical;
use Joinapi\PoliticalFlow\PoliticalFlow;
use Joinapi\PoliticalFlow\RedirectsActions;
use Livewire\Component;

class DeletePoliticalForm extends Component
{
    use RedirectsActions;

    /**
     * The political instance.
     */
    public mixed $political;

    /**
     * Mount the component.
     */
    public function mount(mixed $political): void
    {
        $this->political = $political;
    }

    /**
     * Delete the political.
     *
     * @throws AuthorizationException
     */
    public function deletePolitical(ValidatePoliticalDeletion $validator, DeletesPolitical $deleter): Response | Redirector | RedirectResponse
    {
        $validator->validate(Auth::user(), $this->political);

        $deleter->delete($this->political);

        if (PoliticalFlow::hasNotificationsFeature()) {
            if (method_exists($deleter, 'politicalDeleted')) {
                $deleter->politicalDeleted($this->political);
            } else {
                $this->politicalDeleted($this->political);
            }
        }

        $this->political = null;

        return $this->redirectPath($deleter);
    }

    /**
     * Cancel the political deletion.
     */
    public function cancelPoliticalDeletion(): void
    {
        $this->dispatch('close-modal', id: 'confirmingPoliticalDeletion');
    }

    /**
     * Render the component.
     */
    public function render(): View
    {
        return view('political-flow::political.delete-political-form');
    }

    public function politicalDeleted($political): void
    {
        $name = $political->name;

        Notification::make()
            ->title(__('political-flow::default.notifications.political_deleted.title'))
            ->success()
            ->body(Str::inlineMarkdown(__('political-flow::default.notifications.political_deleted.body', compact('name'))))
            ->send();
    }
}
