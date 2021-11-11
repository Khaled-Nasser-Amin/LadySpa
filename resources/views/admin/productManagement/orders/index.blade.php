@section('title',__('text.Orders'))
@push('css')
    @livewireStyles

    <style>
        svg{
            width: 20px;
            height: 20px;
        }
    </style>
@endpush
    <div class="content">
        <!-- Start Content-->
        <div class="container-fluid pt-2">

            <!-- start page title -->
            <x-admin.general.page-title>
                <li class="breadcrumb-item active">{{__('text.Orders')}}</li>
                <li class="breadcrumb-item active"><a href="{{route('admin.index')}}">{{__('text.Dashboard')}}</a></li>
                <x-slot name="title">
                    <h4 class="page-title">{{__('text.Orders')}}</h4>
                </x-slot>
            </x-admin.general.page-title>

            @include('admin.partials.success')

            <div class="row mt-5">
                <br>
                    <div class="col-sm-12">
                        <div class="row">
                            <div class="form-group col-md-3 col-sm-12">
                                <label for="payment_status">@lang('text.Search')</label>
                                <input type="text" class="form-control " placeholder="@lang('text.Search')" wire:model="search">
                            </div>
                            <div class="form-group col-md-3 col-sm-12">
                                <label for="payment_status">@lang('text.Payment Status')</label>
                                <select wire:model="payment_status" id="payment_status" class="form-control">
                                    <option value=""></option>
                                    <option value="paid">@lang('text.Paid')</option>
                                    <option value="unpaid">@lang('text.Unpaid')</option>
                                    <option value="failed">@lang('text.Failed')</option>
                                </select>
                            </div>
                            <div class="form-group col-md-3 col-sm-12">
                                <label for="order_status">@lang('text.Order status')</label>
                                <select wire:model="order_status" id="order_status" class="form-control">
                                    <option value=""></option>
                                    <option value="pending">@lang('text.Pending')</option>
                                    <option value="processing">@lang('text.Processing')</option>
                                    <option value="collected">@lang('text.Collected')</option>
                                    <option value="modified">@lang('text.Modified')</option>
                                    <option value="completed">@lang('text.Completed')</option>
                                    <option value="canceled">@lang('text.Canceled')</option>
                                </select>

                            </div>
                            <div class="form-group col-md-3 col-sm-12">
                                <label for="payment_way">@lang('text.Payment Way')</label>
                                <select wire:model="payment_way"  id="payment_way" class="form-control">
                                    <option value=""></option>
                                    <option value="cash on delivery">@lang('text.Cash on delivery')</option>
                                    <option value="online payment">@lang('text.Online Payment')</option>
                                </select>


                            </div>
                        </div>





                        <div class="table-responsive">
                             <table class="table table-striped table-secondary col-12 " style="overflow-x:auto!important">

                            <tr>
                                <th>{{__('text.Order\'s Number')}}</th>
                                @can('isAdmin')
                                    <th>{{__('text.Assign To Delivery Provider')}}</th>
                                    <th>{{__('text.Receiver\'s Name')}}</th>
                                    <th>{{__('text.Payment Way')}}</th>


                                @endcan
                                <th>{{__('text.Order status')}}</th>
                                <th>{{__('text.Payment Status')}}</th>

                                <th>{{__('text.Total Amount')}}</th>
                                <th>{{__('text.Subtotal')}}</th>
                                <th>{{__('text.Action')}}</th>
                            </tr>
                            @forelse ($orders as $order)
                                <tr>
                                    <td>{{$order->id}}</td>
                                    @can('isAdmin')
                                    <td>
                                        @if(($order->order_status == 'pending' && $order->payment_way == 'cash on delivery') || ($order->payment_way == 'online payment' && $order->payment_status == 'paid' && $order->order_status == 'pending'))

                                            <div class="dropdown">
                                                <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" wire:key="{{ $loop->index }}">
                                                    @if ($order->delivery_service_provider_id)
                                                        <img src="{{ $order->delivery_service_provider->image }}" class="rounded-circle" style="width: 50px;height: 50px" alt="delivery-image">

                                                        <span>{{ $order->delivery_service_provider->name }}</span>
                                                    @else
                                                        @lang('text.Select Delivery Provider')
                                                    @endif
                                                </button>
                                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton" style="height: 200px;overflow-y:auto;">
                                                    @if ($delivery_service_providers->count() > 0)
                                                        @foreach ($delivery_service_providers as $delivery )
                                                        <a class="dropdown-item" href="#" wire:click.prevent="assignOrderToDelivery({{ $delivery->id }},{{ $order->id }})">
                                                            <img src="{{ $delivery->image }}" class="rounded-circle" style="width: 50px;height: 50px" alt="delivery-image">
                                                            <span>{{ $delivery->name }}</span>
                                                        </a>
                                                        @endforeach
                                                        @if ($order->delivery_service_provider_id)
                                                            <a class="dropdown-item bg-soft-dark" href="#" wire:click.prevent="cancelOrderToDelivery({{ $order->id }})">
                                                                <span>@lang('text.Stop Delivery')</span>
                                                            </a>
                                                        @endif



                                                    @else
                                                    <span class="text-muted">@lang('text.No Data Yet')</span>
                                                    @endif

                                                </div>

                                            </div>

                                        @elseif ($order->order_status != 'pending')
                                        <div class="d-flex flex-row  align-items-center">
                                            @if ($order->delivery_service_provider_id)
                                            <a href="{{ $order->delivery_service_provider->image }}" target="_blanck">
                                                <img src="{{ $order->delivery_service_provider->image }}" class="rounded-circle" style="width: 50px;height: 50px" alt="delivery-image">
                                            </a>
                                            <span>{{ $order->delivery_service_provider->name }}</span>
                                            @endif
                                        </div>
                                        @endif
                                    </td>
                                    <td>{{$order->receiver_first_name ." ". $order->receiver_last_name}}</td>
                                    <td>{{__('text.'.ucfirst($order->payment_way))}}</td>
                                    @endcan
                                    <td>{{__('text.'.ucfirst($order->order_status))}}</td>
                                    <td>
                                        @if ($order->payment_status == 'paid')
                                            <i class="text-success mdi mdi-checkbox-marked-circle"></i>
                                        @elseif ($order->payment_status == 'failed')
                                            <i class="text-danger mdi mdi-close-circle"></i>
                                        @elseif ($order->payment_status == 'unpaid')
                                            <i class="text-warning mdi mdi-dots-horizontal-circle"></i>
                                        @endif
                                        {{ __('text.'.ucfirst($order->payment_status))}}
                                    </td>
                                    @can('isAdmin')
                                    <td> {{ $order->total_amount }}</td>
                                    <td> {{ $order->subtotal }}</td>
                                    @endcan
                                    @cannot('isAdmin')
                                    <td> {{ $order->vendors->find(auth()->user()->id)->pivot->total_amount }}</td>
                                    <td> {{ $order->vendors->find(auth()->user()->id)->pivot->subtotal }}</td>
                                    @endcannot
                                    <td>
                                        <a href="{{ route('order.show',$order->id) }}" class="btn btn-info d-flex">@lang('text.Show' ) <i class="mdi px-1 mdi-eye text-dark" ></i></a>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="9" class="text-center">{{__('text.No Data Yet')}}</td></tr>
                            @endforelse

                        </table>
                        </div>
                        {{$orders->links()}}
                    </div>
                <br>
            </div>


        </div>
    </div>
@push('script')
    @livewireScripts
    <script>
        $('.table-responsive').on('show.bs.dropdown', function () {
            $('.table-responsive').css( "overflow", "inherit" );
        });

        $('.table-responsive').on('hide.bs.dropdown', function () {
            $('.table-responsive').css( "overflow", "auto" );
        })
    </script>

@endpush

