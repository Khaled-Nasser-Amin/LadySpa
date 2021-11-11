
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
            <table class="table table-striped  table-secondary col-12">
                <tr>
                    <th>{{__('text.Image')}}</th>
                    <th>{{__('text.Name')}}</th>
                    <th>{{__('text.Store Name')}}</th>
                    <th>{{__('text.Email')}}</th>
                    <th>{{__('text.Location')}}</th>
                    <th>{{__('text.Phone Number')}}</th>
                    <th>{{__('text.Completed Orders')}}</th>
                    <th>{{__('text.Refunds')}}</th>
                    <th>{{__('text.Total Amount')}}</th>
                    <th>{{__('text.Status')}}</th>
                    <th>{{__('text.Action')}}</th>
                </tr>
                @forelse ($vendors as $vendor)
                    <tr>
                        <td><a href="{{$vendor->image}}" target="_blank"><img src="{{$vendor->image}}" class="rounded-circle" style="width: 50px;height: 50px" alt="user-image"></a></td>
                        <td>{{$vendor->name}}</td>
                        <td>{{$vendor->store_name}}</td>
                        <td>{{$vendor->email}}</td>
                        <td>{{$vendor->location}}</td>
                        <td>{{$vendor->phone}}</td>
                        <td> {{$vendor->orders->where('payment_status','paid')->count()}} </td>
                        <td>{{$vendor->refunds->where('refund_status','not refunded yet')->sum('total_refund_amount')}}</td>
                        <td> {{$vendor->orders->where('payment_status','paid')->sum('pivot.total_amount')}} </td>
                        <td>{{ $vendor->activation == 0 ? __('text.Non Active'): __('text.Active') }}</td>
                        <td>
                            <button  wire:click.prevent="confirmRestore({{$vendor->id}})" class="btn btn-primary mt-3 btn-rounded btn-bordered waves-effect width-md waves-light text-white d-block mx-auto w-75">{{__('text.Restore')}}</button>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="11" class="text-center">{{__('text.No Data Yet')}}</td></tr>
                @endforelse

            </table>
        </div>



        {{$vendors->links()}}
    </div>
</div>
