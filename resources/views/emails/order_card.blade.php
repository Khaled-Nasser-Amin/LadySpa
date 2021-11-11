@component('mail::message')
# @lang('text.Hello,'){{ $vendor->name }}

@lang('text.Your order')
@component('mail::panel')
@component('mail::table')
|@lang('text.Image')|@lang('text.Product Name')|@lang('text.Color')|@lang('text.Sizes')|@lang('text.Quantity')|@lang('text.Price')|
|:-------------:|:-------------:|:--------:|:------------:|:------------:|:------------:|
@foreach ( $order->colors()->withTrashed()->get()->when($vendor->role !='admin',function($q) use($vendor){return $q->where('product.user_id',$vendor->id);}) as $row)
|<a href="{{ $row->images->pluck('name')->first() }}" target="_blanck">@lang('Image')</a>|{{$row->product()->withTrashed()->pluck('name_'.app()->getLocale())->first() }}|<span class="label label-danger w-100" style="height:25px;width:25px;border-radius:10px;background-color: {{ $row->color }};display:inline-block"></span>|{{ $order->sizes()->withTrashed()->where('color_id',$row->id)->get()->pluck('size')->implode(',') }}|{{$row->pivot->quantity}}|{{$row->pivot->amount}} {{ app()->getLocale() == 'ar' ? 'ريال' : 'RSA' }}|
@endforeach
@endcomponent
@endcomponent


@component('mail::panel')
@component('mail::table')
@if($vendor->role == 'admin')
|@lang('text.Total Amount')|@lang('text.Subtotal')|@lang('text.Total Taxes')|@lang('text.Shipping')|
|:-------------:|:-------------:|:--------:|:------------:|
|{{$vendor->role =='admin' ?$order->total_amount : $order->vendors->find($vendor->id)->pivot->subtotal}} {{ app()->getLocale() == 'ar' ? 'ريال' : 'RSA' }}|{{$vendor->role =='admin' ?$order->subtotal : $order->vendors->find($vendor->id)->pivot->subtotal-$order->vendors->find($vendor->id)->pivot->taxes}} {{ app()->getLocale() == 'ar' ? 'ريال' : 'RSA' }}|{{ $vendor->role =='admin' ?$order->taxes : $order->vendors->find($vendor->id)->pivot->taxes }}|{{ $order->shipping }}|
@endif

@if($vendor->role != 'admin')
|@lang('text.Total Amount')|@lang('text.Subtotal')|@lang('text.Total Taxes')|
|:-------------:|:-------------:|:--------:|
|{{$vendor->role =='admin' ?$order->total_amount : $order->vendors->find($vendor->id)->pivot->total_amount}} {{ app()->getLocale() == 'ar' ? 'ريال' : 'RSA' }}|{{$vendor->role =='admin' ?$order->subtotal : $order->vendors->find($vendor->id)->pivot->subtotal}} {{ app()->getLocale() == 'ar' ? 'ريال' : 'RSA' }}|{{ $vendor->role =='admin' ?$order->taxes : $order->vendors->find($vendor->id)->pivot->taxes }}|
@endif

@endcomponent
@endcomponent



@lang('text.Thanks,')<br>
<br>
{{ config('app.name') }}
@endcomponent
