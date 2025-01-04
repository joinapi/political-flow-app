<?php

namespace Joinapi\PoliticalFlow\Pages\Political;

use Filament\Facades\Filament;
use Filament\Pages\Tenancy\EditTenantProfile as BaseEditTenantProfile;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Model;

use function Filament\authorize;

class PoliticalSettings extends BaseEditTenantProfile
{
    protected static string $view = 'political-flow::filament.pages.political.political_settings';

    public static function getLabel(): string
    {
        return __('political-flow::default.pages.titles.political_settings');
    }

    public static function canView(Model $tenant): bool
    {
        try {
            return authorize('view', $tenant)->allowed();
        } catch (AuthorizationException $exception) {
            return $exception->toResponse()->allowed();
        }
    }

    protected function getViewData(): array
    {
        return [
            'political' => Filament::getTenant(),
        ];
    }
}
