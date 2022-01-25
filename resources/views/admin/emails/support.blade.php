@component('mail::message')
# @lang('text.Support')

{{ $message }}

@lang('text.Thanks,')<br>
{{ config('app.name') }}
@endcomponent
