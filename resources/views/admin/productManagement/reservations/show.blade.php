@extends('admin.layouts.appLogged')
@section('title',__('text.Reservation Show'))
@push('css')
 @livewireStyles
    <link href="{{asset('libs/sweetalert2/sweetalert2.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('css/style.css')}}"rel="stylesheet"type="text/css"/>
    <link rel="stylesheet" href="{{asset('css/toast.style.min.css')}}">

    <style>
        svg{
            width: 20px;
            height: 20px;
        }
        .dropdown-menu.show{
            left:0!important;
        }
    </style>
@endpush
@section('content')

<div class="content">

    <!-- Start Content-->
    <div class="container-fluid">

      <!-- start page title -->
      <x-admin.general.page-title>
        <li class="breadcrumb-item active">{{ __('text.Reservation Show') }}</li>
        <li class="breadcrumb-item"><a href="{{route('admin.reservations')}}">{{__('text.Reservation')}}</a></li>
        <li class="breadcrumb-item"><a href="{{route('admin.index')}}">{{__('text.Dashboard')}}</a></li>
        <x-slot name="title">
            <h4 class="page-title">{{__('text.Reservation Show')}}</h4>
        </x-slot>
    </x-admin.general.page-title>

    @livewire('admin.products-management.reservations.reservation-details',['reservation'=>$reservation])

    </div>
    <!-- end container-fluid -->

</div>
<!-- end content -->
@endsection
@push('script')
<script src="{{asset('js/toast.script.js')}}"></script>
<script>
    window.addEventListener('success',e=>{
        $.Toast(e.detail,"",'success',{
            stack: false,
            position_class: "toast-top-center",
            rtl: {{app()->getLocale()=='ar' ? "true" : 'false'}}
        });

    })

    window.addEventListener('error',e=>{
        $.Toast(e.detail,"",'error',{
            stack: false,
            position_class: "toast-top-center",
            rtl: {{app()->getLocale()=='ar' ? "true" : 'false'}}
        });
    })


</script>
<script src="{{asset('libs/sweetalert2/sweetalert2.min.js')}}"></script>
@livewireScripts
<script>
    window.Livewire.on('saveTime',()=>{
        $('#modify_reservation').modal('hide');
    })
    //event fired to livewire called delete
    window.Livewire.on('confirmCancel',function (e) {
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
            confirmButtonText: '{{__("text.Yes")}}',
            cancelButtonText: '{{__("text.No")}}',
            reverseButtons: true
        }).then((result) => {
            if (result.value == true) {
                window.Livewire.emit('cancelReservation',e)
            } else if (
                /* Read more about handling dismissals below */
                result.dismiss === Swal.DismissReason.cancel
            ) {
                swalWithBootstrapButtons.fire(
                    '',
                    '{{__("text.Order is safe :)")}}',
                    'error'
                )
            }
        })

    })
</script>
@endpush
