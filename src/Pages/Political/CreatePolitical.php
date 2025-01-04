<?php

namespace Joinapi\PoliticalFlow\Pages\Political;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Tenancy\RegisterTenant as FilamentRegisterTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Joinapi\PoliticalFlow\Events\AddingPolitical;
use Joinapi\PoliticalFlow\PoliticalFlow;

class CreatePolitical extends FilamentRegisterTenant
{
    protected static string $view = 'political-flow::filament.pages.political.create_political';

    public static function getLabel(): string
    {
        return __('political-flow::default.pages.titles.create_political');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label(__('political-flow::default.labels.political_name'))
                    ->autofocus()
                    ->maxLength(255)
                    ->required(),
            ])
            ->model(PoliticalFlow::politicalModel())
            ->statePath('data');
    }

    protected function handleRegistration(array $data): Model
    {
        $user = Auth::user();

        Gate::forUser($user)->authorize('create', PoliticalFlow::newPoliticalModel());

        AddingPolitical::dispatch($user);

        $personalPolitical = $user?->personalPolitical() === null;

        $political = $user?->ownedPoliticals()->create([
            'name' => $data['name'],
            'personal_portal' => $personalPolitical,
        ]);

        $user?->switchPolitical($political);

        $name = $data['name'];

        $this->politicalCreated($name);

        return $political;
    }

    protected function politicalCreated($name): void
    {
        Notification::make()
            ->title(__('political-flow::default.notifications.political_created.title'))
            ->success()
            ->body(Str::inlineMarkdown(__('political-flow::default.notifications.political_created.body', compact('name'))))
            ->send();
    }
}
