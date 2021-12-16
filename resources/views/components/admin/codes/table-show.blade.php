
<div class="card-box" style="overflow-y: scroll">
        <table  class="table table-striped table-secondary text-center" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
        <thead>
        <tr>

            <th>{{__('text.Code')}}</th>
            <th>{{__('text.Start date')}}</th>
            <th>{{__('text.End date')}}</th>
            <th>{{__('text.Limitation')}}</th>
            <th>{{__('text.Number of used times')}}</th>
            <th>{{__('text.For')}}</th>
            <th>{{__('text.Type of code')}}</th>
            <th>{{__('text.Type of discount')}}</th>
            <th>{{__('text.Value')}}</th>
            <th>{{__('text.The maximum discount')}} / {{__('text.The minimum purchase')}}</th>
            <th>{{__('text.Action')}}</th>
        </tr>
        </thead>

        <tbody>
        @forelse($codes as $code)
            <tr>

                <td>{{$code->code}}</td>
                <td>{{$code->start_date}}</td>
                <td>{{$code->end_date}}</td>
                <td>{{$code->limitation}}</td>
                <td>{{$code->used_customers->count()}}</td>
                <td>{{$code->for}}</td>
                <td>{{$code->type_of_code}}</td>
                <td>{{$code->type_of_discount}}</td>
                <td>{{$code->value}} {{ $code->type_of_discount =='percentage' ? '%' : __('text.SAR') }}</td>
                <td>{{$code->condition}} @lang('text.SAR')</td>

                <td>
                    <button type="button" wire:click="confirmDelete({{$code->id}})" class="btn btn-danger waves-effect waves-light btn-sm">
                        <i class="mdi mdi-delete"></i>
                        {{__('text.Delete')}}
                    </button>
                </td>
            </tr>

        @empty
            <tr><td colspan="11" class="text-center">{{__('text.No Data Yet')}}</td></tr>
        @endforelse

        </tbody>
    </table>
</div>
