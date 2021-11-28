 @props(['index','updateAddition'])
 <div wire:ignore.self class="modal fade" id="updateAddition" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm bg-primary">
    <div class="modal-content">
        <div class="modal-header">
        <h5 class="modal-title" id="staticBackdropLabel">@lang('text.Addition')</h5>
        <button onclick="$('#updateAddition').modal('toggle')" type="button" class="close"  >
            <span aria-hidden="true">&times;</span>
        </button>
        </div>
            <div class="modal-body row">
                <div class="col-md-6 col-sm-12">
                    <label for="update_addition_name_ar">{{__('text.Name')}}</label><br>
                    <input type="string" class="form-control"  wire:model='update_addition_name_ar' id="update_addition_name_ar" ><br>
                    <x-general.input-error for="update_addition_name_ar" />
                </div>
                <div class="col-md-6 col-sm-12">
                    <label for="update_addition_name_en">{{__('text.Name')}}</label><br>
                    <input type="string" class="form-control"  wire:model='update_addition_name_en' id="update_addition_name_en" ><br>
                    <x-general.input-error for="update_addition_name_en" />
                </div>
                <div class="col-sm-12">
                    <label for="update_addition_price">{{__('text.Price')}}</label><br>
                    <input type="number" wire:model='update_addition_price' class="form-control" id="update_addition_price" autocomplete="none"><br>
                    <x-general.input-error for="update_addition_price" />
                </div>
            </div>
        <div class="modal-footer row justify-content-center">
        <button wire:click.prevent="updateAdditionComplete" type="button" class="btn btn-primary">@lang('text.Save')</button>
        </div>
    </div>
    </div>
</div>
