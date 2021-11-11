@extends('admin.layouts.appLogged')
@section('title','Update Product')
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
                <li class="breadcrumb-item"><a href="/admin/products">{{__('text.Products')}}</a></li>
                <li class="breadcrumb-item active"> {{__('text.Edit Product')}}</li>
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
                        @livewire('admin.products-management.products.product-form',['action' => 'update('.$product->id.')','product' => $product])
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
         window.Livewire.on('addColor',()=>{
            $('#colorsAndPrices').modal('hide');
        })
         window.Livewire.on('updateColor',()=>{
            $('#updateColorsAndPrices').modal('hide');
        })
        window.Livewire.on('addSize',e=>{
            $('#sizeAndStock'+e).modal('hide');
        })
        window.Livewire.on('refreshMultiSelect',()=>{
            $('#my_multi_select1').multiSelect('refresh');
        })

        window.Livewire.on('updateSize',e=>{
            $('#updateSizeAndStock'+e).modal('hide');
        })
        $('#my_multi_select1').multiSelect();

        window.addEventListener('success',e=>{
            $.Toast(e.detail," ",'success',{
                stack: false,
                position_class: "toast-top-center",
                rtl: {{app()->getLocale()=='ar' ? "true" : 'false'}}
            });
        })

        //fire event to get product by id
        $(window).on('load',function (){
            window.Livewire.emit('edit')

        })
    </script>


@endpush
