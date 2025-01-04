<?php

namespace Joinapi\PoliticalFlow\Http\Livewire;

use Filament\Notifications\Notification;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Component;
use Joinapi\PoliticalFlow\Contracts\UpdatesPoliticalNames;
use Joinapi\PoliticalFlow\PoliticalFlow;

class UpdatePoliticalNameForm extends Component
{
    /**
     * The political instance.
     */
    public mixed $political;

    /**
     * The component's state.
     */
    public array $state = [];

    /**
     * Mount the component.
     */
    public function mount(mixed $political): void
    {
        $this->political = $political;

        $this->state = $political->withoutRelations()->toArray();
    }

    /**
     * Update the political's name.
     */
    public function updatePoliticalName(UpdatesPoliticalNames $updater): void
    {
        $this->resetErrorBag();

        $updater->update($this->user, $this->political, $this->state);

        if (PoliticalFlow::hasNotificationsFeature()) {
            if (method_exists($updater, 'politicalNameUpdated')) {
                $updater->politicalNameUpdated($this->user, $this->political, $this->state);
            } else {
                $this->politicalNameUpdated($this->political);
            }
        }
    }

    protected function politicalNameUpdated($political): void
    {
        $name = $political->name;

        Notification::make()
            ->title(__('political-flow::default.notifications.political_name_updated.title'))
            ->success()
            ->body(Str::inlineMarkdown(__('political-flow::default.notifications.political_name_updated.body', compact('name'))))
            ->send();
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
        return view('political-flow::political.update-political-name-form');
    }
}
