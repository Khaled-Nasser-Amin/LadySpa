@extends('admin.layouts.appLogged')
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
@section('content')
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
            <hr style="border:1px solid rgb(145, 141, 141)">

            <h5>@lang('text.Single product')</h5>
          @livewire('admin.products-management.refunds.refunds')

          <hr style="border:1px solid rgb(145, 141, 141)">
          <h5>@lang('text.Group products')</h5>

          @livewire('admin.products-management.refunds.refund-groups')

          <hr style="border:1px solid rgb(145, 141, 141)">
          <h5>@lang('text.Reservations')</h5>
          @livewire('admin.products-management.refunds.refunds-reservations')

        </div>
    </div>
@endsection
@push('script')
    <script src="{{asset('libs/sweetalert2/sweetalert2.min.js')}}"></script>
    @livewireScripts

    <script>
        //event fired to livewire called delete
        window.Livewire.on('confirmDelete',function (e) {
            console.log(e)
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

