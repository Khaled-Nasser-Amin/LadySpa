<div wire:ignore.self id="AddNewBanner"  class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h4 class="modal-title mt-0">{{__('text.Banner')}}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">

                <form  id="addNewBanner" enctype="multipart/form-data">
                    <div class="d-flex flex-row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="name1" class="control-label">{{__('text.Name')}}</label>
                                <input type="text" wire:model="name" class="form-control" id="name1" >
                                <x-general.input-error for="name" />
                            </div>
                        </div>

                    </div>
                    <div class="d-flex flex-row">
                        <div class="col-md-12">
                            <div class="form-group mb-4">
                                <label>{{__('text.Add Image')}}</label>
                                <input type="file"  wire:model="image"  data-height="210" />
                                <x-general.input-error for="image" />
                            </div>
                        </div>
                    </div>

                    <div class="d-flex flex-row">
                        <div class="col-md-12">
                            <div class="form-group no-margin">
                                <label for="date1" class="control-label">{{__('text.Expired date')}}</label>
                                <input type="datetime-local" wire:model="expire_at" class="form-control" id="date1">
                                <x-general.input-error for="expire_at" />
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary waves-effect" data-dismiss="modal">{{__('text.Close')}}</button>
                <button type="button" class="btn btn-info waves-effect waves-light" wire:click.prevent="store" wire:loading.attr="disabled" wire:target="image">{{__('text.Save')}}</button>
            </div>
        </div>
    </div>
</div>
