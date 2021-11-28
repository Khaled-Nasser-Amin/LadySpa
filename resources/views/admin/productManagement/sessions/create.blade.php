@extends('admin.layouts.appLogged')
@section('title',__('text.Add Session'))
@push('css')
    <link rel="stylesheet" href="{{asset('css/toast.style.min.css')}}">
    @livewireStyles
<style>
    .ms-container .ms-list{
        border-color: rgb(175, 175, 175)!important;
    }
</style>
    <link href="{{asset('libs/multiselect/multi-select.css')}}" rel="stylesheet" type="text/css" />
@endpush
@section('content')

    <div class="content">

        <!-- Start Content-->
        <div class="container-fluid">

            <!-- start page title -->
           <x-admin.general.page-title>
               <li class="breadcrumb-item"><a href="/admin/sessions">{{__('text.Sessions')}} </a></li>
               <li class="breadcrumb-item active"> {{__('text.Add Session')}}</li>
                <x-slot name="title">
                    <h4 class="page-title">{{__('text.Add New Session')}} </h4>
                </x-slot>
           </x-admin.general.page-title>



            <!-- end page title -->

            <div class="row">
                <div class="col-md-12">
                    <div class="card-box">
                        <h4 class="header-title mt-0">{{__('text.Fill in the Form')}}</h4>
                        @livewire('admin.products-management.sessions.session-form',['action' => 'store'])

                        <!-- end form -->
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



    <!-- App js -->

    @livewireScripts
    <script>
         window.Livewire.on('addedAllAdditions',()=>{
            $('#AddAdditions').modal('hide');
        })


        window.Livewire.on('changeType',()=>{
            $('#my_multi_select1').multiSelect('refresh');
        })

        window.Livewire.on('addAddition',e=>{
            $('#addNewAddition').modal('hide');
        })

        window.Livewire.on('updateAddition',e=>{
            $('#updateAddition').modal('hide');
        })
        $('#my_multi_select1').multiSelect();

        window.addEventListener('success', e =>{
            $.Toast(e.detail,"",'success',{
                stack: false,
                position_class: "toast-top-center",
                rtl: {{app()->getLocale()=='ar' ? "true" : 'false'}}

            });
            $('#my_multi_select1').multiSelect('refresh');

        })
    </script>
@endpush
