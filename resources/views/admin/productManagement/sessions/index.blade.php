@section('title',__('text.Sessions'))
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
    <div class="content">

        <!-- Start Content-->
        <div class="container-fluid">

            <!-- start page title -->
            <x-admin.general.page-title>
                <li class="breadcrumb-item active">{{__('text.Sessions')}}</li>
                <li class="breadcrumb-item active"><a href="{{route('admin.index')}}">{{__('text.Dashboard')}}</a></li>
                <x-slot name="title">
                    <h4 class="page-title">{{__('text.Sessions')}} </h4>
                </x-slot>
            </x-admin.general.page-title>

            <!-- button add product -->
            <div class="row">
                <div class="col-sm-12">
                    <div class="col-3 mb-4 text-left mt-2">

                        <a href="/admin/session-add" type="submit"  class="btn btn-secondary waves-effect waves-light">
                            <i class=""></i>
                            {{__('text.Add New Session')}}
                        </a>

                    </div>
                </div>
            </div>

            {{--search boxes--}}
            <div class="row">
                {{-- <x-admin.sessions.search-boxes  /> --}}
            </div>

            <!-- all products -->
            <div class="row">
                {{-- <x-admin.sessions.card-show :sessions="$sessions" /> --}}
            </div>

            <!-- pagination -->
            <div>
                {{$sessions->links()}}
            </div>

        </div>
        <!-- end container-fluid -->

    </div>


@push('script')
    <script src="{{asset('js/toast.script.js')}}"></script>
    <script>
        window.addEventListener('danger',e=>{
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
