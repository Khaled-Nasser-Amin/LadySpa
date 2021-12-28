<x-general.form-section submit="updateGeoLocation">
    <x-slot name="title">
        {{ __('text.Update sessions configration.') }}
    </x-slot>

    <x-slot name="description">
        {{ __('text.Update Number of available sessions rooms & number of outdoor service provider & opening and closing time.') }}
    </x-slot>

    <x-slot name="form">
        <x-general.action-message on="saved">
            {{ __('text.Saved.') }}
        </x-general.action-message>
        <div class="w-md-75">
                <!-- Name -->
                <div class="form-group">
                    <x-general.label for="name" value="{{ __('text.Number of Sessions\' rooms indoor') }}" />
                    <x-general.input id="name" type="text" class="{{ $errors->has('name') ? 'is-invalid' : '' }}" wire:model.defer="state.name" autocomplete="name" />
                    <x-general.input-error for="name" />
                </div>
                 <!-- store name -->
                 <div class="form-group">
                    <x-general.label for="store_name" value="{{ __('text.Store Name') }}" />
                    <x-general.input id="store_name" type="text" class="{{ $errors->has('store_name') ? 'is-invalid' : '' }}" wire:model.defer="state.store_name" />
                    <x-general.input-error for="store_name" />
                </div>
                <div class="form-group">
                    <x-general.label for="opening_time" value="{{ __('text.Opening time') }}" />
                    <x-general.input id="opening_time" type="time" class="{{ $errors->has('opening_time') ? 'is-invalid' : '' }}" wire:model.defer="opening_time" autocomplete="name" />
                    <x-general.input-error for="opening_time" />
                </div>
                 <!-- store name -->
                 <div class="form-group">
                    <x-general.label for="closing_time" value="{{ __('text.Closing time') }}" />
                    <x-general.input id="closing_time" type="time" class="{{ $errors->has('closing_time') ? 'is-invalid' : '' }}" wire:model.defer="closing_time" />
                    <x-general.input-error for="closing_time" />
                </div>
        </div>
    </x-slot>

    <x-slot name="actions">
        <x-general.button>
            {{ __('text.Save') }}
        </x-general.button>
    </x-slot>
</x-general.form-section>

@push('script')
    {{-- //google map --}}


@endpush
