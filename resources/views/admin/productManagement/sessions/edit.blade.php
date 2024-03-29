@extends('admin.layouts.appLogged')
@section('title',__('text.Update Session'))
@push('css')
    <link rel="stylesheet" href="{{asset('css/toast.style.min.css')}}">
    @livewireStyles
    <link href="{{asset('libs/multiselect/multi-select.css')}}" rel="stylesheet" type="text/css" />
    <style>
        .ms-container .ms-list{
            border-color: rgb(175, 175, 175)!important;
        }
    </style>
@endpush
@section('content')
    <div class="content">

        <!-- Start Content-->
        <div class="container-fluid">

            <!-- start page title -->
            <x-admin.general.page-title>
                <li class="breadcrumb-item"><a href="/admin/sessions">{{__('text.Sessions')}}</a></li>
                <li class="breadcrumb-item active"> {{__('text.Update Session')}}</li>
                <x-slot name="title">
                    <h4 class="page-title">{{__('text.Edit')}} </h4>
                </x-slot>
            </x-admin.general.page-title>
            <!-- end page title -->
            @include('admin.partials.success')
            <div class="row">
                <div class="col-md-12">
                    <div class="card-box">
                        <h4 class="header-title mt-0">{{__('text.Fill in the Form')}}</h4>
                        @livewire('admin.products-management.sessions.session-form',['action' => 'update('.$session->id.')','session' => $session])
                    </div>
                    <!-- end card-box -->
                </div>
                <!-- end col -->
            </div>
            <!-- end row -->

        </div>
        <!-- end container-fluid -->

    </div>
    <!-- end content -->
@endsection
@push('script')
    <script src="{{asset('js/toast.script.js')}}"></script>
    <script src="{{asset('libs/multiselect/jquery.multi-select.js')}}"></script>
    <script src="{{asset('libs/bootstrap-touchspin/jquery.bootstrap-touchspin.min.js')}}"></script>
    @livewireScripts
    <script>
          window.Livewire.on('addedAllAdditions',()=>{
            $('#AddAdditions').modal('hide');
        })

        window.Livewire.on('addAddition',e=>{
            $('#addNewAddition').modal('hide');
        })

        window.Livewire.on('updateAddition',e=>{
            $('#updateAddition').modal('hide');
        })
        window.Livewire.on('refreshMultiSelect',()=>{
            $('#my_multi_select1').multiSelect('refresh');
        })
        $('#my_multi_select1').multiSelect();

        window.addEventListener('success',e=>{
            $.Toast(e.detail," ",'success',{
                stack: false,
                position_class: "toast-top-center",
                rtl: {{app()->getLocale()=='ar' ? "true" : 'false'}}
            });
            $('#my_multi_select1').multiSelect('refresh');
        })

        //fire event to get product by id
        $(window).on('load',function (){
            window.Livewire.emit('edit')

        })
    </script>


@endpush
