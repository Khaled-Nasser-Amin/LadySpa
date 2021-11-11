@props(['title' => __('text.Confirm Password'), 'content' => __('text.For your security, please confirm your password to continue.'), 'button' => __('text.Confirm')])

@php
    $confirmableId = md5($attributes->wire('then'));
@endphp

<span
    {{ $attributes->wire('then') }}
    x-data
    x-ref="span"
    x-on:click="$wire.startConfirmingPassword('{{ $confirmableId }}')"
    x-on:password-confirmed.window="setTimeout(() => $event.detail.id === '{{ $confirmableId }}' && $refs.span.dispatchEvent(new CustomEvent('then', { bubbles: false })), 250);"
>
    {{ $slot }}
</span>

@once
<x-general.dialog-modal wire:model="confirmingPassword">
    <x-slot name="title">
        {{ $title }}
    </x-slot>

    <x-slot name="content">
        {{ $content }}

        <div class="mt-4" x-data="{}" x-on:confirming-password.window="setTimeout(() => $refs.confirmable_password.focus(), 250)">
            <x-general.input type="password" class="{{ $errors->has('confirmable_password') ? 'is-invalid' : '' }}" placeholder="{{ __('text.Password') }}"
                         x-ref="confirmable_password"
                         wire:model.defer="confirmablePassword"
                         wire:keydown.enter="confirmPassword" />

            <x-general.input-error for="confirmable_password" />

        </div>
    </x-slot>

    <x-slot name="footer">
        <x-general.secondary-button wire:click="stopConfirmingPassword" wire:loading.attr="disabled">
            {{ __('text.Cancel') }}
        </x-general.secondary-button>

        <x-general.button class="ml-2" wire:click="confirmPassword" wire:loading.attr="disabled">
            {{ $button }}
        </x-general.button>
    </x-slot>
</x-general.dialog-modal>
@endonce
