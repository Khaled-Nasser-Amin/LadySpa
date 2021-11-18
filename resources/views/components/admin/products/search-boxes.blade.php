<div class="col-sm-12 mt-2 mb-4">
    <form class="row justify-content-center" method="get">

        <div class="col-sm-6 col-md-4">
            <div class="form-group">
                <label for="field-00" class="control-label">{{__('text.Product Name')}}</label>
                <input type="text" wire:model="productName" class="form-control" id="field-00" placeholder="{{__('text.Product Name')}}...">
            </div>
        </div>

        <div class="col-sm-6 col-md-4">
            <div class="form-group">
                <label for="field-4" class="control-label">{{__('text.Size')}}</label>
                <input type="text" wire:model="size" class="form-control" id="field-4" placeholder="42">
            </div>
        </div>
        <div class="col-sm-6 col-md-4">
            <div class="form-group">
                <label for="field-5" class="control-label">{{__('text.Date')}}</label>
                <input type="date" wire:model="date" class="form-control" id="field-5">
            </div>
        </div>

        <div class="col-sm-6 col-md-4">
            <div class="form-group">
                <label for="field-3" class="control-label">{{__('text.Price')}}</label>
                <input type="text" wire:model="price" class="form-control" id="field-3" placeholder="{{__('text.Search By Price')}}">
            </div>
        </div>
        <div class="col-sm-6 col-md-4">
            <div class="form-group">
                <label for="field-7" class="control-label">{{__('text.Product Type')}}</label>
                <select  id="field-7" class="form-control" wire:model="product_type">
                    <option value=""></option>
                    <option value="single">@lang('text.Single')</option>
                    <option value="group">@lang('text.Group')</option>
                </select>
            </div>
        </div>
        <div class="col-sm-6 col-md-4">
            <div class="form-group">
                <label for="field-6" class="control-label">{{__('text.Featured And Non Featured')}}</label>
                <select  id="field-6" class="form-control" wire:model="featured_non_featured">
                    <option value=""></option>
                    <option value="Featured">@lang('text.Featured')</option>
                    <option value="Non Featured">@lang('text.Non Featured')</option>
                </select>
            </div>
        </div>
    </form>
</div>
