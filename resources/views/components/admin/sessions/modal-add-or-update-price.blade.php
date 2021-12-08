<div wire:ignore.self class="modal fade" id="priceAndSale" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{__('text.Price')}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <div class="">

                    <div class="form-group " >
                        <h4 class="d-flex flex-row justify-content-between">@lang('text.Internal price') <span>@?</span></h4>
                        <div class="form-group">
                            <label for="price">{{__('text.Price')}}</label><br>
                            <input type="number" wire:model='price' class="form-control" id="price" autocomplete="none"><br>
                            <x-general.input-error for="price" />

                        </div>
                        <div class="form-group">
                            <label for="sale">{{__('text.Sale')}}</label><br>
                            <input type="number" wire:model='sale' class="form-control" id="sale" autocomplete="none"><br>
                            <x-general.input-error for="sale" />

                        </div>
                    </div>


                    <div class="form-group " >
                        <h4 class="d-flex flex-row justify-content-between">@lang('text.External Price') <span>@?</span></h4>
                        <div class="form-group">
                            <label for="external_price">{{__('text.Price')}}</label><br>
                            <input type="number" wire:model='external_price' class="form-control" id="external_price" autocomplete="none"><br>
                            <x-general.input-error for="external_price" />

                        </div>
                        <div class="form-group">
                            <label for="external_sale">{{__('text.Sale')}}</label><br>
                            <input type="number" wire:model='external_sale' class="form-control" id="external_sale" autocomplete="none"><br>
                            <x-general.input-error for="external_sale" />

                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

