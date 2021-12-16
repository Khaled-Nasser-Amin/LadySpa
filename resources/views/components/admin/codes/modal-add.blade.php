<div wire:ignore.self id="AddNewCode"  class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h4 class="modal-title mt-0">{{__('text.Code')}}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">

                <form  id="addNewCat">
                    <div class="row">
                        <div class="col-md-12">
                            <label for="code" class="control-label">{{__('text.Code')}}</label>
                            <div class="form-group d-flex flex-row ">
                                <input type="text" value="{{ $code }}" class="form-control" id="code" disabled>
                                <button class="btn btn-primary btn-sm mx-2" wire:click.prevent="changeCode">@lang('text.Change')</button>
                            </div>
                            <x-general.input-error for="code" />
                        </div>
                        <div class="col-md-12 d-flex flex-row justify-content-between px-0 mx-0">
                            <div class="form-group col-md-6">
                                <label for="start_date" class="control-label">{{__('text.Start date')}}</label>
                                <input type="datetime-local" wire:model="start_date" class="form-control" id="start_date" >
                                <x-general.input-error for="start_date" />
                            </div>
                            <div class="form-group col-md-6">
                                <label for="end_date" class="control-label">{{__('text.End date')}}</label>
                                <input type="datetime-local" wire:model="end_date" class="form-control" id="end_date" >
                                <x-general.input-error for="end_date" />
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group no-margin">
                                <label for="limitation" class="control-label">{{__('text.Limitation')}}</label>
                                <input type="number" wire:model="limitation" class="form-control" id="limitation">
                                <x-general.input-error for="limitation" />
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group no-margin">
                                <label for="for" class="control-label">{{__('text.For')}}</label>
                                <select wire:model="for" class="form-control" id="for">
                                    <option value=""></option>
                                    <option value="general">@lang('text.General')</option>
                                    <option value="sessions">@lang('text.Sessions')</option>
                                    <option value="products">@lang('text.Products')</option>
                                </select>
                                <x-general.input-error for="for" />
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group no-margin">
                                <label for="type_of_code" class="control-label">{{__('text.Type of code')}}</label>
                                <select wire:model="type_of_code" class="form-control" id="type_of_code">
                                    <option value=""></option>
                                    <option value="normal">@lang('text.Normal')</option>
                                    <option value="special">@lang('text.Special')</option>
                                </select>
                                <x-general.input-error for="type_of_code" />
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group no-margin">
                                <label for="type_of_discount" class="control-label">{{__('text.Type of discount')}}</label>
                                <select wire:model="type_of_discount" class="form-control" id="type_of_discount">
                                    <option value="amount">@lang('text.Amount')</option>
                                    <option value="percentage">@lang('text.Percentage')</option>
                                </select>
                                <x-general.input-error for="type_of_discount" />
                            </div>
                        </div>
                        <div class="col-md-12 d-flex flex-row justify-content-between px-0 mx-0">
                            <div class="form-group col-md-6">
                                @if ($typeOfDiscount == 'percentage')
                                    <label for="value" class="control-label">{{__('text.Percentage')}}</label>
                                    <input type="number" wire:model="value" class="form-control" id="value" placeholder="10%">
                                @else
                                    <label for="value" class="control-label">{{__('text.Amount')}}</label>
                                    <input type="number" wire:model="value" class="form-control" id="value" placeholder="20 @lang("text.SAR")">
                                @endif

                                <x-general.input-error for="value" />
                            </div>
                            <div class="form-group col-md-6">
                                @if ($typeOfDiscount == 'percentage')
                                    <label for="condition" class="control-label">{{__('text.The maximum discount')}}</label>
                                    <input type="number" wire:model="condition" class="form-control" id="condition" placeholder="80 @lang("text.SAR")">
                                @else
                                    <label for="condition" class="control-label">{{__('text.The minimum purchase')}}</label>
                                    <input type="number" wire:model="condition" class="form-control" id="condition" placeholder="200 @lang("text.SAR")">
                                @endif
                               <x-general.input-error for="condition" />
                            </div>
                        </div>
                    </div>

                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary waves-effect" data-dismiss="modal">{{__('text.Close')}}</button>
                <button type="button" class="btn btn-info waves-effect waves-light" wire:click.prevent="store">{{__('text.Save')}}</button>
            </div>
        </div>
    </div>
</div>
