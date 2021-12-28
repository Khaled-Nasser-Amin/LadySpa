@section('title',__('text.Recycle Bin'))
@push('css')
    @livewireStyles
    <link href="{{asset('libs/sweetalert2/sweetalert2.min.css')}}" rel="stylesheet" type="text/css" />
    <style>
        svg{
            width: 20px;
            height: 20px;
        }
    </style>
        <link href="{{asset('css/style.css')}}"rel="stylesheet"type="text/css"/>

@endpush
    <div class="content">
        <!-- Start Content-->
        <div class="container-fluid pt-2">

            <!-- start page title -->
            <x-admin.general.page-title>
                <li class="breadcrumb-item active">{{__('text.Recycle Bin')}}</li>
                <li class="breadcrumb-item active"><a href="{{route('admin.index')}}">{{__('text.Dashboard')}}</a></li>
                <x-slot name="title">
                    <h4 class="page-title">{{__('text.Recycle Bin')}}</h4>
                </x-slot>
            </x-admin.general.page-title>



            <div class="row mt-5 justify-content-center">
                <div class="form-group row mx-0 px-0">
                    <label for="select_bin col-12">@lang('text.Select Category')</label>
                    <select id="select_bin" class="form-control col-12" wire:model="select">
                        <option value="Products">@lang('text.Products')</option>
                        <option value="Sessions">@lang('text.Sessions')</option>
                        @can('isAdmin')
                            <option value="Vendors">@lang('text.Vendors')</option>
                            <option value="Users">@lang('text.Users')</option>
                            <option value="Taxes">@lang('text.Taxes')</option>
                        @endcan


                    </select>
                </div>
            </div>
            @if($select  == 'Products')
                @livewire('admin.products-management.recycle-bin.recycle-products')
            @endif

            @if($select  == 'Sessions')
                @livewire('admin.products-management.recycle-bin.recycle-sessions')
            @endif
            @if($select  == 'Vendors')
                @livewire('admin.products-management.recycle-bin.recycle-vendors')

            @elseif($select  == 'Users')
                @livewire('admin.products-management.recycle-bin.recycle-customers')
            @elseif($select  == 'Taxes')
                @livewire('admin.products-management.recycle-bin.recycle-taxes')

            @endif



        </div>
    </div>
@push('script')
    @livewireScripts
    <script src="{{asset('libs/sweetalert2/sweetalert2.min.js')}}"></script>
<script>

    //event fired to livewire called delete
    window.Livewire.on('confirmRestore',function (e) {
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
            confirmButtonText: '{{__("text.Yes, restore it!")}}',
            cancelButtonText: '{{__("text.No, cancel!")}}',
            reverseButtons: true
        }).then((result) => {
            if (result.value == true) {
                window.Livewire.emit('restore',e)
            } else if (
                /* Read more about handling dismissals below */
                result.dismiss === Swal.DismissReason.cancel
            ) {
                swalWithBootstrapButtons.fire(
                    '{{__("text.Cancelled")}}',
                    '',
                    'error'
                )
            }
        })

    })

</script>
    {{-- <script>
        $('.table-responsive').on('show.bs.dropdown', function () {
            $('.table-responsive').css( "overflow", "inherit" );
        });

        $('.table-responsive').on('hide.bs.dropdown', function () {
            $('.table-responsive').css( "overflow", "auto" );
        })
    </script> --}}

@endpush

