@extends('admin.layouts.appLogged')
@section('title',__('text.Order Show'))
@push('css')
<style>

</style>
@endpush
@section('content')

<div class="content">

    <!-- Start Content-->
    <div class="container-fluid">

      <!-- start page title -->
      <x-admin.general.page-title>
        <li class="breadcrumb-item active">{{ __('text.Order Show') }}</li>
        <li class="breadcrumb-item"><a href="{{route('admin.orders')}}">{{__('text.Orders')}}</a></li>
        <li class="breadcrumb-item"><a href="{{route('admin.index')}}">{{__('text.Dashboard')}}</a></li>
        <x-slot name="title">
            <h4 class="page-title">{{__('text.Order Show')}}</h4>
        </x-slot>
    </x-admin.general.page-title>

        <div class="property-detail-wrapper">
            <div class="row">
                <div class="col-lg-8">


                    {{-- products images  --}}
                    <div class="">
                        <div id="carouselExampleIndicators"  class="carousel slide" data-ride="carousel">
                            <ol class="carousel-indicators">
                                @for ($i = 0; $i < $order->products()->withTrashed()->when(auth()->user()->role != 'admin',function($q){
                                    return $q->where('user_id',auth()->user()->id);
                                })->count(); $i++)
                                <li data-target="#carouselExampleIndicators" data-slide-to="{{$i}}" class="{{$i == 0 ? 'active' : ''}}"></li>
                                @endfor
                            </ol>
                            <div class="carousel-inner">
                                @foreach($order->products()->withTrashed()->when(auth()->user()->role != 'admin',function($q){
                                    return $q->where('user_id',auth()->user()->id);
                                })->get() as $product)
                                <div class="carousel-item  {{$loop->index == 0 ? 'active' : ''}}">
                                    <img class="d-block w-100" style="height: 500px" src="{{ asset('/images/products//'.$product->pivot->image) }}" alt="Second slide">
                                </div>
                                @endforeach
                            </div>
                            <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
                              <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                              <span class="sr-only">Previous</span>
                            </a>
                            <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
                              <span class="carousel-control-next-icon" aria-hidden="true"></span>
                              <span class="sr-only">Next</span>
                            </a>
                          </div>

                    </div>
                    <!-- end slider -->

                    {{-- products name --}}
                    <div class="mt-4">
                        <h4>
                            @foreach ($order->products()->withTrashed()->when(auth()->user()->role != 'admin',
                            function($q){return $q->where('user_id',auth()->user()->id);
                            })->get() as $product)
                            @if ($loop->index != 0)
                                 +
                            @endif
                            {{app()->getLocale() == 'ar' ? $product->pivot->name_ar: $product->pivot->name_en}}
                            @endforeach
                        </h4>

                        {{-- order details --}}
                        @can('isAdmin')
                            <p class="text-muted text-overflow"><i class="mdi mdi-map-marker-radius mr-2"></i>{{$order->location}}</p>
                            <h4 class="mt-4 mb-3">@lang('text.Receiver Information')</h4>
                            <p class="text-muted text-overflow"><span class="text-danger">@lang('text.Name')</span>: {{$order->receiver_first_name . " " .$order->receiver_last_name}}</p>
                            <p class="text-muted text-overflow"><span class="text-danger">@lang('text.Phone Number')</span>: {{$order->receiver_phone}}</p>

                            <h4 class="mt-4 mb-3">@lang('text.Payment Information')</h4>
                            <p class="text-muted text-overflow"><span class="text-danger">@lang('text.Payment Way')</span>: {{__('text.'.ucfirst($order->payment_way))}}</p>
                            <p class="text-muted text-overflow"><span class="text-danger">@lang('text.Payment Status')</span>:
                                @if ($order->payment_status == 'paid')
                                            <i class="text-success mdi mdi-checkbox-marked-circle"></i>
                                        @elseif ($order->payment_status == 'failed')
                                            <i class="text-danger mdi mdi-close-circle"></i>
                                        @elseif ($order->payment_status == 'unpaid')
                                            <i class="text-warning mdi mdi-dots-horizontal-circle"></i>
                                        @endif
                                {{__('text.'.ucfirst($order->payment_status))}}</p>
                            <p class="text-muted text-overflow"><span class="text-danger">@lang('text.Order status')</span>: {{__('text.'.ucfirst($order->order_status))}}</p>

                            <h4 class="mt-4 mb-3">@lang('text.Deliery Service Provider Information')</h4>
                            @if ($order->delivery_service_provider_id)
                                <a href="{{ $order->delivery_service_provider->image }}" target="_blanck"><img src="{{ $order->delivery_service_provider->image }}" class="rounded-circle" style="width: 100px;height: 100px" alt="delivery-image"></a>
                                <br>
                                <p class="text-muted text-overflow"><span class="text-danger">@lang('text.Name')</span>: {{$order->delivery_service_provider->name}}</p>
                                <p class="text-muted text-overflow"><span class="text-danger">@lang('text.Phone Number')</span>: {{$order->delivery_service_provider->phone}}</p>
                                @else
                                    @lang('text.Does Not Exist')
                            @endif
                        @endcan



                        {{-- items details --}}
                        <div class="card-box" style="max-height: 1000px;overflow-y:scroll">
                            <div class="table-responsive">
                                @foreach ( $order->colors()->withTrashed()->when(auth()->user()->role !='admin',function($q){
                                    return $q->join('products','products.id','colors.product_id')
                                         ->withTrashed()->where('products.user_id',auth()->user()->id);
                                })->get() as $row)
                                <table class="table table-bordered table-secondary  mb-4">
                                    <tbody>
                                    <tr>
                                        <th > @lang('text.Store Name')</th>
                                        <th > @lang('text.Product Name')</th>
                                        <th > @lang('text.Price')</th>
                                        <th >@lang('text.Quantity') </th>
                                        <th >@lang('text.Taxes') </th>
                                        <th >@lang('text.Color')</th>
                                        <th >@lang('text.Sizes')</th>

                                        @php
                                            $refunds=sizes_refund($order->id,$row->sizes()->withTrashed()->get()->pluck('id')->toArray());
                                        @endphp

                                        @if ($refunds->count() > 0)
                                        <th >@lang('text.Refunds')</th>
                                        @endif
                                    </tr>
                                    <tr>
                                        <td>{{ $row->product()->withTrashed()->first()->user()->withTrashed()->pluck('store_name')->first() }} </td>

                                        <td>{{$row->product()->withTrashed()->when(true,function($q){
                                            if(app()->getLocale() == 'ar'){
                                                return $q->pluck('name_ar')->first();
                                            }else {
                                                return $q->pluck('name_en')->first();
                                            }
                                        })}}</td>
                                        <td>{{$row->pivot->amount}} @lang('text.RSA')</td>
                                        <td>{{$row->pivot->quantity}}</td>
                                        <td>{{$row->product()->withTrashed()->first()->taxes()->withTrashed()->sum('tax')."% = ".((($row->pivot->amount*$row->product()->withTrashed()->first()->taxes()->withTrashed()->sum('tax'))/100)*$row->pivot->quantity)}} {{ app()->getLocale() == 'ar' ? 'ريال' : 'RSA' }}</td>
                                        <td><span class="label label-danger w-100" style="height:25px;border-radius:10px;background-color: {{ $row->color }};display:inline-block"></span></td>
                                        <td>{{ $order->sizes()->withTrashed()->where('color_id',$row->id)->get()->pluck('size')->implode(',') }}</td>
                                        @if ($refunds->count() > 0)
                                        <td>
                                            @foreach ($refunds as $refund)
                                                {{ $refund->quantity .' '. $refund->size.'='. $refund->total_refund_amount}} @lang('text.RSA')
                                            @endforeach
                                        </td>
                                        @endif
                                    </tr>
                                    </tbody>
                                </table>
                                @endforeach
                                <hr>
                                <table class="table table-bordered  table-responsive mb-4">
                                    <tbody class="table-secondary">
                                    <tr>
                                        <th > @lang('text.Total Amount')</th>
                                        <th > @lang('text.Subtotal')</th>
                                        <th> @lang('text.Total Taxes')</th>
                                        @can('isAdmin')
                                        <th> @lang('text.Shipping')</th>

                                        @endcan
                                        <th> @lang('text.Total Pieces')</th>
                                    </tr>
                                    <tr>
                                        <td>
                                            {{
                                                auth()->user()->role =='admin' ?
                                                $order->total_amount : $order->vendors->find(auth()->user()->id)->pivot->total_amount
                                            }} @lang('text.RSA')
                                        </td>
                                        <td>
                                            {{
                                                auth()->user()->role =='admin' ?
                                                $order->subtotal : $order->vendors->find(auth()->user()->id)->pivot->subtotal
                                            }} @lang('text.RSA')
                                        </td>

                                        <td>{{ auth()->user()->role =='admin' ?
                                            $order->taxes : $order->vendors->find(auth()->user()->id)->pivot->taxes }}</td>
                                        @can('isAdmin')
                                        <td>{{ $order->shipping }}</td>
                                        @endcan
                                        <td>
                                            {{
                                                $order->colors()->withTrashed()->when(auth()->user()->role !='admin',function($q){
                                                    return $q->with(['product' => function($q){
                                                            return $q->withTrashed()->where('user_id',auth()->user()->id);
                                                    }]);
                                                })->get()->pluck('pivot')->sum('quantity')
                                            }}
                                        </td>
                                    </tr>

                                    </tbody>
                                </table>
                            </div>
                        </div>



                    </div>
                    <!-- end m-t-30 -->

                </div>
                <!-- end col -->

                {{-- customer details  --}}
                <div class="col-lg-4">
                    @can('isAdmin')
                        <div class="text-center card-box">
                            <div class="text-left">
                                <h4 class="header-title mb-4">@lang('text.User')</h4>
                            </div>
                            <div class="member-card">
                                <div class="avatar-xl member-thumb mb-2 mx-auto d-block">
                                    <img src="{{$order->customer()->withTrashed()->first()->image }}" class="rounded-circle img-thumbnail" alt="profile-image">
                                    <i class="mdi mdi-star-circle member-star text-success" title="Featured Agent"></i>
                                </div>

                                <div class="">
                                    <h5 class="font-18 mb-1">{{$order->customer()->withTrashed()->first()->name}}</h5>
                                </div>

                                <div class="mt-20">
                                    <ul class="list-inline row">
                                        <li class="list-inline-item col-12 mx-0">
                                            <h5>@lang('text.Email')</h5>
                                            <p>{{ $order->customer()->withTrashed()->first()->email }}</p>
                                        </li>
                                        <li class="list-inline-item col-6 mx-0">
                                            <h5>@lang('text.Orders')</h5>
                                            <p>{{$order->customer()->withTrashed()->first()->orders()->count()}}</p>
                                        </li>

                                        <li class="list-inline-item col-6 mx-0">
                                            <h5>@lang('text.Phone Number')</h5>
                                            <p>{{ $order->customer()->withTrashed()->first()->phone }}</p>
                                        </li>
                                    </ul>
                                </div>

                            </div>
                            <!-- end membar card -->
                        </div>
                        <div class="text-center row ">
                            <div class="text-left col-12">
                                <h4 class="header-title mb-4">@lang('text.Location')</h4>
                            </div>
                            <div class="mapouter col-12">
                                <div class="gmap_canvas">
                                    <iframe style="width:100%!important"  height="500" id="gmap_canvas" src="https://maps.google.com/maps?center=45.468889,9.202216&q={{ $order->lat_long }}&t=&z=13&ie=UTF8&iwloc=&output=embed" frameborder="0" scrolling="no" marginheight="0" marginwidth="0"></iframe>
                                    <a href="https://kissanime-ws.com"></a>
                                    <br>
                                    <style>.mapouter{position:relative;text-align:right;height:500px;width:400px;}</style>
                                    <a href="https://www.embedgooglemap.net">how to get google map embed code</a>
                                    <style>.gmap_canvas {overflow:hidden;background:none!important;height:500px;width:400px;}</style>
                                </div>
                            </div>
                        </div>
                    @endcan

                    <!-- end card-box -->

                </div>

                <!-- end col -->
            </div>
            <!-- end row -->
        </div>

    </div>
    <!-- end container-fluid -->

</div>
<!-- end content -->
@endsection
@push('script')

@endpush
