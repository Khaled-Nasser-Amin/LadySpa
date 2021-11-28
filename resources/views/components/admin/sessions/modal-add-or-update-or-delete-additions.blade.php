<div wire:ignore.self class="modal fade" id="AddAdditions" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{__('text.Additions')}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <div class="addSelectProduct">

                    <div class="form-group " >
                        {{-- size modal --}}
                        <div class="col-12 row justify-content-between mx-0">

                            <button data-toggle="modal" data-target="#addNewAddition" type="button" class="btn btn-success btn_AddMore btn-sm">
                                {{__('text.Add Addition')}}
                            </button>

                            <x-admin.sessions.modal-add-new-addition />
                            <x-admin.sessions.modal-update-addition :updateAddition="$updateAddition" />

                        </div>
                        <div class="px-2">
                            <x-general.input-error for="additions" />

                        </div>

                        <br>

                        <div class="col-12 " style="overflow-y: scroll">
                            @if (collect($additions)->count() > 0)
                            <h5>@lang('text.Active Additions')</h5>

                                 <table class="table table-borderd text-center">
                                <tr>
                                    <th>@lang('text.Name_ar')</th>
                                    <th>@lang('text.Name_en')</th>
                                    <th>@lang('text.Price')</th>
                                    <th>@lang('text.Action')</th>
                                </tr>
                                @foreach ($additions as $index => $value)
                                    <tr wire:key={{ $value['addition_name_ar'].$index }}>
                                        <td>{{ $value['addition_name_ar'] }}</td>
                                        <td>{{ $value['addition_name_en'] }}</td>
                                        <td>{{ $value['addition_price'] }}</td>
                                        <td>
                                            <button data-toggle="modal" data-target="#updateAddition" type="button" wire:click.prevent="updateAddition({{ $index }})" class="btn btn-info btn-sm "><i class="mdi mdi-pencil"></i></button>
                                            <button wire:click.prevent="deleteAddition({{ $index }})" class="btn btn-danger btn-sm "><i class="mdi mdi-delete"></i></button>
                                        </td>
                                    </tr>
                                @endforeach

                            </table>
                            @endif

                        </div>

                        <br>

                        <div class="col-12 " style="overflow-y: scroll">
                            @if (collect($deletedAdditions)->count() > 0)
                            <h5>@lang('text.Deleted Additions')</h5>

                                 <table class="table table-borderd text-center">
                                <tr>
                                    <th>@lang('text.Name_ar')</th>
                                    <th>@lang('text.Name_en')</th>
                                    <th>@lang('text.Price')</th>
                                    <th>@lang('text.Action')</th>
                                </tr>
                                @foreach ($deletedAdditions as $index => $value)
                                    <tr wire:key={{ $value['addition_name_ar'].$index }}>
                                        <td>{{ $value['addition_name_ar'] }}</td>
                                        <td>{{ $value['addition_name_en'] }}</td>
                                        <td>{{ $value['addition_price'] }}</td>
                                        <td>
                                            <button wire:click.prevent="restoreAddition({{ $index }})" class="btn btn-info btn-sm "><i class="mdi mdi-delete-restore"></i></button>
                                        </td>
                                    </tr>
                                @endforeach

                            </table>
                            @endif

                        </div>

                        <div class="col-12 row justify-content-center">
                            <button wire:click.prevent="addedAllAdditions" class="btn btn-primary">@lang('text.Save')</button>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

