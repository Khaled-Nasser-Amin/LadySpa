@section('title',__('text.Users'))
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
    <div class="content">
        <!-- Start Content-->
        <div class="container-fluid">

            <!-- start page title -->
            <x-admin.general.page-title>
                <li class="breadcrumb-item active">{{__('text.Users')}}</li>
                <li class="breadcrumb-item active"><a href="{{route('admin.index')}}">{{__('text.Dashboard')}}</a></li>
                <x-slot name="title">
                    <h4 class="page-title">{{__('text.Users')}}</h4>
                </x-slot>
            </x-admin.general.page-title>

            @include('admin.partials.success')

            <div class="row">
                <div class="col-12">
                    <input type="text" wire:model="search" class="form-control col-4 my-3 d-inline-block" placeholder="{{__('text.Search')}}...">
                    <select wire:model="status" class="form-control col-4 my-3 d-inline-block">
                        <option value="">@lang('text.Choose User\'s Status')</option>
                        <option value="1">@lang('text.Active')</option>
                        <option value="2">@lang('text.Non Active')</option>
                    </select>
                    <div class="table-responsive">
                        <table class="table table-striped col-12 table-secondary">
                            <tr>
                                <th>{{__('text.Image')}}</th>
                                <th>{{__('text.Name')}}</th>
                                <th>{{__('text.Email')}}</th>
                                <th>{{__('text.Phone Number')}}</th>
                                <th>{{__('text.Number of Orders')}}</th>
                                <th>{{__('text.Status')}}</th>
                                <th>{{__('text.Action')}}</th>
                            </tr>
                            @forelse ($users as $user)
                                <tr>
                                    <td><a href="{{$user->image}}" target="_blank"><img src="{{$user->image}}" class="rounded-circle" style="width: 50px;height: 50px" alt="user-image"></a></td>
                                    <td>{{$user->name}}</td>
                                    <td>{{$user->email}}</td>
                                    <td>{{$user->phone}}</td>
                                    <td> {{$user->orders->count()}}</td>
                                    <td>{{ $user->activation == 0 ? __('text.Non Active'): __('text.Active') }}</td>
                                    <td><button class="btn btn-danger" wire:click.prevent="confirmDelete({{$user->id}})">{{__('text.Delete')}}</button></td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="text-center">{{__('text.No Data Yet')}}</td></tr>
                            @endforelse

                        </table>
                    </div>

                    {{$users->links()}}
                </div>
            </div>


        </div>
    </div>
@push('script')
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

