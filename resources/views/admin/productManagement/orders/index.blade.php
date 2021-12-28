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
                                    <th>{{__('text.Receiver\'s Name')}}</th>
                                    <th>{{__('text.Payment Way')}}</th>


                                @endcan
                                <th>{{__('text.Order status')}}</th>
                                <th>{{__('text.Payment Status')}}</th>

                                <th>{{__('text.Total Amount')}}</th>
                                <th>{{__('text.Subtotal')}}</th>
                                @can('isAdmin')
                                <th>{{__('text.Discount')}}</th>

                                @endcan
                                <th>{{__('text.Action')}}</th>
                            </tr>
                            @forelse ($orders as $order)
                                <tr>
                                    <td>{{$order->id}}</td>
                                    @can('isAdmin')

                                    <td>{{$order->receiver_name}}</td>
                                    <td>{{__('text.'.ucfirst($order->payment_way))}}</td>
                                    @endcan
                                    <td>
                                        @if ($order->hold == 0)

                                        {{__('text.'.ucfirst($order->order_status))}}
                                        @else
                                        <i class="text-danger far fa-pause-circle"></i> @lang('text.Hold')
                                        @endif
                                    </td>
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
                                    <td> {{ $order->discount }}</td>
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

