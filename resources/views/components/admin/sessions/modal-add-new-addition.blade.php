@props(['index'])
<div wire:ignore.self class="modal fade" id="addNewAddition" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm bg-primary">
    <div class="modal-content">
        <div class="modal-header">
        <h5 class="modal-title" id="staticBackdropLabel">@lang('text.Addition')</h5>
        <button onclick="$('#addNewAddition').modal('toggle')" type="button" class="close"  >
            <span aria-hidden="true">&times;</span>
        </button>
        </div>
            <div class="modal-body row">
                <div class="col-md-6 col-sm-12">
                    <label for="name_ar">{{__('text.Name_ar')}}</label><br>
                    <input type="string" class="form-control"  wire:model='addition_name_ar' id="name_ar" ><br>
                    <x-general.input-error for="name_ar" />
                </div>
                <div class="col-md-6 col-sm-12">
                    <label for="name_en">{{__('text.Name_en')}}</label><br>
                    <input type="string" class="form-control"  wire:model='addition_name_en' id="name_en" ><br>
                    <x-general.input-error for="name_en" />
                </div>
                <div class="col-sm-12">
                    <label for="addition_price">{{__('text.Price')}}</label><br>
                    <input type="number" wire:model='addition_price' class="form-control" id="addition_price" autocomplete="none"><br>
                    <x-general.input-error for="addition_price" />
                </div>
            </div>
        <div class="modal-footer row justify-content-center">
        <button wire:click.prevent="addAddition" type="button" class="btn btn-primary">@lang('text.Save')</button>
        </div>
    </div>
    </div>
</div>
