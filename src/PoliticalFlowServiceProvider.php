<?php

namespace Joinapi\PoliticalFlow;

use Illuminate\Support\ServiceProvider;
use Joinapi\PoliticalFlow\Http\Livewire\ConnectedAccountsForm;
use Joinapi\PoliticalFlow\Http\Livewire\DeletePoliticalForm;
use Joinapi\PoliticalFlow\Http\Livewire\DeleteUserForm;
use Joinapi\PoliticalFlow\Http\Livewire\LogoutOtherBrowserSessionsForm;
use Joinapi\PoliticalFlow\Http\Livewire\PoliticalEmployeeManager;
use Joinapi\PoliticalFlow\Http\Livewire\SetPasswordForm;
use Joinapi\PoliticalFlow\Http\Livewire\UpdatePasswordForm;
use Joinapi\PoliticalFlow\Http\Livewire\UpdatePoliticalNameForm;
use Joinapi\PoliticalFlow\Http\Livewire\UpdateProfileInformationForm;
use Livewire\Livewire;

class PoliticalFlowServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'political-flow');

        $this->loadTranslationsFrom(__DIR__ . '/../lang', 'political-flow');

        $this->configurePublishing();
        $this->configureCommands();

        $this->app->booted(function () {
            $this->configureComponents();
        });
    }

    /**
     * Configure the components offered by the application.
     */
    protected function configureComponents(): void
    {
        $featureComponentMap = [
            'update-profile-information-form' => [PoliticalFlow::canUpdateProfileInformation(), UpdateProfileInformationForm::class],
            'update-password-form' => [PoliticalFlow::canUpdatePasswords(), UpdatePasswordForm::class],
            'delete-user-form' => [PoliticalFlow::hasAccountDeletionFeatures(), DeleteUserForm::class],
            'logout-other-browser-sessions-form' => [PoliticalFlow::canManageBrowserSessions(), LogoutOtherBrowserSessionsForm::class],
            'update-political-name-form' => [PoliticalFlow::hasPoliticalFeatures(), UpdatePoliticalNameForm::class],
            'political-employee-manager' => [PoliticalFlow::hasPoliticalFeatures(), PoliticalEmployeeManager::class],
            'delete-political-form' => [PoliticalFlow::hasPoliticalFeatures(), DeletePoliticalForm::class],
            'set-password-form' => [PoliticalFlow::canSetPasswords(), SetPasswordForm::class],
            'connected-accounts-form' => [PoliticalFlow::canManageConnectedAccounts(), ConnectedAccountsForm::class],
        ];

        foreach ($featureComponentMap as $alias => [$enabled, $component]) {
            if ($enabled) {
                Livewire::component($alias, $component);
            }
        }
    }

    /**
     * Configure publishing for the package.
     */
    protected function configurePublishing(): void
    {
        if (! $this->app->runningInConsole()) {
            return;
        }

        $this->publishes([
            __DIR__ . '/../resources/views' => resource_path('views/vendor/political-flow'),
        ], 'political-flow-views');

        $this->publishes([
            __DIR__ . '/../lang' => lang_path('vendor/political-flow'),
        ], 'political-flow-translations');

        $this->publishes([
            __DIR__ . '/../database/migrations/0001_01_01_000000_create_users_table.php' => database_path('migrations/0001_01_01_000000_create_users_table.php'),
        ], 'political-flow-migrations');

        $this->publishesMigrations([
            __DIR__ . '/../database/migrations/2020_05_21_100000_create_political_table.php' => database_path('migrations/2020_05_21_100000_create_politicals_table.php'),
            __DIR__ . '/../database/migrations/2020_05_21_200000_create_political_user_table.php' => database_path('migrations/2020_05_21_200000_create_political_user_table.php'),
            __DIR__ . '/../database/migrations/2020_05_21_300000_create_political_invitations_table.php' => database_path('migrations/2020_05_21_300000_create_political_invitations_table.php'),
        ], 'political-flow-political-migrations');

        $this->publishesMigrations([
            __DIR__ . '/../database/migrations/2020_12_22_000000_create_connected_accounts_table.php' => database_path('migrations/2020_12_22_000000_create_connected_accounts_table.php'),
        ], 'political-flow-socialite-migrations');
    }

    /**
     * Configure the commands offered by the application.
     */
    protected function configureCommands(): void
    {
        if (! $this->app->runningInConsole()) {
            return;
        }

        $this->commands([
            Console\InstallCommand::class,
        ]);
    }
}
