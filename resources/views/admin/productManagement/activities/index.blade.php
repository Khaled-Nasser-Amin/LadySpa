@section('title',__('text.Activites'))
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
                <li class="breadcrumb-item active">{{__('text.Activites')}}</li>
                <li class="breadcrumb-item active"><a href="{{route('admin.index')}}">{{__('text.Dashboard')}}</a></li>
                <x-slot name="title">
                    <h4 class="page-title">{{__('text.Activites')}}</h4>
                </x-slot>
            </x-admin.general.page-title>


            <!-- End row -->
            @include('admin.partials.success')
            <br>
            <div class="row">
                <div class="col-12">
                    <div class="row col-12 justify-content-between p-0 m-0 my-2">
                        <input type="text" wire:model="search" class="form-control col-4 my-3 d-inline-block" placeholder="{{__('text.Search')}}...">
                        <button class="btn btn-danger btn-sm " wire:click.prevent="confirmDeleteAll">@lang('text.Delete All Activities')</button>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped table-secondary col-12">

                            <tr>
                                <th>{{__('text.Image')}}</th>
                                <th>{{__('text.Store Name')}}</th>
                                <th>{{__('text.Ip Address')}}</th>
                                <th>{{__('text.Opreating System')}}</th>
                                <th>{{__('text.Browser')}}</th>
                                <th>{{__('text.Event')}}</th>
                                <th>{{__('text.Event Time')}}</th>
                                <th>{{__('text.Action')}}</th>
                            </tr>
                            @forelse ($activities as $activity)
                                <tr>
                                    <td><a href="{{$activity->activity->image}}" target="_blank"><img src="{{$activity->activity->image}}" class="rounded-circle" style="width: 50px;height: 50px" alt="user-image"></a></td>
                                    <td>{{ $activity->activity->store_name }}</td>
                                    <td>{{$activity->ip_address}}</td>
                                    <td>{{$activity->user_agent->platform()}}</td>
                                    <td>{{$activity->user_agent->browser()}}</td>
                                    <td>{{__('text.'.$activity->activity_action)}}</td>
                                    <td>{{$activity->created_at->diffForHumans() }}</td>
                                    <td>
                                       @can('delete-activity',$activity)
                                       <button class="btn btn-danger btn-sm" wire:click.prevent="confirmDelete({{$activity->id}})">{{__('text.Delete')}}</button>

                                       @endcan
                                    </td>
                                </tr>

                            @empty
                                <tr><td colspan="7" class="text-center">{{__('text.No Data Yet')}}</td></tr>
                            @endforelse


                        </table>
                        {{$activities->links()}}
                    </div>
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
        alertForDelete('delete',e)
    })
    window.Livewire.on('confirmDeleteAll',function (e) {
        alertForDelete('deleteAll',e)
    })

    function alertForDelete(event_name,event){
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
                window.Livewire.emit(event_name,event)
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
    }

    </script>



@endpush

