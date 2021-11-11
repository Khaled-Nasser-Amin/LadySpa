@extends('admin.layouts.appLogged')
@section('title','Product Details')
@push('css')
    @livewireStyles
@endpush
@section('content')

    <div class="content">

        <!-- Start Content-->
        <div class="container-fluid">

            <!-- start page title -->
           <x-admin.general.page-title>
               <li class="breadcrumb-item"><a href="/admin/products">{{__('text.Products')}} </a></li>
               <li class="breadcrumb-item active"> {{__('text.Product Details')}}</li>
                <x-slot name="title">
                    <h4 class="page-title">{{__('text.Product Details')}} </h4>
                </x-slot>
           </x-admin.general.page-title>



            <!-- end page title -->

            <div class="row">
                @livewire('admin.products-management.products.product-details',['images' => $images , 'product' => $product])
            </div>
            <!-- end row -->

        </div>
        <!-- end container-fluid -->

    </div>
    <!-- end content -->
@endsection
@push('script')
    @livewireScripts


@endpush
