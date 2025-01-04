<?php

namespace Joinapi\PoliticalFlow\Http\Livewire;

use Filament\Notifications\Notification;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Joinapi\PoliticalFlow\Contracts\CreatesPolitical;
use Joinapi\PoliticalFlow\RedirectsActions;
use Livewire\Component;

class CreatePoliticalForm extends Component
{
    use RedirectsActions;

    /**
     * The component's state.
     */
    public array $state = [];

    /**
     * Create a new political.
     */
    public function createPolitical(CreatesPolitical $creator): Response | Redirector | RedirectResponse
    {
        $this->resetErrorBag();

        $creator->create($this->user, $this->state);

        $name = $this->state['name'];

        $this->politicalCreated($name);

        return $this->redirectPath($creator);
    }

    /**
     * Get the current user of the application.
     */
    public function getUserProperty(): ?Authenticatable
    {
        return Auth::user();
    }

    /**
     * Render the component.
     */
    public function render(): View
    {
        return view('political-flow::political.create-political-form');
    }

    public function politicalCreated($name): void
    {
        Notification::make()
            ->title(__('political-flow::default.notifications.political_created.title'))
            ->success()
            ->body(Str::inlineMarkdown(__('political-flow::default.notifications.political_created.body', compact('name'))))
            ->send();
    }
}
