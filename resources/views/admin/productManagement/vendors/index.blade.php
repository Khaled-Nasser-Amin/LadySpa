@section('title',__('text.Vendors'))
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
                <li class="breadcrumb-item active">{{__('text.Vendors')}}</li>
                <li class="breadcrumb-item active"><a href="{{route('admin.index')}}">{{__('text.Dashboard')}}</a></li>
                <x-slot name="title">
                    <h4 class="page-title">{{__('text.Vendors')}}</h4>
                </x-slot>
            </x-admin.general.page-title>

            @include('admin.partials.success')

            <div class="row">
                <div class="col-12">
                    <input type="text" wire:model="search" class="form-control col-4 my-3 d-inline-block" placeholder="{{__('text.Search')}}...">
                    <select wire:model="status" class="form-control col-4 my-3 d-inline-block">
                        <option value="">@lang('text.Choose User\'s Status')</option>
                        <option value="1">@lang('text.Active')</option>
                        <option value="2">@lang('text.Non Active')</option>
                    </select>
                    <div class=" d-inline-flex px-0 mx-0 col-3 justify-content-end">
                        <button  class="btn btn-primary waves-effect waves-light" data-toggle="modal" data-target="#AddNewVendor">@lang('text.Add New Vendor')</button>
                    </div>

                    <x-admin.vendors.modal-add  />
                    <x-admin.vendors.modal-update  />
                    <div class="table-responsive">
                        <table class="table table-striped table-secondary col-12">
                            <tr>
                                <th>{{__('text.Image')}}</th>
                                <th>{{__('text.Name')}}</th>
                                <th>{{__('text.Store Name')}}</th>
                                <th>{{__('text.Email')}}</th>
                                <th>{{__('text.Location')}}</th>
                                <th>{{__('text.Phone Number')}}</th>
                                <th>{{__('text.Completed Orders')}}</th>
                                <th>{{__('text.Refunds')}}</th>
                                <th>{{__('text.Total Amount')}}</th>
                                <th>{{__('text.Status')}}</th>
                                <th>{{__('text.Upload Product')}}</th>
                                <th>{{__('text.Action')}}</th>
                            </tr>
                            @forelse ($vendors as $vendor)
                                <tr>
                                    <td><a href="{{$vendor->image}}" target="_blank"><img src="{{$vendor->image}}" class="rounded-circle" style="width: 50px;height: 50px" alt="user-image"></a></td>
                                    <td>{{$vendor->name}}</td>
                                    <td>{{$vendor->store_name}}</td>
                                    <td>{{$vendor->email}}</td>
                                    <td>{{$vendor->location}}</td>
                                    <td>{{$vendor->phone}}</td>
                                    <td> {{$vendor->orders->where('payment_status','paid')->count()}} </td>
                                    <td>{{$vendor->refunds->where('refund_status','not refunded yet')->sum('total_refund_amount')}}</td>
                                    <td> {{$vendor->orders->where('payment_status','paid')->sum('pivot.total_amount')}} </td>
                                    <td>{{ $vendor->activation == 0 ? __('text.Non Active'): __('text.Active') }}</td>
                                    <td class="row justify-content-center align-items-center"><input wire:click.prevent="productStatus({{ $vendor->id }})" type="checkbox" {{ $vendor->add_product == 1 ? "checked" : '' }}></td>
                                    <td>
                                        <button  class="btn btn-primary btn-sm waves-effect waves-light" data-toggle="modal" wire:click.prevent="getVendorForUpdate({{ $vendor->id }})" data-target="#EditVendor">@lang('text.Edit')</button>
                                        <button class="btn btn-danger btn-sm" wire:click.prevent="confirmDelete({{$vendor->id}})">{{__('text.Delete')}}</button>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="11" class="text-center">{{__('text.No Data Yet')}}</td></tr>
                            @endforelse

                        </table>
                    </div>



                    {{$vendors->links()}}
                </div>
            </div>


        </div>
    </div>
@push('script')
    <script src="{{asset('libs/sweetalert2/sweetalert2.min.js')}}"></script>
    @livewireScripts

    <script>
        window.livewire.on('vendorCreated', ()=>{
            $('#AddNewVendor').modal('hide');
        } )

        window.livewire.on('vendorUpdated', ()=>{
            $('#EditVendor').modal('hide');
        } )
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

