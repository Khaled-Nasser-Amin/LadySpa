<div class="d-flex flex-row" >

    <div class="col-md-6 col-sm-12 form-group">
        <h6>@lang('text.Select single product or collection of products')</h6>
        <input id="for_single" wire:model="type" type="radio"  value="single">

        <label for="for_single">@lang('text.Single product')</label>
        <br>
        <input id="for_group" wire:model="type" type="radio"  value="group">

        <label for="for_group">@lang('text.Group of products')</label>

    </div>
</div>
