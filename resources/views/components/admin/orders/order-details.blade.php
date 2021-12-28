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

                <div class="d-flex flex-row flex-wrap">
                    <div class="col-md-6 col col-sm-12">
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
                        <p class="text-muted text-overflow"><span class="text-danger">@lang('text.Order status')</span>:
                            @if ($order->hold == 0)
                                @if ($order->order_status == 'pending')
                                    <i class="far fa-pause-circle"></i>
                                @elseif ($order->order_status == 'processing')
                                    <i class="text-primary fas fa-cogs"></i>
                                @elseif ($order->order_status == 'shipping')
                                    <i class=" text-info fas fa-truck"></i>
                                @elseif ($order->order_status == 'completed')
                                    <i class="text-success fas fa-check-circle"></i>
                                @elseif ($order->order_status == 'canceled')
                                <i class="text-danger mdi mdi-close-circle"></i>
                                @endif
                                {{__('text.'.ucfirst($order->order_status))}}

                            @else
                                <i class="text-dark far fa-pause-circle"></i> @lang('text.Hold')
                            @endif
                        </p>
                    </div>
                    <div class="col-md-6 col-sm-12 py-4 d-flex flex-row justify-content-center">
                        @if ($order->order_status != 'completed' && $order->order_status != 'canceled' && $order->order_status != 'modified')
                            @if ($order->order_status != 'pending'   && ($order->order_status == 'processing' || $order->order_status == 'shipping') )
                                @if ($order->hold == 0)
                                <button class="btn btn-warning btn-sm mx-1 text-dark" wire:click.prevent="holdOrder" style="height: 60px">@lang('text.Hold') <i class="text-dark far fa-pause-circle"></i></button>

                                    @else
                                    <button class="btn btn-primary btn-sm mx-1" wire:click.prevent="holdOrder" style="height: 60px">@lang('text.Continue') <i class="far fa-play-circle"></i></button>

                                @endif
                            @endif
                            @if ($order->hold == 0)
                                 @if ($order->order_status == 'pending')
                                <button class="btn btn-primary btn-sm mx-1" wire:click.prevent="updateOrderStatus" style="height: 60px">@lang('text.Processing') <i class="fas fa-cogs"></i></button>

                                @endif
                                @if ($order->order_status == 'processing')
                                    <button class="btn btn-info btn-sm mx-1" wire:click.prevent="updateOrderStatus" style="height: 60px">@lang('text.Shipping') <i class="fas fa-truck"></i></button>
                                @endif

                                @if ($order->order_status == 'shipping')
                                <button class="btn btn-success btn-sm mx-1" wire:click.prevent="updateOrderStatus" style="height: 60px">@lang('text.Completed') <i class="fas fa-check-circle"></i></button>
                                @endif
                            @endif

                        @endif

                        @if($order->order_status == 'pending')
                            <button class="btn btn-danger btn-sm mx-1" wire:click.prevent="cancel" style="height: 60px">@lang('text.Cancel Order') <i class="fas fa-power-off"></i></button>

                        @endif

                        @if($order->order_status == 'processing' || $order->order_status == 'shipping' || ($order->order_status == 'completed' && $order->updated_at->addDays(10) > now()))
                            @if($order->order_status == 'completed' && $order->updated_at->addDays(10) > now())
                                <p class="text-danger"><i class="text-danger mdi mdi-close-circle"></i> @lang('text.You can not return order after 10 days')</p>

                            @endif
                            <button class="btn btn-danger btn-sm mx-1" wire:click.prevent="cancel" style="height: 60px"><i class="fas fa-reply"></i> @lang('text.Refund order')</button>

                        @endif
                    </div>

                </div>
                    <h4 class="mt-4 mb-3">@lang('text.Receiver Information')</h4>
                    <p class="text-muted text-overflow"><span class="text-danger">@lang('text.Name')</span>: {{$order->receiver_name }}</p>
                    <p class="text-muted text-overflow"><span class="text-danger">@lang('text.Phone Number')</span>: {{$order->receiver_phone}}</p>
                    <p class="text-muted text-overflow"><span class="text-danger">@lang('text.Address')</span>: {{$order->address}}</p>
                    <p class="text-muted text-overflow"><span class="text-danger">@lang('text.Description')</span>: {{$order->description}}</p>



                @endcan



                {{-- items details --}}
                <div class="card-box" style="max-height: 1000px;overflow-y:scroll">
                    <div class="table-responsive">
                        @foreach ( $order->sizes()->withTrashed()->when(auth()->user()->role !='admin',function($q){
                            return $q->join('products','products.id','sizes.product_id')
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
                                <th >@lang('text.Size')</th>

                                @php
                                    $refund=sizes_refund($order->id,$row->id);
                                @endphp

                                @if ($refund)
                                    <th >@lang('text.Refund')</th>
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
                                <td>{{$row->pivot->amount}} @lang('text.SAR')</td>
                                <td>{{$row->pivot->quantity}}</td>
                                <td>{{$row->product()->withTrashed()->first()->taxes()->withTrashed()->sum('tax')."% = ".((($row->pivot->amount*$row->product()->withTrashed()->first()->taxes()->withTrashed()->sum('tax'))/100))}} {{ app()->getLocale() == 'ar' ? 'ريال' : 'SAR' }}</td>
                                <td>{{ $order->sizes()->withTrashed()->where('size_id',$row->id)->get()->pluck('size')->implode(',') }}</td>
                                @if ($refund)
                                <td class="text-danger">
                                    <i class="text-danger mdi mdi-close-circle"></i>{{'('.$refund->quantity .') '. $refund->size.'='. $refund->total_refund_amount}} @lang('text.SAR')
                                </td>
                                @endif
                            </tr>
                            </tbody>
                        </table>
                        @endforeach



                        <br>
                        @if ($order->group_products()->withTrashed()->where('user_id',auth()->user()->id)->count() > 0)
                        <hr>

                            <h3>@lang('text.Group of Products')</h3>
                        @endif
                        @foreach ( $order->group_products()->withTrashed()->when(auth()->user()->role !='admin',function($q){
                            return $q->where('user_id',auth()->user()->id);
                        })->get() as $row)

                            <div class="table-responsive col" >
                                <table class="table table-sm table-borderless mb-0">
                                    <tbody>
                                            <tr>
                                               <th > @lang('text.Store Name')</th>
                                                <th > @lang('text.Product Name')</th>
                                                <th > @lang('text.Price')</th>
                                                <th >@lang('text.Quantity') </th>
                                                <th >@lang('text.Taxes') </th>

                                                @php
                                                    $refund=groups_refund($order->id,$row->id);
                                                @endphp

                                                @if ($refund)
                                                    <th >@lang('text.Refund')</th>
                                                @endif

                                            </tr>
                                            <tr>
                                                <td>{{ $row->user()->withTrashed()->pluck('store_name')->first() }} </td>

                                                <td>{{app()->getLocale() == 'ar' ? $row->name_ar :$row->name_en}}</td>
                                                <td>{{$row->pivot->price}} @lang('text.SAR')</td>
                                                <td>{{$row->pivot->quantity}}</td>
                                                <td>{{$row->taxes()->withTrashed()->sum('tax')."% = ".((($row->pivot->amount*$row->taxes()->withTrashed()->sum('tax'))/100))}} {{ app()->getLocale() == 'ar' ? 'ريال' : 'SAR' }}</td>

                                                @if ($refund)
                                                    <td class="text-danger">
                                                        <i class="text-danger mdi mdi-close-circle"></i>{{ '('.$refund->quantity .') '. $refund->total_refund_amount}} @lang('text.SAR')
                                                    </td>
                                                @endif
                                            </tr>
                                    </tbody>
                                </table>
                            </div>
                            <hr>


                            <div class="table-responsive col" >
                                <table class="table table-sm table-borderless mb-0">
                                        @foreach($order->group_products_sizes()->withTrashed()->get()->groupBy('product_id') as $sizes)
                                            @foreach ($sizes as $size)
                                                @if ($loop->index == 0)
                                                <tr><th>{{app()->getLocale() == 'ar'? $size->product->name_ar : $size->product->name_en }}</th></tr>
                                                @endif
                                                <tr>
                                                    <th class="" scope="row"><strong>@lang('text.Size')</strong></th>
                                                    <td class="text-muted">{{ $size->pivot->size }}</td>
                                                    <th class="" scope="row"><strong>@lang('text.Quantity')</strong></th>
                                                    <td class="text-muted">{{ $size->pivot->quantity }}</td>
                                                </tr>
                                            @endforeach

                                        @endforeach

                                </table>
                            </div>
                        @endforeach



                        <hr style="">
                        <br>
                        <table class="table table-bordered  table-responsive mb-4">
                            <tbody class="table-secondary">
                            <tr>
                                <th > @lang('text.Total Amount')</th>
                                <th > @lang('text.Subtotal')</th>
                                <th> @lang('text.Total Taxes')</th>
                                @can('isAdmin')
                                <th> @lang('text.Shipping')</th>
                                <th> @lang('text.Discount')</th>
                                <th> @lang('text.Total Pieces')</th>

                                @endcan
                            </tr>
                            <tr>
                                <td>
                                    {{
                                        auth()->user()->role =='admin' ?
                                        $order->total_amount : $order->vendors->find(auth()->user()->id)->pivot->total_amount
                                    }} @lang('text.SAR')
                                </td>
                                <td>
                                    {{
                                        auth()->user()->role =='admin' ?
                                        $order->subtotal : $order->vendors->find(auth()->user()->id)->pivot->subtotal
                                    }} @lang('text.SAR')
                                </td>

                                <td>{{ auth()->user()->role =='admin' ?
                                    $order->taxes : $order->vendors->find(auth()->user()->id)->pivot->taxes }}</td>
                                @can('isAdmin')
                                <td>{{ $order->shipping }}</td>
                                <td>{{ $order->discount }}</td>
                                <td>
                                    {{
                                        $order->sizes()->withTrashed()->when(auth()->user()->role !='admin',function($q){
                                            return $q->with(['product' => function($q){
                                                    return $q->withTrashed()->where('user_id',auth()->user()->id);
                                            }]);
                                        })->get()->pluck('pivot')->sum('quantity')
                                    }}
                                </td>
                                @endcan

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
