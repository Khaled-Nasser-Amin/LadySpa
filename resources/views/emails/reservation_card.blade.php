@component('mail::message')
# @lang('text.Hello,'){{ $name }}

@lang('text.Your Reservation')
@component('mail::panel')
@component('mail::table')
|@lang('text.Addition Name')|@lang('text.Price')|
|:-------------:|:-------------:|
@foreach ( $reservation->additions()->withTrashed()->get() as $row)
|{{$row->pivot->pluck('name_'.app()->getLocale())->first() }}|{{$row->pivot->price}} {{ app()->getLocale() == 'ar' ? 'ريال' : 'RSA' }}|
@endforeach
@endcomponent
@endcomponent


@component('mail::panel')
@component('mail::table')
|@lang('text.Date')|@lang('text.Start time')|@lang('text.End time')|
|:-------------:|:-------------:|:------------:|
@foreach ( $reservation->times()->get() as $row)
|{{ $row->date }}|{{$row->start_time}}|{{ $row->end_time }}|
@endforeach
@endcomponent
@endcomponent

@component('mail::panel')
@component('mail::table')
@if($reservation->type == 'outdoor')
|@lang('text.Total Amount')|@lang('text.Subtotal')|@lang('text.Total Taxes')|@lang('text.Shipping')|@lang('text.Discount')|
|:-------------:|:-------------:|:--------:|:------------:|:------------:|
|{{$reservation->total_amount}} {{ app()->getLocale() == 'ar' ? 'ريال' : 'RSA' }}|{{$reservation->subtotal}} {{ app()->getLocale() == 'ar' ? 'ريال' : 'RSA' }}|{{ $reservation->taxes }}|{{ $reservation->shipping }}|{{ $reservation->discount }}|
@endif

@if($reservation->type == 'indoor')
|@lang('text.Total Amount')|@lang('text.Subtotal')|@lang('text.Total Taxes')|@lang('text.Discount')|
|:-------------:|:-------------:|:--------:|:--------:|
|{{$reservation->total_amount}} {{ app()->getLocale() == 'ar' ? 'ريال' : 'RSA' }}|{{$reservation->subtotal}} {{ app()->getLocale() == 'ar' ? 'ريال' : 'RSA' }}|{{ $reservation->taxes }}|{{ $reservation->discount }}|
@endif

@endcomponent
@endcomponent



@lang('text.Thanks,')<br>
<br>
{{ config('app.name') }}
@endcomponent
