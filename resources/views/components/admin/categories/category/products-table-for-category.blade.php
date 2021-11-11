<div class="col-lg-10">
    <!-- end m-t-30 -->
    <h6> @lang('text.Sold Pieces for category') <span class="text-danger" style="font-weight: bold">{{app()->getLocale() =='ar' ? $category->name_ar:$category->name_en}}</span> @lang('text.during') ({{ session()->get("current_month") ?? null }} / {{ session()->get("current_year")??null }})</h6>
        <table id="datatable-buttons" class="table table-striped table-secondary" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
            <thead>
            <tr>
                <th>{{__('text.Image')}}</th>
                <th>{{__('text.Name')}}</th>
                <th>{{__('text.Type Of Fabric')}}</th>
                <th>{{__('text.Type Of Sleeve')}}</th>
                <th>{{__('text.Number Of Orders')}}</th>
                <th>{{__('text.Sold Pieces')}}</th>
                <th>{{__('text.Color')}}</th>
                <th>{{__('text.Size')}}</th>
                <th>{{__('text.Total Amount')}}</th>
            </tr>
            </thead>
            <tbody>
                @forelse($sizes as $size)
                    <tr>
                        <td>
                            <a wire:click="searchByProduct({{$size->color->product->id}})" href="#">
                                <div class="inbox-item-img">
                                    <img style="width: 50px;height: 50px" class="img-thumbnail" src="{{$size->color->product->image}}" alt="{{__('text.Image')}}">
                                </div>
                            </a>
                        </td>
                        <td>{{app()->getLocale() == 'ar' ? $size->color->product->name_ar : $size->color->product->name_en}}</td>
                        <td>{{$size->color->product->typeOfFabric}}</td>
                        <td>{{$size->color->product->typeOfSleeve}}</td>
                        <td>{{$size->order()->whereYear('created_at',session()->get('current_year')??null)->whereMonth('created_at',session()->get('current_month')??null)->count()}}</td>
                        <td>{{getOrdersCurrentMonth($size)->sum('quantity')}}</td>
                        <td><button class="w-100 d-block btn btn-sm" style="height:40px;border-radius:10px;background-color:{{ $size->color->color }};border-color:{{ $size->color->color }};color:{{ $size->color->color }}"></btn></td>
                        <td>{{$size->size}}</td>
                        {{-- (size quantity / total color quantity) * total_amount for color --}}
                        <?php  $size_subtotals=(getOrdersCurrentMonth($size)->sum('quantity')/ getOrdersCurrentMonth($size->color()->withTrashed()->first())->sum('quantity')) * getOrdersCurrentMonth($size->color()->withTrashed()->first())->get()->sum('pivot.total_amount')?>
                        {{-- calculate subtotals + taxes --}}
                        <td>{{ ($size_subtotals + ($size_subtotals*$size->color()->withTrashed()->first()->product()->withTrashed()->first()->taxes()->withTrashed()->sum('tax')/100)) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center">{{__('text.The category does not have any product yet')}}</td>
                    </tr>
                @endforelse


            </tbody>
        </table>
    <!-- end card-box -->

</div>
