@extends('admin.layouts.app')
@section('title', __('text.Two Factor Authentication'))
@push('css')
    <script type="module" src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js"></script>

@endpush
@section('content')
    <x-general.authentication-card>


        <div class="card-body bg-dark text-white">
            <x-general.input-error for="code"></x-general.input-error>
            <x-general.input-error for="recovery_code"></x-general.input-error>

            <div x-data="{ recovery: false }">
                <div class="mb-3" x-show="! recovery">
                    {{ __('text.Please confirm access to your account by entering the authentication code provided by your authenticator application.') }}
                </div>

                <div class="mb-3" x-show="recovery">
                    {{ __('text.Please confirm access to your account by entering one of your emergency recovery codes.') }}
                </div>
                @include('admin.partials.errors')
                <form method="POST" action="{{ route('two-factor.login') }}">
                    @csrf

                    <div class="form-group" x-show="! recovery">
                        <x-general.label value="{{ __('text.Code') }}" />
                        <x-general.input class="{{ $errors->has('code') ? 'is-invalid' : '' }}" type="text"
                                     inputmode="numeric" name="code" autofocus x-ref="code" autocomplete="one-time-code" />
                        @error('code')
                        <span class="is-invalid text-danger" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror

                    </div>

                    <div class="form-group" x-show="recovery">
                        <x-general.label value="{{ __('text.Recovery Code') }}" />
                        <x-general.input class="{{ $errors->has('recovery_code') ? 'is-invalid' : '' }}" type="text"
                                     name="recovery_code" x-ref="recovery_code" autocomplete="one-time-code" />
                        @error('recovery_code')
                        <span class="is-invalid text-danger" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-end mt-3">
                        <button id="showRecovery"  type="button" class="btn btn-outline-light mx-2"
                                x-show="! recovery"
                                x-on:click="
                                            recovery = true;
                                            $nextTick(() => { $refs.recovery_code.focus() })
                                        ">
                            {{ __('text.Use a recovery code') }}
                        </button>

                        <button id="showCode" type="button" class="btn btn-outline-light mx-2"
                                x-show="recovery"
                                x-on:click="
                                            recovery = false;
                                            $nextTick(() => { $refs.code.focus() })
                                        ">
                            {{ __('text.Use an authentication code') }}
                        </button>

                        <x-general.button class="btn btn-outline-light">
                            {{ __('text.Log in') }}
                        </x-general.button>
                    </div>
                </form>
            </div>
        </div>
    </x-general.authentication-card>
@endsection
@push('js')

    @if ($errors->has('code'))
        <script>
            $(window).on('load',function (){
                $('#showCode').click();
            })
        </script>
    @elseif($errors->has('recovery_code'))
        <script>
            $(window).on('load',function (){
                $('#showRecovery').click();
            })
        </script>
    @endif
@endpush
