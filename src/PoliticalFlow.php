<?php

namespace Joinapi\PoliticalFlow;

use Filament\Contracts\Plugin;
use Filament\Events\TenantSet;
use Filament\Panel;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Event;
use Joinapi\PoliticalFlow\Contracts\CreatesConnectedAccounts;
use Joinapi\PoliticalFlow\Contracts\CreatesUserFromProvider;
use Joinapi\PoliticalFlow\Contracts\HandlesInvalidState;
use Joinapi\PoliticalFlow\Contracts\UpdatesConnectedAccounts;
use Joinapi\PoliticalFlow\Http\Controllers\OAuthController;
use Joinapi\PoliticalFlow\Listeners\SwitchCurrentPolitical;
use Joinapi\PoliticalFlow\Pages\Political\CreatePolitical;
use Joinapi\PoliticalFlow\Pages\Political\PoliticalSettings;
use Livewire\Livewire;

class PoliticalFlow implements Plugin
{
    use Concerns\Base\HasAddedProfileComponents;
    use Concerns\Base\HasBaseActionBindings;
    use Concerns\Base\HasBaseModels;
    use Concerns\Base\HasBaseProfileComponents;
    use Concerns\Base\HasBaseProfileFeatures;
    use Concerns\Base\HasModals;
    use Concerns\Base\HasNotifications;
    use Concerns\Base\HasPanels;
    use Concerns\Base\HasPermissions;
    use Concerns\Base\HasPoliticalFeatures;
    use Concerns\Base\HasRoutes;
    use Concerns\Base\HasTermsAndPrivacyPolicy;
    use Concerns\ManagesProfileComponents;
    use Concerns\Socialite\CanEnableSocialite;
    use Concerns\Socialite\HasConnectedAccountModel;
    use Concerns\Socialite\HasProviderFeatures;
    use Concerns\Socialite\HasProviders;
    use Concerns\Socialite\HasSocialiteActionBindings;
    use Concerns\Socialite\HasSocialiteComponents;
    use Concerns\Socialite\HasSocialiteProfileFeatures;

    public function getId(): string
    {
        return 'political';
    }

    public static function make(): static
    {
        return app(static::class);
    }

    public function register(Panel $panel): void
    {
        if (static::hasPoliticalFeatures()) {
            Livewire::component('filament.pages.political.create_political', CreatePolitical::class);
            Livewire::component('filament.pages.political.political_settings', PoliticalSettings::class);
        }

        if (static::hasSocialiteFeatures()) {
            app()->bind(OAuthController::class, static function (Application $app) {
                return new OAuthController(
                    $app->make(CreatesUserFromProvider::class),
                    $app->make(CreatesConnectedAccounts::class),
                    $app->make(UpdatesConnectedAccounts::class),
                    $app->make(HandlesInvalidState::class),
                );
            });
        }

        if (static::$registersRoutes) {
            $panel->routes(fn () => $this->registerPublicRoutes());
            $panel->authenticatedRoutes(fn () => $this->registerAuthenticatedRoutes());
        }
    }

    public function boot(Panel $panel): void
    {
        if (static::switchesCurrentPolitical()) {
            Event::listen(TenantSet::class, SwitchCurrentPolitical::class);
        }
    }
}
