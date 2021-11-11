<?php

namespace App\Http\Livewire\Admin\Profile;

use Illuminate\Support\Facades\Auth;
use App\Http\Actions\DisableTwoFactorAuthentication;
use App\Http\Actions\EnableTwoFactorAuthentication;
use App\Http\Actions\GenerateNewRecoveryCodes;
use App\Http\Controllers\admin\Features;
use App\Traits\ConfirmsPasswords;
use Livewire\Component;

class TwoFactorAuthenticationForm extends Component
{
    use ConfirmsPasswords;


    public $showingQrCode = false;


    public $showingRecoveryCodes = false;


    public function enableTwoFactorAuthentication(EnableTwoFactorAuthentication $enable)
    {

        if (Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword')) {
            $this->ensurePasswordIsConfirmed();
        }

        $enable(Auth::user());

        $this->showingQrCode = true;
        $this->showingRecoveryCodes = true;
    }

    /*
     * Display the user's recovery codes.
     *
     * @return void
     */
    public function showRecoveryCodes()
    {
        if (Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword')) {
            $this->ensurePasswordIsConfirmed();
        }

        $this->showingRecoveryCodes = true;
    }

    /*
     * Generate new recovery codes for the user.
     *
     * @param  \Laravel\Fortify\Actions\GenerateNewRecoveryCodes  $generate
     * @return void
     */
    public function regenerateRecoveryCodes(GenerateNewRecoveryCodes $generate)
    {
        if (Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword')) {
            $this->ensurePasswordIsConfirmed();
        }

        $generate(Auth::user());

        $this->showingRecoveryCodes = true;
    }

    /*
     * Disable two factor authentication for the user.
     *
     * @param  \Laravel\Fortify\Actions\DisableTwoFactorAuthentication  $disable
     * @return void
     */
    public function disableTwoFactorAuthentication(DisableTwoFactorAuthentication $disable)
    {
        if (Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword')) {
            $this->ensurePasswordIsConfirmed();
        }

        $disable(Auth::user());
    }

    /*
     * Get the current user of the application.
     *
     * @return mixed
     */
    public function getUserProperty()
    {
        return Auth::user();
    }

    /*
     * Determine if two factor authentication is enabled.
     *
     * @return bool
     */
    public function getEnabledProperty()
    {
        return ! empty($this->user->two_factor_secret);
    }

    /*
     * Render the component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('admin.Profile.two-factor-authentication-form');
    }
}
