<?php

namespace Joinapi\PoliticalFlow\Pages\Auth;

use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Component;
use Filament\Forms\Form;
use Filament\Pages\Auth\Register as FilamentRegister;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;
use Joinapi\PoliticalFlow\PoliticalFlow;

class Register extends FilamentRegister
{
    protected static string $view = 'political-flow::auth.register';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                $this->getNameFormComponent(),
                $this->getEmailFormComponent(),
                $this->getPasswordFormComponent(),
                $this->getPasswordConfirmationFormComponent(),
                ...PoliticalFlow::hasTermsAndPrivacyPolicyFeature() ? [$this->getTermsFormComponent()] : []])
            ->statePath('data')
            ->model(PoliticalFlow::userModel());
    }

    protected function getTermsFormComponent(): Component
    {
        return Checkbox::make('terms')
            ->label(new HtmlString(__('political-flow::default.subheadings.auth.register', [
                'terms_of_service' => $this->generateFilamentLink(Terms::getRouteName(), __('political-flow::default.links.terms_of_service')),
                'privacy_policy' => $this->generateFilamentLink(PrivacyPolicy::getRouteName(), __('political-flow::default.links.privacy_policy')),
            ])))
            ->validationAttribute(__('political-flow::default.errors.terms'))
            ->accepted();
    }

    public function generateFilamentLink(string $routeName, string $label): string
    {
        return Blade::render('filament::components.link', [
            'href' => PoliticalFlow::route($routeName),
            'target' => '_blank',
            'color' => 'primary',
            'slot' => $label,
        ]);
    }
}
