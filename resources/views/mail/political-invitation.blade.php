@component('mail::message')
{{ __('You have been invited to join the :political political!', ['political' => $invitation->political->name]) }}

@if (filament()->getRegistrationUrl())
{{ __('If you do not have an account, you may create one by clicking the button below. After creating an account, you may click the invitation acceptance button in this email to accept the company invitation:') }}

@component('mail::button', ['url' => url(filament()->getRegistrationUrl())])
    {{ __('Create Account') }}
@endcomponent

{{ __('If you already have an account, you may accept this invitation by clicking the button below:') }}

@else
{{ __('You may accept this invitation by clicking the button below:') }}
@endif


@component('mail::button', ['url' => $acceptUrl])
{{ __('Accept Invitation') }}
@endcomponent

{{ __('If you did not expect to receive an invitation to this portal, you may discard this email.') }}
@endcomponent
