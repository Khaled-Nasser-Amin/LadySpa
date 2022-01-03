@section('title',__('text.Reservations'))
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
                <li class="breadcrumb-item active">{{__('text.Reservations')}}</li>
                <li class="breadcrumb-item active"><a href="{{route('admin.index')}}">{{__('text.Dashboard')}}</a></li>
                <x-slot name="title">
                    <h4 class="page-title">{{__('text.Reservations')}}</h4>
                </x-slot>
            </x-admin.general.page-title>


            <div class="row mt-5">
                <br>
                    <div class="col-sm-12">
                        <div class="d-flex flex-row flex-wrap justify-content-center">
                            <div class="form-group col-md-4 col-sm-12">
                                <label for="payment_status">@lang('text.Search')</label>
                                <input type="text" class="form-control " placeholder="@lang('text.Search')" wire:model="search">
                            </div>
                            <div class="form-group col-md-4 col-sm-12">
                                <label for="payment_status">@lang('text.Payment Status')</label>
                                <select wire:model="payment_status" id="payment_status" class="form-control">
                                    <option value=""></option>
                                    <option value="paid">@lang('text.Paid')</option>
                                    <option value="unpaid">@lang('text.Unpaid')</option>
                                    <option value="failed">@lang('text.Failed')</option>
                                </select>
                            </div>
                            <div class="form-group col-md-4 col-sm-12">
                                <label for="reservation_status">@lang('text.Reservation status')</label>
                                <select wire:model="reservation_status" id="reservation_status" class="form-control">
                                    <option value=""></option>
                                    <option value="pending">@lang('text.Pending')</option>
                                    <option value="processing">@lang('text.Processing')</option>
                                    <option value="completed">@lang('text.Completed')</option>
                                    <option value="canceled">@lang('text.Canceled')</option>
                                </select>

                            </div>
                            <div class="form-group col-md-4 col-sm-12">
                                <label for="payment_way">@lang('text.Payment Way')</label>
                                <select wire:model="payment_way"  id="payment_way" class="form-control">
                                    <option value=""></option>
                                    <option value="cash on delivery">@lang('text.Cash on delivery')</option>
                                    <option value="online payment">@lang('text.Online Payment')</option>
                                </select>
                            </div>
                            <div class="form-group col-md-4 col-sm-12">
                                <label for="date">@lang('text.Date')</label>
                                <input type="date" class="form-control " placeholder="@lang('text.Date')" wire:model="date">
                            </div>
                        </div>


                        <div class="table-responsive">
                             <table class="table table-striped table-secondary col-12 " style="overflow-x:auto!important">

                            <tr>
                                <th>{{__('text.Reservation\'s Number')}}</th>
                                <th>{{__('text.Receiver\'s Name')}}</th>
                                <th>{{__('text.Store Name')}}</th>
                                <th>{{__('text.Payment Way')}}</th>

                                <th>{{__('text.Reservation status')}}</th>
                                <th>{{__('text.Payment Status')}}</th>

                                <th>{{__('text.Total Amount')}}</th>
                                <th>{{__('text.Subtotal')}}</th>
                                <th>{{__('text.Discount')}}</th>
                                <th>{{__('text.Action')}}</th>
                            </tr>
                            @forelse ($reservations as $reservation)
                                <tr>
                                    <td>{{$reservation->id}}</td>
                                    <td>{{$reservation->receiver_name}}</td>
                                    <td>{{$reservation->vendor->store_name}}</td>
                                    <td>{{__('text.'.ucfirst($reservation->payment_way))}}</td>
                                    <td>{{__('text.'.ucfirst($reservation->reservation_status))}}</td>
                                    <td>
                                        @if ($reservation->payment_status == 'paid')
                                            <i class="text-success mdi mdi-checkbox-marked-circle"></i>
                                        @elseif ($reservation->payment_status == 'failed')
                                            <i class="text-danger mdi mdi-close-circle"></i>
                                        @elseif ($reservation->payment_status == 'unpaid')
                                            <i class="text-warning mdi mdi-dots-horizontal-circle"></i>
                                        @endif
                                        {{ __('text.'.ucfirst($reservation->payment_status))}}
                                    </td>
                                    <td> {{ $reservation->total_amount }}</td>
                                    <td> {{ $reservation->subtotal }}</td>
                                    <td> {{ $reservation->discount }}</td>

                                    <td>
                                        @can('show-reservation',$reservation)
                                            <a href="{{ route('reservation.show',$reservation->id) }}" class="btn btn-info text-center">@lang('text.Show' ) <i class="mdi px-1 mdi-eye text-dark" ></i></a>
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="9" class="text-center">{{__('text.No Data Yet')}}</td></tr>
                            @endforelse

                        </table>
                        </div>
                        {{$reservations->links()}}
                    </div>
                <br>
            </div>


        </div>
    </div>
@push('script')
    @livewireScripts


@endpush

