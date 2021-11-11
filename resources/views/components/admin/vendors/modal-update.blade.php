<div wire:ignore.self id="EditVendor" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title mt-0">{{__('text.Vendor')}}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form >
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="password" class="control-label">{{__('text.Password')}}</label>
                                <input type="password" wire:model="password" class="form-control" id="password"  placeholder="">
                                <x-general.input-error for="password" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="password_confirmation" class="control-label">{{__('text.Confirm Password')}}</label>
                                <input type="password" wire:model="password_confirmation" class="form-control" id="password_confirmation" placeholder="">
                                <x-general.input-error for="password_confirmation" />
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary waves-effect" data-dismiss="modal">{{__('text.Close')}}</button>
                <button type="button" class="btn btn-info waves-effect waves-light" wire:click.prevent="updateVendor">{{__('text.Edit')}}</button>
            </div>
        </div>
    </div>
</div>

