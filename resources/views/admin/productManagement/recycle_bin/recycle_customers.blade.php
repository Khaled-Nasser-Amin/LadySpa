
<div class="row">
    <div class="col-12">
        @include('admin.partials.success')

        <input type="text" wire:model="search" class="form-control col-4 my-3 d-inline-block" placeholder="{{__('text.Search')}}...">
        <select wire:model="status" class="form-control col-4 my-3 d-inline-block">
            <option value="">@lang('text.Choose User\'s Status')</option>
            <option value="1">@lang('text.Active')</option>
            <option value="2">@lang('text.Non Active')</option>
        </select>
        <div class="table-responsive">
            <table class="table table-striped table-secondary col-12 ">
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
                        <td>
                            <button  wire:click.prevent="confirmRestore({{$user->id}})" class="btn btn-primary mt-3 btn-rounded btn-bordered waves-effect width-md waves-light text-white d-block mx-auto w-75">{{__('text.Restore')}}</button>

                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center">{{__('text.No Data Yet')}}</td></tr>
                @endforelse

            </table>
        </div>

        {{$users->links()}}
    </div>
</div>
