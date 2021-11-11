<div wire:ignore.self id="EditShipping" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title mt-0">{{__('text.Category')}}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form >
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="name_ar" class="control-label">{{__('text.Name')}}</label>
                                <input type="text" class="form-control" value="{{ $cityName }}" disabled>
                            </div>
                            <div class="form-group">
                                <label for="shipping_cost" class="control-label">{{__('text.Shipping Cost')}}</label>
                                <input type="text" wire:model="shipping_cost" class="form-control" id="shipping_cost" placeholder="50">
                                <x-general.input-error for="shipping_cost" />
                            </div>
                        </div>

                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary waves-effect" data-dismiss="modal">{{__('text.Close')}}</button>
                <button type="button" class="btn btn-info waves-effect waves-light" wire:click.prevent="update">{{__('text.Save')}}</button>
            </div>
        </div>
    </div>
</div>

