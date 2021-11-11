@component('mail::message')
# @lang('text.Hello,'){{ $name }}

@lang('text.Your activation code is:') {{ $code }}

@lang('text.Thanks,')<br>
<br>
{{ config('app.name') }}
@endcomponent
