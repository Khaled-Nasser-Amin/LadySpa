<x-general.form-section submit="updatePassword">
    <x-slot name="title">
        {{ __('text.Update Password') }}
    </x-slot>

    <x-slot name="description">
        {{ __('text.Ensure your account is using a long, random password to stay secure.') }}
    </x-slot>

    <x-slot name="form">
        <x-general.action-message on="saved">
            {{ __('text.Saved.') }}
        </x-general.action-message>
        <div class="w-md-75">
            <div class="form-group">
                <x-general.label for="current_password" value="{{ __('text.Current Password') }}" />
                <x-general.input id="current_password" type="password" class="{{ $errors->has('current_password') ? 'is-invalid' : '' }}"
                             wire:model.defer="state.current_password" autocomplete="current-password" />
                <x-general.input-error for="current_password" />
            </div>

            <div class="form-group">
                <x-general.label for="password" value="{{ __('text.New Password') }}" />
                <x-general.input id="password" type="password" class="{{ $errors->has('password') ? 'is-invalid' : '' }}"
                             wire:model.defer="state.password" autocomplete="new-password" />
                <x-general.input-error for="password" />
            </div>

            <div class="form-group">
                <x-general.label for="password_confirmation" value="{{ __('text.Confirm Password') }}" />
                <x-general.input id="password_confirmation" type="password" class="{{ $errors->has('password_confirmation') ? 'is-invalid' : '' }}"
                             wire:model.defer="state.password_confirmation" autocomplete="new-password" />
                <x-general.input-error for="password_confirmation" />
            </div>
        </div>
    </x-slot>

    <x-slot name="actions">
        <x-general.button>
            {{ __('text.Save') }}
        </x-general.button>
    </x-slot>
</x-general.form-section>
