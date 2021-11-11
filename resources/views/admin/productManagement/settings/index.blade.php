@section('title',__('text.Settings'))
@push('css')
    @livewireStyles
    <script type="module" src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js"></script>
    <link rel="stylesheet" href="{{asset('css/toast.style.min.css')}}">

@endpush
    <div class="content">
        <!-- Start Content-->
        <div class="container-fluid pt-2">

            <!-- start page title -->
            <x-admin.general.page-title>
                <li class="breadcrumb-item active">{{__('text.Settings')}}</li>
                <li class="breadcrumb-item active"><a href="{{route('admin.index')}}">{{__('text.Dashboard')}}</a></li>
                <x-slot name="title">
                    <h4 class="page-title">{{__('text.Settings')}}</h4>
                </x-slot>
            </x-admin.general.page-title>

            @include('admin.partials.success')

            <div class="row mt-5" x-data="{ edit : false }">
                <h2>@lang('text.Payment Token')</h2>
                <div class="col-12" x-show="!edit">
                    <form wire:submit.prevent class="row">
                        <textarea name="" id="" cols="30" rows="5" class="form-control col-11" disabled>{{ $payment_token }}</textarea>
                        <button  x-on:click="edit=true" class="btn btn-secondary col-1">@lang('text.Edit')</button>
                    </form>
                </div>

                <div class="col-12" x-show="edit">
                    <form wire:submit.prevent="updatePaymentToken" class="row">
                        <textarea name="" id="" wire:model="payment_token" cols="30" rows="5" class="form-control col-11">{{ $payment_token }}</textarea>
                        <button x-on:click="edit=false" class="btn btn-primary col-1">@lang('text.Save')</button>
                    </form>
                </div>
            </div>

            <div class="row mt-5" x-data="{ sms : false }">
                <h2>@lang('text.SMS Configuration')</h2>
                <div class="col-12" x-show="!sms">
                    <form wire:submit.prevent class="row">
                        <div class="form-group col-12">
                            <label for="twillo_token">Twillo Token</label>
                            <input id="twillo_token" class="form-control d-block col-6" disabled value="{{ $twillo_token }}">
                        </div>
                        <div class="form-group col-12">
                            <label for="twillo_phone">Twillo Phone</label>
                            <input id="twillo_phone" class="form-control d-block col-6" disabled value="{{ $twillo_phone }}">
                            <x-general.input-error for="twillo_phone" />

                        </div>
                        <div class="form-group col-12">
                            <label for="twillo_sid">Twillo Sid</label>
                            <input id="twillo_sid" class="form-control d-block col-6" disabled value="{{ $twillo_sid }}">
                        </div>
                        <div class="form-group col-12">
                            <button  x-on:click="sms=true" class="btn btn-secondary col-1">@lang('text.Edit')</button>
                        </div>
                    </form>
                </div>

                <div class="col-12" x-show="sms">
                    <form wire:submit.prevent="updateTwilloConfiguration" class="row">
                        <div class="form-group col-12">
                            <label for="twillo_token">Twillo Token</label>
                            <input id="twillo_token"  type="text"   class="form-control d-block col-6" wire:model="twillo_token"  value="{{ $twillo_token }}">
                        </div>
                        <div class="form-group col-12">

                            <label for="twillo_phone">Twillo Phone</label>
                            <input id="twillo_phone"  type="text"  class="form-control d-block col-6" wire:model="twillo_phone"  value="{{ $twillo_phone }}">
                            <x-general.input-error for="twillo_phone" />

                        </div>
                        <div class="form-group col-12">
                            <label for="twillo_sid">Twillo Sid</label>
                            <input id="twillo_sid"  type="text"  class="form-control d-block col-6" wire:model="twillo_sid"  value="{{ $twillo_sid }}">
                        </div>
                        <div class="form-group col-12">
                            <button x-on:click="sms=false" class="btn btn-primary col-1">@lang('text.Save')</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="row mt-5" x-data="{ contact : false }">
                <h2>@lang('text.Contact Information')</h2>
                <div class="col-12" x-show="!contact">
                    <form wire:submit.prevent class="row">
                        <div class="form-group col-12">
                            <label for="email">@lang('text.Email')</label>
                            <input id="email" class="form-control d-block col-6" disabled value="{{ $contact_email }}">
                        </div>
                        <div class="form-group col-12">
                            <label for="phone">@lang('text.Phone Number')</label>
                            <input id="phone" class="form-control d-block col-6" disabled value="{{ $contact_phone }}">
                            <x-general.input-error for="contact_phone" />
                        </div>
                        <div class="form-group col-12">
                            <label for="whatsapp">@lang('text.WhatsApp')</label>
                            <input id="whatsapp" class="form-control d-block col-6" disabled value="{{ $contact_whatsapp }}">
                            <x-general.input-error for="contact_whatsapp" />

                        </div>
                        <div class="form-group col-12">
                            <label for="land_line">@lang('text.Land Line')</label>
                            <input id="land_line" class="form-control d-block col-6" disabled value="{{ $contact_land_line }}">
                            <x-general.input-error for="contact_land_line" />

                        </div>
                        <div class="form-group col-12">
                            <button  x-on:click="contact=true" class="btn btn-secondary col-1">@lang('text.Edit')</button>
                        </div>
                    </form>
                </div>

                <div class="col-12" x-show="contact">
                    <form wire:submit.prevent="updateContactInformation" class="row">
                        <div class="form-group col-12">
                            <label for="email">@lang('text.Email')</label>
                            <input type="text" id="email" class="form-control d-block col-6" wire:model="contact_email" value="{{ $contact_email }}">
                        </div>
                        <div class="form-group col-12">
                            <label for="phone">@lang('text.Phone Number')</label>
                            <input  type="text" id="phone" class="form-control d-block col-6"  wire:model="contact_phone" value="{{ $contact_phone }}">
                            <x-general.input-error for="contact_phone" />

                        </div>
                        <div class="form-group col-12">
                            <label for="whatsapp">@lang('text.WhatsApp')</label>
                            <input type="text" id="whatsapp" class="form-control d-block col-6"  wire:model="contact_whatsapp" value="{{ $contact_whatsapp }}">
                            <x-general.input-error for="contact_whatsapp" />

                        </div>
                        <div class="form-group col-12">
                            <label for="land_line">@lang('text.Land Line')</label>
                            <input type="text" id="land_line" class="form-control d-block col-6"  wire:model="contact_land_line" value="{{ $contact_land_line }}">
                            <x-general.input-error for="contact_land_line" />

                        </div>
                        <div class="form-group col-12">
                            <button  x-on:click="contact=false" class="btn btn-primary col-1">@lang('text.Save')</button>
                        </div>
                    </form>
                </div>
            </div>


        </div>
    </div>
@push('script')
    @livewireScripts

    <script src="{{asset('js/toast.script.js')}}"></script>
    <script>
        window.addEventListener('success',e=>{
            $.Toast(e.detail,"",'success',{
                stack: false,
                position_class: "toast-top-center",
                rtl: {{app()->getLocale()=='ar' ? "true" : 'false'}}
            });
        })
    </script>

@endpush

