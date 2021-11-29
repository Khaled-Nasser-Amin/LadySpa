@extends('admin.layouts.appLogged')
@section('title',__('text.Session Details'))
@push('css')
    @livewireStyles
@endpush
@section('content')

    <div class="content">

        <!-- Start Content-->
        <div class="container-fluid">

            <!-- start page title -->
           <x-admin.general.page-title>
               <li class="breadcrumb-item"><a href="/admin/sessions">{{__('text.Sessions')}} </a></li>
               <li class="breadcrumb-item active"> {{__('text.Session Details')}}</li>
                <x-slot name="title">
                    <h4 class="page-title">{{__('text.Session Details')}} </h4>
                </x-slot>
           </x-admin.general.page-title>



            <!-- end page title -->

            <div class="row">
                @livewire('admin.products-management.sessions.session-details',['images' => $images , 'session' => $session])
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
