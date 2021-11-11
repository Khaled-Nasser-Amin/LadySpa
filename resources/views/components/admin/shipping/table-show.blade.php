
<div class="card-box" style="overflow-y: scroll">
    <input type="text" class="form-control col-md-4 col-sm-8 mb-4" placeholder="{{__('text.Search')}}..." wire:model="search">
    <table  class="table table-striped text-white table-secondary text-center" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
        <thead>
        <tr>
            <th>{{__('text.Name')}}</th>
            <th>{{__('text.Shipping Cost')}}</th>
            <th>{{__('text.Action')}}</th>
        </tr>
        </thead>

        <tbody>
        @forelse($shipping as $ship)
            <tr>
                <td><span>{{$ship->city_name}}</span></td>
                <td><span>{{$ship->shipping_cost}}</span></td>

                <td>
                    <button class="btn btn-secondary waves-effect waves-light btn-sm" wire:click="edit({{$ship->id}})" data-toggle="modal" data-target="#EditShipping">
                        {{__('text.Edit')}}
                    </button>
                    <button type="button" wire:click="confirmDelete({{$ship->id}})" class="btn btn-danger waves-effect waves-light btn-sm">
                        {{__('text.Delete')}}
                    </button>
                </td>
            </tr>

        @empty
            <tr><td colspan="3" class="text-center">{{__('text.No Data Yet')}}</td></tr>
        @endforelse

        </tbody>
    </table>
</div>
