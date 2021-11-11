@section('title',__('text.Shipping'))
@push('css')
    @livewireStyles
    <link rel="stylesheet" href="{{asset('css/toast.style.min.css')}}">
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
                <li class="breadcrumb-item active">{{__('text.Shipping')}}</li>
                <li class="breadcrumb-item active"><a href="{{route('admin.index')}}">{{__('text.Dashboard')}}</a></li>
                <x-slot name="title">
                    <h4 class="page-title">{{__('text.Shipping')}}</h4>
                </x-slot>
            </x-admin.general.page-title>


            <!-- End row -->
            @include('admin.partials.success')
            <br>
            <div class="d-flex flex-row" >

                <div class="col-md-6 col-sm-12 form-group">
                    <h6>@lang('text.Which way do you want to calculate Your shipping cost : ')        </h6>
                    <input id="for_kilometer" wire:model="shipping_status" type="radio"  value="by_kilometer">

                    <label for="for_kilometer">@lang('text.For each kilometer')</label>
                    <br>
                    <input id="for_city" wire:model="shipping_status" type="radio"  value="by_city">

                    <label for="for_city">@lang('text.For each city')</label>

                </div>
            </div>
            <br>
            @if ($shipping_status == 'by_kilometer')

            <div class="d-flex flex-row">

                <div class="col-md-6 col-sm-12 form-group" >
                    <label for="shipping_cost_for_kilometer" class="d-block">@lang('text.Cost for kilometer')</label>
                    <input wire:model="shipping_cost_by_kilometer" type="number" class="form-control col-9 d-inline-block">
                    <button wire:click.prevent="shippingCost" class="btn btn-primary col-2">@lang('text.Save')</button>
                </div>

            </div>


            @elseif ($shipping_status == 'by_city')
                <div class="row">
                    <div class="col-sm-12">
                        <x-admin.shipping.modal-add :cities="$cities"/>
                        <x-admin.shipping.modal-update :ids="$ids" :cityName="$city_name" />
                        <!-- Responsive modal -->
                        <button class="btn btn-primary waves-effect waves-light" data-toggle="modal" data-target="#AddNewShipping">
                            {{__('text.Add New City')}}
                        </button>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-sm-12">
                        <x-admin.shipping.table-show :shipping="$shipping" />
                        {{$shipping->links()}}
                    </div>
                </div>
            @endif

        </div>
    </div>

@push('script')
    <script src="{{asset('libs/sweetalert2/sweetalert2.min.js')}}"></script>
    @livewireScripts
    <script>
    window.Livewire.on('addedShipping',()=>{
        $('#AddNewShipping').modal('hide');
    })
    window.Livewire.on('EditShipping',()=>{
        $('#EditShipping').modal('hide');
    })

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



<script src="{{asset('js/toast.script.js')}}"></script>
<script>
    window.addEventListener('success',e=>{
        $.Toast(e.detail,"",'success',{
            stack: false,
            position_class: "toast-top-center",
            rtl: {{app()->getLocale()=='ar' ? "true" : 'false'}}
        });
    })
</script>

@endpush

