
<div class="row">
    <div class="col-sm-12">
        @include('admin.partials.success')

        <div class="card-box" style="overflow-y: scroll">
            <input type="text" class="form-control col-md-4 col-sm-8 mb-4" placeholder="{{__('text.Search')}}..." wire:model="search">
            <table  class="table table-striped  table-secondary " style="border-collapse: collapse; border-spacing: 0; width: 100%;">
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
                        <td><span >{{$tax->tax}}</span></td>
                        <td>
                            <button  wire:click.prevent="confirmRestore({{$tax->id}})" class="btn btn-primary mt-3 btn-rounded btn-bordered waves-effect width-md waves-light text-white d-block mx-auto w-75">{{__('text.Restore')}}</button>
                        </td>
                    </tr>

                @empty
                    <tr><td colspan="4" class="text-center">{{__('text.No Data Yet')}}</td></tr>
                @endforelse

                </tbody>
            </table>
        </div>
        {{$taxes->links()}}
    </div>
</div>
