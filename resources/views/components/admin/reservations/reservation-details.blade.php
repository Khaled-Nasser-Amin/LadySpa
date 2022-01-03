<div class="property-detail-wrapper">
    <div class="row">
        <div class="col-lg-8">


            {{-- session images  --}}
            @php
                $session=$reservation->session()->withTrashed()->first();
            @endphp
            <div class="">
                <div id="carouselExampleIndicators"  class="carousel slide" data-ride="carousel">
                    <ol class="carousel-indicators">
                        <li data-target="#carouselExampleIndicators" data-slide-to="0" class="active"></li>

                        @for ($i =1; $i < $session->images->count(); $i++)
                        <li data-target="#carouselExampleIndicators" data-slide-to="{{$i}}" class="{{$i == 0 ? 'active' : ''}}"></li>
                        @endfor
                    </ol>
                    <div class="carousel-inner">
                        <div class="carousel-item  active">
                            <img class="d-block w-100" style="height: 500px" src="{{ $session->image }}" alt="Second slide">
                        </div>
                        @foreach($session->images as $image)
                        <div class="carousel-item ">
                            <img class="d-block w-100" style="height: 500px" src="{{$image->name}}" alt="Second slide">
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

            {{-- session name --}}
            <div class="mt-4">
                <h4>
                    {{app()->getLocale() == 'ar' ? $session->name_ar: $session->name_en}}
                </h4>

                {{-- reservation details --}}

                <div class="d-flex flex-row flex-wrap">
                    <div class="col-md-6 col col-sm-12">
                        <h4 class="mt-4 mb-3">@lang('text.Payment Information')</h4>
                        <p class="text-muted text-overflow"><span class="text-danger">@lang('text.Payment Way')</span>: {{__('text.'.ucfirst($reservation->payment_way))}}</p>
                        <p class="text-muted text-overflow"><span class="text-danger">@lang('text.Payment Status')</span>:
                            @if ($reservation->payment_status == 'paid')
                                <i class="text-success mdi mdi-checkbox-marked-circle"></i>
                            @elseif ($reservation->payment_status == 'failed')
                                <i class="text-danger mdi mdi-close-circle"></i>
                            @elseif ($reservation->payment_status == 'unpaid')
                                <i class="text-warning mdi mdi-dots-horizontal-circle"></i>
                            @endif
                            {{__('text.'.ucfirst($reservation->payment_status))}}
                        </p>

                        <p class="text-muted text-overflow"><span class="text-danger">@lang('text.Reservation status')</span>:
                            @if ($reservation->reservation_status == 'pending')
                                <i class="far fa-pause-circle"></i>
                            @elseif ($reservation->reservation_status == 'completed')
                                <i class="text-success fas fa-check-circle"></i>
                            @elseif ($reservation->reservation_status == 'canceled')
                            <i class="text-danger mdi mdi-close-circle"></i>
                            @elseif ($reservation->reservation_status == 'refund')
                            <i class="fas fa-reply text-danger"></i>
                            @endif
                            {{__('text.'.ucfirst($reservation->reservation_status))}}

                        </p>
                    </div>
                    <div class="col-md-6 col-sm-12 py-4 d-flex flex-row justify-content-center">
                        @if ($reservation->reservation_status == 'pending')
                            @if (now() >= date('Y-m-d H:i:s',strtotime($first_reservation->date.' '.$first_reservation->end_time)) )
                                <button class="btn btn-success btn-sm mx-1" wire:click.prevent="updateReservationStatus" style="height: 60px">@lang('text.Completed') <i class="fas fa-check-circle"></i></button>
                            @endif
                            @if ($reservation->payment_way == 'cash on delivery')
                                <button class="btn btn-danger btn-sm mx-1" wire:click.prevent="cancel(1)" style="height: 60px">@lang('text.Cancel Reservation') <i class="fas fa-power-off"></i></button>
                            @endif
                            @if ($reservation->payment_way == 'online payment')
                                <button class="btn btn-danger btn-sm mx-1" wire:click.prevent="cancel(2)" style="height: 60px"><i class="fas fa-reply"></i> @lang('text.Refund')</button>
                            @endif

                        @endif
                    </div>
                </div>








                <h4 class="mt-4 mb-3">@lang('text.Receiver Information')</h4>
                <p class="text-muted text-overflow"><span class="text-danger">@lang('text.Reservation type')</span>: {{$reservation->type }}</p>
                <p class="text-muted text-overflow"><span class="text-danger">@lang('text.Name')</span>: {{$reservation->receiver_name }}</p>
                <p class="text-muted text-overflow"><span class="text-danger">@lang('text.Phone Number')</span>: {{$reservation->receiver_phone}}</p>
                <p class="text-muted text-overflow"><span class="text-danger">@lang('text.Address')</span>: {{$reservation->address}}</p>
                <p class="text-muted text-overflow"><span class="text-danger">@lang('text.Description')</span>: {{$reservation->description}}</p>






                {{-- items details --}}
                <div class="card-box" style="max-height: 1000px;overflow-y:scroll">
                    <div class="table-responsive">
                        @foreach ( $reservation->times()->get() as $row)
                        <table class="table table-bordered table-secondary  mb-4">
                            <tbody>
                            <tr>
                                <th > @lang('text.Date')</th>
                                <th > @lang('text.Start time')</th>
                                <th >@lang('text.End time') </th>
                                <th >@lang('text.Action') </th>

                                {{--  @php
                                    $refund=sizes_refund($order->id,$row->id);
                                @endphp  --}}

                                {{--  @if ($refund)
                                    <th >@lang('text.Refund')</th>
                                @endif  --}}
                            </tr>
                            <tr>
                                <td>{{ $row->date }} </td>
                                <td>{{date('h:i a',strtotime($row->start_time))}}</td>
                                <td>{{date('h:i a',strtotime($row->end_time))}}</td>
                                <td class="text-center">
                                    @if (now() < date('Y-m-d H:i:s',strtotime($row->date.' '.$row->start_time)) && $reservation->reservation_status == 'pending')
                                    <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#modify_reservation" wire:click.prevent="edit({{ $row->id }})">
                                        <i class="fas fa-edit text-white"></i>
                                    </button>
                                    <x-admin.reservations.modify-reservation  type="{{ $reservation->type }}" code="{{ $code }}" :rooms="$rooms" :datetime="$date_time" :date="$date"/>
                                    @endif
                                </td>
                                {{--  @if ($refund)
                                <td class="text-danger">
                                    <i class="text-danger mdi mdi-close-circle"></i>{{'('.$refund->quantity .') '. $refund->size.'='. $refund->total_refund_amount}} @lang('text.SAR')
                                </td>
                                @endif  --}}
                            </tr>
                            </tbody>
                        </table>
                        @endforeach



                        <br>
                        @if ($reservation->additions()->withTrashed()->get()->count() > 0)
                        <hr>
                            <h3>@lang('text.Additions')</h3>
                        @endif
                        @foreach ( $reservation->additions()->withTrashed()->get() as $row)

                            <div class="table-responsive col" >
                                <table class="table table-sm table-borderless mb-0">
                                    <tbody>
                                            <tr>
                                               <th > @lang('text.Addition Name')</th>
                                                <th > @lang('text.Price')</th>
                                                {{--  @php
                                                    $refund=groups_refund($order->id,$row->id);
                                                @endphp

                                                @if ($refund)
                                                    <th >@lang('text.Refund')</th>
                                                @endif  --}}

                                            </tr>
                                            <tr>
                                                <td>{{app()->getLocale() == 'ar' ? $row->pivot->name_ar :$row->pivot->name_en}}</td>
                                                <td>{{$row->pivot->price}} @lang('text.SAR')</td>

                                                {{--  @if ($refund)
                                                    <td class="text-danger">
                                                        <i class="text-danger mdi mdi-close-circle"></i>{{ '('.$refund->quantity .') '. $refund->total_refund_amount}} @lang('text.SAR')
                                                    </td>
                                                @endif  --}}
                                            </tr>
                                    </tbody>
                                </table>
                            </div>
                            <hr>
                        @endforeach



                        <hr style="">
                        <br>
                        <table class="table table-bordered  table-responsive mb-4">
                            <tbody class="table-secondary">
                            <tr>
                                <th > @lang('text.Total Amount')</th>
                                <th > @lang('text.Subtotal')</th>
                                <th> @lang('text.Total Taxes')</th>
                                @if ($reservation->type == 'outdoor')

                                <th> @lang('text.Shipping')</th>
                                @endif
                                <th> @lang('text.Discount')</th>
                            </tr>
                            <tr>
                                <td>{{$reservation->total_amount}} @lang('text.SAR')</td>
                                <td>
                                    {{ $reservation->subtotal }} @lang('text.SAR')
                                </td>

                                <td>{{ $reservation->taxes }}</td>
                                @if ($reservation->type == 'outdoor')
                                <td>{{ $reservation->shipping }}</td>

                                @endif

                                <td>{{ $reservation->discount }}</td>

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
                <div class="text-center card-box">
                    <div class="text-left">
                        <h4 class="header-title mb-4">@lang('text.User')</h4>
                    </div>
                    <div class="member-card">
                        <div class="avatar-xl member-thumb mb-2 mx-auto d-block">
                            <img src="{{$reservation->customer()->withTrashed()->first()->image }}" class="rounded-circle img-thumbnail" alt="profile-image">
                            <i class="mdi mdi-star-circle member-star text-success" title="Featured Agent"></i>
                        </div>

                        <div class="">
                            <h5 class="font-18 mb-1">{{$reservation->customer()->withTrashed()->first()->name}}</h5>
                        </div>

                        <div class="mt-20">
                            <ul class="list-inline row">
                                <li class="list-inline-item col-12 mx-0">
                                    <h5>@lang('text.Email')</h5>
                                    <p>{{ $reservation->customer()->withTrashed()->first()->email }}</p>
                                </li>
                                <li class="list-inline-item col-6 mx-0">
                                    <h5>@lang('text.Completed Reservations')</h5>
                                    <p>{{$reservation->customer()->withTrashed()->first()->reservations()->where('reservation_status','completed')->count()}}</p>
                                </li>

                                <li class="list-inline-item col-6 mx-0">
                                    <h5>@lang('text.Phone Number')</h5>
                                    <p>{{ $reservation->customer()->withTrashed()->first()->phone }}</p>
                                </li>

                            </ul>
                        </div>

                    </div>
                    <!-- end membar card -->
                </div>
                @if ($reservation->type == 'outdoor')
                <div class="text-center row ">
                    <div class="text-left col-12">
                        <h4 class="header-title mb-4">@lang('text.Location')</h4>

                    </div>
                    <div class="mapouter col-12">
                        <div class="gmap_canvas">
                            <iframe style="width:100%!important"  height="500" id="gmap_canvas" src="https://maps.google.com/maps?center=45.468889,9.202216&q={{ $reservation->lat_long }}&t=&z=13&ie=UTF8&iwloc=&output=embed" frameborder="0" scrolling="no" marginheight="0" marginwidth="0"></iframe>
                            <a href="https://kissanime-ws.com"></a>
                            <br>
                            <style>.mapouter{position:relative;text-align:right;height:500px;width:400px;}</style>
                            <a href="https://www.embedgooglemap.net">how to get google map embed code</a>
                            <style>.gmap_canvas {overflow:hidden;background:none!important;height:500px;width:400px;}</style>
                        </div>
                    </div>
                </div>
                @endif


            <!-- end card-box -->

        </div>

        <!-- end col -->
    </div>
    <!-- end row -->
</div>
