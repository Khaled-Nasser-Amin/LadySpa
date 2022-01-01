@component('mail::message')
# @lang('text.Hello,') {{ $store_name }}

{{ $message }}

@lang('text.Thanks,')<br>
{{ config('app.name') }}
@endcomponent
