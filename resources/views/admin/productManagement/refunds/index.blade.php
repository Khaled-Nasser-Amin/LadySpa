@section('title',__('text.Refunds'))
@push('css')
    @livewireStyles
    <link href="{{asset('libs/sweetalert2/sweetalert2.min.css')}}" rel="stylesheet" type="text/css" />

    <style>
        svg{
            width: 20px;
            height: 20px;
        }
    </style>
@endpush
    <div class="content">
        <!-- Start Content-->
        <div class="container-fluid">

            <!-- start page title -->
            <x-admin.general.page-title>
                <li class="breadcrumb-item active">{{__('text.Refunds')}}</li>
                <li class="breadcrumb-item active"><a href="{{route('admin.index')}}">{{__('text.Dashboard')}}</a></li>
                <x-slot name="title">
                    <h4 class="page-title">{{__('text.Refunds')}}</h4>
                </x-slot>
            </x-admin.general.page-title>

            @include('admin.partials.success')

            <div class="row">
                <div class="col-12">
                    <input type="text" wire:model="search" class="form-control col-4 my-3 d-inline-block" placeholder="{{__('text.Search')}}...">
                    <select wire:model="status" class="form-control col-4 my-3 d-inline-block">
                        <option value="">@lang('text.Choose Item\'s Status')</option>
                        <option value="1">@lang('text.Items Returned')</option>
                        <option value="2">@lang('text.Not Returned Yet')</option>
                    </select>
                    <div class="table-responsive">
                        <table class="table table-striped col-12 table-secondary">
                            <tr>
                                <th>{{__('text.Image')}}</th>
                                <th>{{__('text.Order\'s Number')}}</th>
                                <th>{{__('text.Product Name')}}</th>
                                @can('isAdmin')
                                    <th>{{__('text.Store Name')}}</th>
                                @endcan
                                <th>{{__('text.Color')}}</th>
                                <th>{{__('text.Size')}}</th>
                                <th>{{__('text.Quantity')}}</th>
                                <th>{{__('text.Item Status')}}</th>
                                <th>{{__('text.Total Amount')}}</th>
                                <th>{{__('text.Subtotal')}}</th>
                                <th>{{__('text.Taxes')}}</th>
                                @can('isAdmin')
                                <th>{{__('text.Action')}}</th>
                                @endcan
                            </tr>
                            @forelse ($refunds as $refund)
                            @php
                                $color=$refund->size()->withTrashed()->first()->color()->withTrashed()->first();
                                $image=$color->images()->first();
                                $product=$color->product()->withTrashed()->first();
                            @endphp
                                <tr>
                                    <td><a href="{{$image->name}}" target="_blank"><img src="{{$image->name}}" class="rounded-circle" style="width: 50px;height: 50px" alt="user-image"></a></td>
                                    <td>{{$refund->order_id}}</td>
                                    <td>{{app()->getLocale() == 'ar' ? $product->name_ar : $product->name_en}}</td>
                                    @can('isAdmin')
                                    <td>{{$product->user->store_name}}</td>
                                    @endcan
                                    <td>{{$refund->color}}</td>
                                    <td> {{$refund->size}}</td>
                                    <td>{{ $refund->quantity}}</td>
                                    <td>{!! $refund->refund_status != 'not refunded yet' ? '<i class="text-success mdi mdi-checkbox-marked-circle"></i> '.__('text.Items Returned'): '<i class="text-danger mdi mdi-close-circle"></i> '.__('text.Not Returned Yet') !!}</td>
                                    <td> {{$refund->total_refund_amount}}</td>
                                    <td> {{$refund->subtotal_refund_amount}}</td>
                                    <td> {{$refund->taxes}}</td>
                                    @can('isAdmin')
                                    <td>
                                    @if ($refund->refund_status == 'not refunded yet')
                                        <button class="btn btn-info" wire:click.prevent="confirmDelete({{$refund->id}})">{{__('text.Restore')}}</button>
                                    @endif
                                    </td>
                                    @endcan
                                </tr>
                            @empty
                                <tr><td colspan="11" class="text-center">{{__('text.No Data Yet')}}</td></tr>
                            @endforelse

                        </table>
                    </div>

                    {{$refunds->links()}}
                </div>
            </div>


        </div>
    </div>
@push('script')
    <script src="{{asset('libs/sweetalert2/sweetalert2.min.js')}}"></script>
    @livewireScripts

    <script>
        //event fired to livewire called delete
        window.Livewire.on('confirmDelete',function (e) {
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-success mx-2',
                    cancelButton: 'btn btn-danger mx-2'
                },
                buttonsStyling: false
            })
            swalWithBootstrapButtons.fire({
                title: '{{__("text.Are you sure?")}}',
                text: '{{__("text.You won't be able to revert this!")}}',
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: '{{__("text.Yes, delete it!")}}',
                cancelButtonText: '{{__("text.No, cancel!")}}',
                reverseButtons: true
            }).then((result) => {
                if (result.value == true) {
                    window.Livewire.emit('delete',e)
                } else if (
                    /* Read more about handling dismissals below */
                    result.dismiss === Swal.DismissReason.cancel
                ) {
                    swalWithBootstrapButtons.fire(
                        '{{__("text.Cancelled")}}',
                        '{{__("text.Your imaginary file is safe :)")}}',
                        'error'
                    )
                }
            })

        })
    </script>

@endpush

