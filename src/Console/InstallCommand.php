<?php

namespace Joinapi\PoliticalFlow\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\error;
use function Laravel\Prompts\info;
use function Laravel\Prompts\select;

class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'political-flow:install {--socialite : Install with Socialite support} {--force : Overwrite existing files}';

    /**
     * The console command description.
     *
     * @var string|null
     */
    protected $description = 'Install the Political Flow package';

    private bool $withSocialite = false;

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if ($this->checkExistingInstallation() === static::FAILURE) {
            return static::FAILURE;
        }

        $this->determineInstallationType();

        info('Installing  Political Flow...');

        // Install Political Flows...
        try {
            $this->commonInstallation();
            $this->installPoliticalFlow();
        } catch (\Exception $e) {
            Log::error('Installation error while installing Political Flows: ' . $e->getMessage());

            error('An error occurred while installing Political Flow. Please check the log for more information.');

            return static::FAILURE;
        }

        return static::SUCCESS;
    }

    protected function checkExistingInstallation(): int
    {
        $force = $this->option('force');

        if ($force === false && File::exists(app_path('Providers/PoliticalFlowServiceProvider.php'))) {
            $shouldProceed = confirm(
                label: 'Political Flow is already installed. Would you like to proceed with the installation?',
                default: false,
                yes: 'Yes, proceed with the installation',
                no: 'No, abort the installation',
                hint: 'By continuing, some files may be overwritten. If necessary, it is recommended to backup your application before proceeding.',
            );

            if ($shouldProceed === false) {
                info('Political Flow installation aborted.');

                return static::FAILURE;
            }
        }

        return static::SUCCESS;
    }

    protected function determineInstallationType(): void
    {
        $this->withSocialite = $this->option('socialite');

        if ($this->withSocialite === false) {
            $installationType = select(
                label: 'Which installation type would you like to use?',
                options: [
                    'base' => 'Base Package',
                    'socialite' => 'With Socialite Support',
                ],
                default: 'base',
            );

            $this->withSocialite = $installationType === 'socialite';
        }
    }

    protected function commonInstallation(): void
    {
        // Storage...
        $this->callSilent('storage:link');

        // Update Welcome Page...
        $this->updateWelcomePage();

        // Configure Session...
        $this->configureSession();

        // Publish...
        $this->callSilent('vendor:publish', [
            '--tag' => 'political-flow-migrations-base',
            '--force' => true,
        ]);

        $this->callSilent('vendor:publish', [
            '--tag' => 'political-flow-migrations-biz',
            '--force' => true,
        ]);

        $this->callSilent('vendor:publish', [
            '--tag' => 'political-flow-migrations-biz',
            '--force' => true,
        ]);

        $this->callSilent('vendor:publish', [
            '--tag' => 'political-flow-migrations-socialite',
            '--force' => true,
        ]);

        // Sanctum...
        $this->call('install:api', [
            '--without-migration-prompt' => true,
        ]);

        // Directories...
        (new Filesystem)->ensureDirectoryExists(app_path('Actions/PoliticalFlow'));
        (new Filesystem)->ensureDirectoryExists(app_path('Policies'));
        (new Filesystem)->ensureDirectoryExists(resource_path('markdown'));

        // Delete Directories...
        (new Filesystem)->deleteDirectory(resource_path('sass'));

        // Terms Of Service / Privacy Policy...
        $this->copyStubFiles('resources/markdown', resource_path('markdown'), ['terms.md', 'policy.md']);

        // Factories...
        copy(__DIR__ . '/../../database/factories/UserFactory.php', base_path('database/factories/UserFactory.php'));
        copy(__DIR__ . '/../../database/factories/PoliticalFactory.php', base_path('database/factories/PoliticalFactory.php'));

        // Actions...
        $this->copyStubFiles('app/Actions/PoliticalFlow', app_path('Actions/PoliticalFlow'), [
            'AddPoliticalEmployee.php',
            'CreatePolitical.php',
            'CreateNewUser.php',
            'DeletePolitical.php',
            'InvitePoliticalEmployee.php',
            'RemovePoliticalEmployee.php',
            'UpdatePoliticalName.php',
            'UpdateUserPassword.php',
            'UpdateUserProfileInformation.php',
        ]);

        // Policies...
        $this->copyStubFiles('app/Policies', app_path('Policies'), ['PoliticalPolicy.php']);

        // Seeders...
        copy(__DIR__ . '/../../database/seeders/DatabaseSeeder.php', base_path('database/seeders/DatabaseSeeder.php'));

        // Models...
        $this->copyStubFiles('app/Models', app_path('Models'), ['Political.php', 'PoliticalInvitation.php', 'Employeeship.php']);
    }

    /**
     * Update the default welcome page.
     */
    protected function updateWelcomePage(): void
    {
        $filePath = resource_path('views/welcome.blade.php');

        if (file_exists($filePath)) {
            $fileContents = file_get_contents($filePath);

            $alreadyExists = Str::contains($fileContents, 'filament()->getHomeUrl()');

            if ($alreadyExists) {
                return;
            }

            $this->replaceInFile("Route::has('login')", 'filament()->getLoginUrl()', $filePath);
            $this->replaceInFile("Route::has('register')", 'filament()->getRegistrationUrl()', $filePath);
            $this->replaceInFile('Dashboard', '{{ ucfirst(filament()->getCurrentPanel()->getId()) }}', $filePath);
            $this->replaceInFile("{{ url('/dashboard') }}", '{{ filament()->getHomeUrl() }}', $filePath);
            $this->replaceInFile("{{ route('login') }}", '{{ filament()->getLoginUrl() }}', $filePath);
            $this->replaceInFile("{{ route('register') }}", '{{ filament()->getRegistrationUrl() }}', $filePath);
        }
    }

    /**
     * Configure the session driver for Political.
     */
    protected function configureSession(): void
    {
        $this->replaceInFile('SESSION_DRIVER=cookie', 'SESSION_DRIVER=database', base_path('.env'));
        $this->replaceInFile('SESSION_DRIVER=cookie', 'SESSION_DRIVER=database', base_path('.env.example'));
    }

    /**
     * Install the FilamentPoliticals political stack into the application.
     */
    protected function installPoliticalFlow(): void
    {
        if ($this->withSocialite) {
            $this->ensureApplicationIsSocialiteCompatible();
            info('Political Flow with Socialite support installed successfully.');
        } else {
            $this->ensureApplicationIsOnlyPoliticalCompatible();
            info('Political Flow installed successfully.');
        }
    }

    /**
     * Ensure the installed user model is ready for political usage.
     */
    protected function ensureApplicationIsOnlyPoliticalCompatible(): void
    {
        // Service Providers...
        $this->copyStubFiles('app/Providers', app_path('Providers'), ['PoliticalFlowServiceProvider.php']);
        ServiceProvider::addProviderToBootstrapFile('App\Providers\PoliticalFlowServiceProvider');

        // Models...
        $this->copyStubFiles('app/Models', app_path('Models'), ['User.php']);

        // PoliticalFlow Actions...
        $this->copyStubFiles('app/Actions/PoliticalFlow', app_path('Actions/PoliticalFlow'), ['DeleteUser.php']);
    }

    protected function ensureApplicationIsSocialiteCompatible(): void
    {
        // Publish PoliticalFlow Socialite Migrations...
        $this->callSilent('vendor:publish', ['--tag' => 'political-flow-socialite-migrations', '--force' => true]);

        // Service Providers...
        copy(__DIR__ . '/../../stubs/app/Providers/PoliticalFlowWithSocialiteServiceProvider.php', app_path('Providers/PoliticalFlowServiceProvider.php'));
        ServiceProvider::addProviderToBootstrapFile('App\Providers\PoliticalFlowServiceProvider');

        // Models...
        copy(__DIR__ . '/../../stubs/app/Models/UserWithSocialite.php', app_path('Models/User.php'));

        $this->copyStubFiles('app/Models', app_path('Models'), ['ConnectedAccount.php']);

        // Actions...
        copy(__DIR__ . '/../../stubs/app/Actions/PoliticalFlow/DeleteUserWithSocialite.php', app_path('Actions/PoliticalFlow/DeleteUser.php'));

        $this->copyStubFiles('app/Actions/PoliticalFlow', app_path('Actions/PoliticalFlow'), [
            'CreateConnectedAccount.php',
            'CreateUserFromProvider.php',
            'HandleInvalidState.php',
            'ResolveSocialiteUser.php',
            'SetUserPassword.php',
            'UpdateConnectedAccount.php',
        ]);

        // Policies...
        $this->copyStubFiles('app/Policies', app_path('Policies'), ['ConnectedAccountPolicy.php']);
    }

    protected function copyStubFiles(string $sourceSubPath, string $destinationPath, array $files): void
    {
        foreach ($files as $file) {
            copy(__DIR__ . '/../../stubs/' . $sourceSubPath . '/' . $file, $destinationPath . '/' . $file);
        }
    }

    /**
     * Replace a given string within a given file.
     */
    protected function replaceInFile(string $search, string $replace, string $path): void
    {
        file_put_contents($path, str_replace($search, $replace, file_get_contents($path)));
    }
}
