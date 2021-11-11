<?php

namespace App\Http\Livewire\Admin\Profile;

use App\Http\Controllers\admin\Profile\DeleteUser;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class DeleteUserForm extends Component
{
    /*
     * Indicates if user deletion is being confirmed.
     *
     * @var bool
     */
    public $confirmingUserDeletion = false;

    /*
     * The user's current password.
     *
     * @var string
     */
    public $password = '';

    /*
     * Confirm that the user would like to delete their account.
     *
     * @return void
     */
    public function confirmUserDeletion()
    {
        // $this->resetErrorBag();

        // $this->password = '';

        // $this->dispatchBrowserEvent('confirming-delete-user');

        // $this->confirmingUserDeletion = true;
    }

    /*
     * Delete the current user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Laravel\Jetstream\Contracts\DeletesUsers  $deleter
     * @param  \Illuminate\Contracts\Auth\StatefulGuard  $Auth
     * @return \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
     */
    public function deleteUser(Request $request, DeleteUser $deleter)
    {
        // $this->resetErrorBag();

        // if (! Hash::check($this->password, Auth::user()->password)) {
        //     throw ValidationException::withMessages([
        //         'password' => [__('text.This password does not match our records.')],
        //     ]);
        // }

        // $deleter->delete(Auth::user()->fresh());

        // auth()->logout();

        // $request->session()->invalidate();
        // $request->session()->regenerateToken();

        // return redirect('/');
    }

    /*
     * Render the component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        $setting=Setting::find(1);
         return view('admin.Profile.delete-user-form',compact('setting'));
    }
}
