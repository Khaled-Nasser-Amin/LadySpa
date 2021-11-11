@component('mail::message')
# @lang('text.Hello,'){{ $vendor_name }}

@lang('text.Your product\'s size is empty')


@component('mail::panel')
@component('mail::table')
|@lang('text.Product Name')|@lang('text.Color')|@lang('text.Size')|@lang('text.Stock')|
|:-------------:|:-------------:|:--------:|:------------:|
|{{ $product_name }}|<span class="label label-danger w-100" style="height:25px;width:25px;border-radius:10px;background-color: {{ $color }};display:inline-block"></span>|{{ $size }}|0|
@endcomponent
@endcomponent


@lang('text.Thanks,')<br>
<br>
{{ config('app.name') }}
@endcomponent
