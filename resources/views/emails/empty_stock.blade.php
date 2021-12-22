@component('mail::message')
# @lang('text.Hello,'){{ $vendor_name }}

@lang('text.Your product\'s size is empty')


@component('mail::panel')
@component('mail::table')
|@lang('text.Product Name')|@lang('text.Size')|@lang('text.Stock')|
|:-------------:|:-------------:|:--------:|:------------:|
|{{ $product_name }}|{{ $size }}|0|
@endcomponent
@endcomponent


@lang('text.Thanks,')<br>
<br>
{{ config('app.name') }}
@endcomponent
