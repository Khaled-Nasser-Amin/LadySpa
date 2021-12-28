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
                    <x-general.label for="session_rooms_limitation_indoor" value="{{ __('text.Number of Sessions\' rooms indoor') }}" />
                    <x-general.input id="session_rooms_limitation_indoor" type="integer" class="{{ $errors->has('name') ? 'is-invalid' : '' }}" wire:model.defer="session_rooms_limitation_indoor" autocomplete="name" />
                    <x-general.input-error for="session_rooms_limitation_indoor" />
                </div>
                 <!-- store name -->
                 <div class="form-group">
                    <x-general.label for="session_rooms_limitation_outdoor" value="{{ __('text.Number of Sessions\' rooms outdoor') }}" />
                    <x-general.input id="session_rooms_limitation_outdoor" type="integer" class="{{ $errors->has('session_rooms_limitation_outdoor') ? 'is-invalid' : '' }}" wire:model.defer="session_rooms_limitation_outdoor" />
                    <x-general.input-error for="session_rooms_limitation_outdoor" />
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
