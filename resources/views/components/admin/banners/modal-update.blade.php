<div wire:ignore.self id="EditBanner" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h4 class="modal-title mt-0">{{__('text.Banner')}}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">

                <form  id="editBanner" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name" class="control-label">{{__('text.Name')}}</label>
                                <input type="text" wire:model="name" class="form-control" id="name" >
                                <x-general.input-error for="name" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="showIn" class="control-label">{{__('text.Show in')}}</label>
                                <select wire:model="show_in" id="showIn" class="form-control">
                                    <option value="home">@lang('text.Dashboard')</option>
                                    <option value="shop">@lang('text.Shop')</option>
                                </select>
                                <x-general.input-error for="show_in" />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group mb-4">
                            <label>{{__('text.Add Image')}}</label>
                            <input type="file"  wire:model="image"  data-height="210" />
                            <x-general.input-error for="image" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="url" class="control-label">{{__('text.Url')}}</label>
                                <input type="text" wire:model="url" class="form-control" id="url" >
                                <x-general.input-error for="url" />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group no-margin">
                                <label for="date" class="control-label">{{__('text.Expired date')}}</label>
                                <input type="datetime-local" wire:model="expire_at" class="form-control" id="date">
                                <x-general.input-error for="expire_at" />
                            </div>

                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary waves-effect" data-dismiss="modal">{{__('text.Close')}}</button>
                <button type="button" class="btn btn-info waves-effect waves-light" wire:click.prevent="update" wire:loading.attr="disabled" wire:target="image">{{__('text.Save')}}</button>
            </div>
        </div>
    </div>
</div>

