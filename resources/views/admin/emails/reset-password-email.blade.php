@component('mail::message')
# Recovering Your Password

please click the button below to reset your password.

@component('mail::button', ['url' => $url])
@lang('text.Submit')
@endcomponent

@lang('text.Thanks,')<br>
{{ config('app.name') }}
@endcomponent
