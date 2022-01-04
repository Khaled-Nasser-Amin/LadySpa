<div class="row">
    <div class="col-12">
        <input type="text" wire:model="search" class="form-control col-4 my-3 d-inline-block" placeholder="{{__('text.Search')}}...">
        <select wire:model="status" class="form-control col-4 my-3 d-inline-block">
            <option value="">@lang('text.Choose Item\'s Status')</option>
            <option value="1">@lang('text.Items Returned')</option>
            <option value="2">@lang('text.Not Returned Yet')</option>
        </select>
        <div class="table-responsive">
            <table class="table table-striped col-12 table-secondary">
                <tr>
                    <th>{{__('text.Image')}}</th>
                    <th>{{__('text.Reservation\'s Number')}}</th>
                    @can('isAdmin')
                        <th>{{__('text.Store Name')}}</th>
                    @endcan
                    <th>{{__('text.Session Name')}}</th>
                    <th>{{__('text.Number of additions')}}</th>
                    <th>{{__('text.Number of persons')}}</th>
                    <th>{{__('text.Refund status')}}</th>
                    <th>{{__('text.Total Amount')}}</th>
                    <th>{{__('text.Subtotal')}}</th>
                    <th>{{__('text.Taxes')}}</th>
                    @can('isAdmin')
                    <th>{{__('text.Action')}}</th>
                    @endcan
                </tr>
                @forelse ($refunds as $refund)
                @php
                    $session=$refund->session()->withTrashed()->first();
                    $image=$session->image;
                @endphp
                    <tr>
                        <td><a href="{{$image}}" target="_blank"><img src="{{$image}}" class="rounded-circle" style="width: 50px;height: 50px" alt="user-image"></a></td>
                        <td>{{$refund->reservation_id}}</td>
                        @can('isAdmin')
                        <td> {{$session->user()->withTrashed()->first()->store_name}}</td>
                        @endcan
                        <td> {{app()->getLocale() == 'ar' ? $session->name_ar:$session->name_en}}</td>
                        <td>{{ $refund->number_of_additions}}</td>
                        <td>{{ $refund->number_of_persons}}</td>
                        <td>{!! $refund->refund_status != 'not refunded yet' ? '<i class="text-success mdi mdi-checkbox-marked-circle"></i> '.__('text.Items Returned'): '<i class="text-danger mdi mdi-close-circle"></i> '.__('text.Not Returned Yet') !!}</td>
                        <td> {{$refund->total_refund_amount}}</td>
                        <td> {{$refund->subtotal_refund_amount}}</td>
                        <td> {{$refund->taxes}}</td>
                        @can('isAdmin')
                        <td>
                        @if ($refund->refund_status == 'not refunded yet')
                            <button class="btn btn-info" wire:click.prevent="confirmDelete({{$refund->id}},'reservation')">{{__('text.Restore')}}</button>
                        @endif
                        </td>
                        @endcan
                    </tr>
                @empty
                    <tr><td colspan="11" class="text-center">{{__('text.No Data Yet')}}</td></tr>
                @endforelse

            </table>
        </div>

        {{$refunds->links()}}
    </div>
</div>
