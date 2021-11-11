<div wire:ignore.self class="modal fade" id="sizesAndPrices" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{__('text.Sizes')}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <div class="addSelectProduct">

                    <div class="form-group row justify-content-between" >
                        {{-- size modal --}}
                        <div class="col-12 row justify-content-between mx-0">

                            <button data-toggle="modal" data-target="#sizeAndStock0" type="button" class="btn btn-success btn_AddMore btn-sm">
                                {{__('text.Add Size')}}
                            </button>

                            <x-admin.products.modal-size-and-stock :index="0"/>
                            <x-admin.products.modal-update-size-and-stock :index="0" />

                        </div>
                        <div class="px-2">
                            <x-general.input-error for="sizes" />

                        </div>

                        <div class="col-12 " style="overflow-y: scroll">
                            @if (collect($sizes)->count() > 0)
                                 <table class="table table-borderd text-center">
                                <tr>
                                    <th>@lang('text.Size')</th>
                                    <th>@lang('text.Stock')</th>
                                    <th>@lang('text.Price')</th>
                                    <th>@lang('text.Sale')</th>
                                    <th>@lang('text.Action')</th>
                                </tr>
                                @foreach ($sizes as $index => $value)
                                    <tr>
                                        <td>{{ $value['size'] }}</td>
                                        <td>{{ $value['stock'] }}</td>
                                        <td>{{ $value['price'] }}</td>
                                        <td>{{ $value['sale'] }}</td>
                                        <td>
                                            <button data-toggle="modal" data-target="#updateSizeAndStock0" type="button" wire:click.prevent="updateSize({{ $index }})" class="btn btn-info btn-sm "><i class="mdi mdi-pencil"></i></button>
                                            <button wire:click.prevent="deleteSize({{ $index }})" class="btn btn-danger btn-sm "><i class="mdi mdi-delete"></i></button>
                                        </td>
                                    </tr>
                                @endforeach

                            </table>
                            @endif

                        </div>




                        <div class="col-12 row justify-content-center">
                            <button wire:click.prevent="addedAllSizes" class="btn btn-primary">@lang('text.Save')</button>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

