<div class="col-sm-12 mt-2 mb-4">
    <form class="row justify-content-center" method="get">

        <div class="col-sm-6 col-md-4">
            <div class="form-group">
                <label for="field-00" class="control-label">{{__('text.Code')}}</label>
                <input type="text" wire:model="searchCode" class="form-control" id="field-00" placeholder="{{__('text.Code')}}...">
            </div>
        </div>

        <div class="col-sm-6 col-md-4">
            <div class="form-group">
                <label for="field-4" class="control-label">{{__('text.Number')}}</label>
                <input type="text" wire:model="searchNumber" class="form-control" id="field-4" placeholder="@lang('text.Limitation , Value')">
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
                <label for="searchFor" class="control-label">{{__('text.For')}}</label>
                <select wire:model="searchFor" class="form-control" id="searchFor">
                    <option value=""></option>
                    <option value="general">@lang('text.General')</option>
                    <option value="sessions">@lang('text.Sessions')</option>
                    <option value="products">@lang('text.Products')</option>
                </select>
            </div>
        </div>
        <div class="col-sm-6 col-md-4">
            <div class="form-group">
                <label for="searchType_of_code" class="control-label">{{__('text.Type of code')}}</label>
                <select wire:model="searchType_of_code" class="form-control" id="searchType_of_code">
                    <option value=""></option>
                    <option value="normal">@lang('text.Normal')</option>
                    <option value="special">@lang('text.Special')</option>
                </select>
            </div>
        </div>
        <div class="col-sm-6 col-md-4">
            <div class="form-group">
                <label for="searchType_of_discount" class="control-label">{{__('text.Type of discount')}}</label>
                <select wire:model="searchType_of_discount" class="form-control" id="searchType_of_discount">
                    <option value=""></option>
                    <option value="amount">@lang('text.Amount')</option>
                    <option value="percentage">@lang('text.Percentage')</option>
                </select>
            </div>
        </div>

    </form>
</div>
