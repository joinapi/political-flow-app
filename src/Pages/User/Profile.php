<?php

namespace Joinapi\PoliticalFlow\Pages\User;

use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class Profile extends Page
{
    protected static string $view = 'political-flow::filament.pages.user.profile';

    protected static bool $shouldRegisterNavigation = false;

    public function getTitle(): string
    {
        return __('political-flow::default.pages.titles.profile');
    }

    protected function getViewData(): array
    {
        return [
            'user' => Auth::user(),
        ];
    }
}
