<div wire:ignore.self id="EditTax" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title mt-0">{{__('text.Tax')}}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form >
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name_ar" class="control-label">{{__('text.Name_ar')}}</label>
                                <input type="text" wire:model="name_ar" class="form-control" id="name_ar"  placeholder="ضريبة القيمة المضافة">
                                <x-general.input-error for="name_ar" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name_en" class="control-label">{{__('text.Name_en')}}</label>
                                <input type="text" wire:model="name_en" class="form-control" id="name_en" placeholder="Vat">
                                <x-general.input-error for="name_en" />
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group no-margin">
                                <label for="tax" class="control-label">{{__('text.Tax')}}</label>
                                <input type="integer" wire:model="tax" class="form-control" id="tax" placeholder="10%">
                                <x-general.input-error for="tax" />
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

