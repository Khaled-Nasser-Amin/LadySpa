
<div class="card-box" style="overflow-y: scroll">
    <input type="text" class="form-control col-md-4 col-sm-8 mb-4" placeholder="{{__('text.Search')}}..." wire:model="search">
    <table  class="table table-striped table-secondary text-center" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
        <thead>
        <tr>

            <th>{{__('text.Name_ar')}}</th>
            <th>{{__('text.Name_en')}}</th>
            <th>{{__('text.Tax')}}</th>
            <th>{{__('text.Action')}}</th>
        </tr>
        </thead>

        <tbody>
        @forelse($taxes as $tax)
            <tr>

                <td><span >{{$tax->name_ar}}</span></td>
                <td><span >{{$tax->name_en}}</span></td>
                <td><span >{{$tax->tax}}%</span></td>


                <td>

                    <button class="btn btn-secondary waves-effect waves-light btn-sm" wire:click="edit({{$tax->id}})" data-toggle="modal" data-target="#EditTax">
                        <i class="mdi mdi-pencil"></i>
                        {{__('text.Edit')}}
                    </button>
                    <button type="button" wire:click="confirmDelete({{$tax->id}})" class="btn btn-danger waves-effect waves-light btn-sm">
                        <i class="mdi mdi-delete"></i>
                        {{__('text.Delete')}}
                    </button>
                </td>
            </tr>

        @empty
            <tr><td colspan="4" class="text-center">{{__('text.No Data Yet')}}</td></tr>
        @endforelse

        </tbody>
    </table>
</div>
