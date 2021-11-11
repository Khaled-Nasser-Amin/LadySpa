<x-general.action-section>
    <x-slot name="title">
        {{ __('text.Two Factor Authentication') }}
    </x-slot>

    <x-slot name="description">
        {{ __('text.Add additional security to your account using two factor authentication.') }}
    </x-slot>

    <x-slot name="content">
        <h3 class="h5 font-weight-bold">
            @if ($this->enabled)
                {{ __('text.You have enabled two factor authentication.') }}
            @else
                {{ __('text.You have not enabled two factor authentication.') }}
            @endif
        </h3>

        <p class="mt-3">
            {{ __('text.When two factor authentication is enabled, you will be prompted for a secure, random token during authentication. You may retrieve this token from your phone\'s Google Authenticator application.') }}
        </p>

        @if ($this->enabled)
            @if ($showingQrCode)
                <p class="mt-3">
                    {{ __('text.Two factor authentication is now enabled. Scan the following QR code using your phone\'s authenticator application.') }}
                </p>

                <div class="mt-3">
                    {!! $this->user->twoFactorQrCodeSvg() !!}
                </div>
            @endif

            @if ($showingRecoveryCodes)
                <p class="mt-3">
                    {{ __('text.Store these recovery codes in a secure password manager. They can be used to recover access to your account if your two factor authentication device is lost.') }}
                </p>

                <div class="bg-light rounded p-3">
                    @foreach (json_decode(decrypt($this->user->two_factor_recovery_codes), true) as $code)
                        <div>{{ $code }}</div>
                    @endforeach
                </div>
            @endif
        @endif

        <div class="mt-3">
            @if (! $this->enabled)
                <x-general.confirms-password wire:then="enableTwoFactorAuthentication">
                    <x-general.button type="button" wire:loading.attr="disabled">
                        {{ __('text.Enable') }}
                    </x-general.button>
                </x-general.confirms-password>
            @else
                @if ($showingRecoveryCodes)
                    <x-general.confirms-password wire:then="regenerateRecoveryCodes">
                        <x-general.secondary-button class="mr-3">
                            {{ __('text.Regenerate Recovery Codes') }}
                        </x-general.secondary-button>
                    </x-general.confirms-password>
                @else
                    <x-general.confirms-password wire:then="showRecoveryCodes">
                        <x-general.secondary-button class="mr-3">
                            {{ __('text.Show Recovery Codes') }}
                        </x-general.secondary-button>
                    </x-general.confirms-password>
                @endif

                <x-general.confirms-password wire:then="disableTwoFactorAuthentication">
                    <x-general.danger-button wire:loading.attr="disabled">
                        {{ __('text.Disable') }}
                    </x-general.danger-button>
                </x-general.confirms-password>
            @endif
        </div>
    </x-slot>
</x-general.action-section>
