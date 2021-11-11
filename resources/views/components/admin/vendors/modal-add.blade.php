<div wire:ignore.self id="AddNewVendor"  class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h4 class="modal-title mt-0">{{__('text.Vendor')}}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">

                <form  id="addVendor">


                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name" class="control-label">{{__('text.Name')}}</label>
                                <input type="text" wire:model="name" class="form-control" id="name"  placeholder="">
                                <x-general.input-error for="name" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="email" class="control-label">{{__('text.Email')}}</label>
                                <input type="text" wire:model="email" class="form-control" id="email" placeholder="">
                                <x-general.input-error for="email" />
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="store_name" class="control-label">{{__('text.Store Name')}}</label>
                                <input type="text" wire:model="store_name" class="form-control" id="store_name"  placeholder="">
                                <x-general.input-error for="store_name" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="location" class="control-label">{{__('text.Location')}}</label>
                                <input type="text" wire:model="location" class="form-control" id="location" placeholder="">
                                <x-general.input-error for="location" />
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="phone" class="control-label">{{__('text.Phone Number')}}</label>
                                <input type="text" wire:model="phone" class="form-control" id="phone"  placeholder="">
                                <x-general.input-error for="phone" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="whatsapp" class="control-label">{{__('text.WhatsApp')}}</label>
                                <input type="text" wire:model="whatsapp" class="form-control" id="whatsapp" placeholder="">
                                <x-general.input-error for="whatsapp" />
                            </div>
                        </div>
                    </div>

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

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group mb-4">
                                <label>{{__('text.Add Image')}}</label>
                                <input type="file"  wire:model="image" data-height="210" />
                                <x-general.input-error for="image" />
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
