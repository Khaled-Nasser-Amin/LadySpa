@component('mail::message')
# @lang('text.Hello,'){{ $vendor->name }}

@lang('text.Your Order')
@component('mail::panel')
@component('mail::table')
|@lang('text.Image')|@lang('text.Product Name')|@lang('text.Size')|@lang('text.Quantity')|@lang('text.Price')|
|:-------------:|:-------------:|:--------:|:------------:|:------------:|
@foreach ( $order->sizes()->withTrashed()->get()->when($vendor->role !='admin',function($q) use($vendor){return $q->where('product.user_id',$vendor->id);}) as $row)
|<a href="{{ $row->product->image }}" target="_blanck">@lang('Image')</a>|{{$row->product()->withTrashed()->pluck('name_'.app()->getLocale())->first() }}|{{ $row->size }}|{{$row->pivot->quantity}}|{{$row->pivot->amount}} {{ app()->getLocale() == 'ar' ? 'ريال' : 'RSA' }}|
@endforeach
@endcomponent
@endcomponent


@component('mail::panel')
@component('mail::table')
@if($vendor->role == 'admin')
|@lang('text.Total Amount')|@lang('text.Subtotal')|@lang('text.Total Taxes')|@lang('text.Shipping')|@lang('text.Discount')|
|:-------------:|:-------------:|:--------:|:------------:|:------------:|
|{{$order->total_amount}} {{ app()->getLocale() == 'ar' ? 'ريال' : 'RSA' }}|{{$order->subtotal}} {{ app()->getLocale() == 'ar' ? 'ريال' : 'RSA' }}|{{ $order->taxes }}|{{ $order->shipping }}|{{ $order->discount }}|
@endif

@if($vendor->role != 'admin')
|@lang('text.Total Amount')|@lang('text.Subtotal')|@lang('text.Total Taxes')|
|:-------------:|:-------------:|:--------:|
|{{$order->vendors->find($vendor->id)->pivot->total_amount}} {{ app()->getLocale() == 'ar' ? 'ريال' : 'RSA' }}|{{$order->vendors->find($vendor->id)->pivot->subtotal}} {{ app()->getLocale() == 'ar' ? 'ريال' : 'RSA' }}|{{ $order->vendors->find($vendor->id)->pivot->taxes }}|
@endif

@endcomponent
@endcomponent



@lang('text.Thanks,')<br>
<br>
{{ config('app.name') }}
@endcomponent
