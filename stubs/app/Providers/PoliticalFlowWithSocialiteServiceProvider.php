<?php

namespace App\Providers;

use App\Actions\PoliticalFlow\AddPoliticalEmployee;
use App\Actions\PoliticalFlow\CreateConnectedAccount;
use App\Actions\PoliticalFlow\CreateNewUser;
use App\Actions\PoliticalFlow\CreateUserFromProvider;
use App\Actions\PoliticalFlow\DeletePolitical;
use App\Actions\PoliticalFlow\DeleteUser;
use App\Actions\PoliticalFlow\HandleInvalidState;
use App\Actions\PoliticalFlow\InvitePoliticalEmployee;
use App\Actions\PoliticalFlow\RemovePoliticalEmployee;
use App\Actions\PoliticalFlow\ResolveSocialiteUser;
use App\Actions\PoliticalFlow\SetUserPassword;
use App\Actions\PoliticalFlow\UpdateConnectedAccount;
use App\Actions\PoliticalFlow\UpdatePoliticalName;
use App\Actions\PoliticalFlow\UpdateUserPassword;
use App\Actions\PoliticalFlow\UpdateUserProfileInformation;
use App\Models\Political;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Joinapi\PoliticalFlow\Actions\GenerateRedirectForProvider;
use Joinapi\PoliticalFlow\Enums\Feature;
use Joinapi\PoliticalFlow\Enums\Provider;
use Joinapi\PoliticalFlow\Pages\Auth\Login;
use Joinapi\PoliticalFlow\Pages\Auth\Register;
use Joinapi\PoliticalFlow\Pages\Political\CreatePolitical;
use Joinapi\PoliticalFlow\Pages\Political\PoliticalSettings;
use Joinapi\PoliticalFlow\Pages\User\Profile;
use Joinapi\PoliticalFlow\PoliticalFlow;

class PoliticalFlowWithSocialiteServiceProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('political')
            ->path('political')
            ->default()
            ->login(Login::class)
            ->passwordReset()
            ->homeUrl(static fn (): string => url(Pages\Dashboard::getUrl(panel: 'political', tenant: Auth::user()?->personalPolitical())))
            ->plugin(
                PoliticalFlow::make()
                    ->userPanel('admin')
                    ->switchCurrentPolitical()
                    ->updateProfileInformation()
                    ->updatePasswords()
                    ->setPasswords()
                    ->connectedAccounts()
                    ->manageBrowserSessions()
                    ->accountDeletion()
                    ->profilePhotos()
                    ->api()
                    ->political(invitations: true)
                    ->termsAndPrivacyPolicy()
                    ->notifications()
                    ->modals()
                    ->socialite(
                        providers: [Provider::Github],
                        features: [Feature::RememberSession, Feature::ProviderAvatars],
                    ),
            )
            ->registration(Register::class)
            ->colors([
                'primary' => Color::Amber,
            ])
            ->tenant(Political::class)
            ->tenantProfile(PoliticalSettings::class)
            ->tenantRegistration(CreatePolitical::class)
            ->discoverResources(in: app_path('Filament/Political/Resources'), for: 'App\\Filament\\Political\\Resources')
            ->discoverPages(in: app_path('Filament/Political/Pages'), for: 'App\\Filament\\Political\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->userMenuItems([
                'profile' => MenuItem::make()
                    ->label('Profile')
                    ->icon('heroicon-o-user-circle')
                    ->url(static fn () => route(Profile::getRouteName(panel: 'admin'))),
            ])
            ->authGuard('web')
            ->discoverWidgets(in: app_path('Filament/Political/Widgets'), for: 'App\\Filament\\Political\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configurePermissions();

        PoliticalFlow::createUsersUsing(CreateNewUser::class);
        PoliticalFlow::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        PoliticalFlow::updateUserPasswordsUsing(UpdateUserPassword::class);

        PoliticalFlow::createPoliticalsUsing(CreatePolitical::class);
        PoliticalFlow::updatePoliticalNamesUsing(UpdatePoliticalName::class);
        PoliticalFlow::addPoliticalEmployeesUsing(AddPoliticalEmployee::class);
        PoliticalFlow::invitePoliticalEmployeesUsing(InvitePoliticalEmployee::class);
        PoliticalFlow::removePoliticalEmployeesUsing(RemovePoliticalEmployee::class);
        PoliticalFlow::deletePoliticalsUsing(DeletePolitical::class);
        PoliticalFlow::deleteUsersUsing(DeleteUser::class);

        PoliticalFlow::resolvesSocialiteUsersUsing(ResolveSocialiteUser::class);
        PoliticalFlow::createUsersFromProviderUsing(CreateUserFromProvider::class);
        PoliticalFlow::createConnectedAccountsUsing(CreateConnectedAccount::class);
        PoliticalFlow::updateConnectedAccountsUsing(UpdateConnectedAccount::class);
        PoliticalFlow::setUserPasswordsUsing(SetUserPassword::class);
        PoliticalFlow::handlesInvalidStateUsing(HandleInvalidState::class);
        PoliticalFlow::generatesProvidersRedirectsUsing(GenerateRedirectForProvider::class);
    }

    /**
     * Configure the roles and permissions that are available within the application.
     */
    protected function configurePermissions(): void
    {
        PoliticalFlow::defaultApiTokenPermissions(['read']);

        PoliticalFlow::role('admin', 'Administrator', [
            'create',
            'read',
            'update',
            'delete',
        ])->description('Administrator users can perform any action.');

        PoliticalFlow::role('editor', 'Editor', [
            'read',
            'create',
            'update',
        ])->description('Editor users have the ability to read, create, and update.');
    }
}
