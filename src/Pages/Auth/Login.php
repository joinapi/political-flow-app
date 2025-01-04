<?php

namespace Joinapi\PoliticalFlow\Pages\Auth;

use Filament\Forms\Form;
use Filament\Pages\Auth\Login as FilamentLogin;
use Joinapi\PoliticalFlow\PoliticalFlow;

class Login extends FilamentLogin
{
    public static string $view = 'political-flow::auth.login';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                $this->getEmailFormComponent(),
                $this->getPasswordFormComponent(),
                $this->getRememberFormComponent(),
            ])
            ->statePath('data')
            ->model(PoliticalFlow::userModel());
    }
}
