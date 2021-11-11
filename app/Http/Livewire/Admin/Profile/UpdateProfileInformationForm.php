<?php

namespace App\Http\Livewire\Admin\Profile;

use App\Http\Controllers\admin\Profile\UpdateUserProfileInformation;
use App\Mail\SendCode;
use App\Traits\ImageTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;

class UpdateProfileInformationForm extends Component
{
    use WithFileUploads,ImageTrait;

    /*
     * The component's state.
     *
     * @var array
     */
    public $state = [];

    /*
     * The new avatar for the user.
     *
     * @var mixed
     */
    public $photo;
    public $code;

    /*
     * Prepare the component.
     *
     * @return void
     */
    public function mount()
    {

        $this->state = Auth::user()->withoutRelations()->toArray();
    }

    /*/**
     * Update the user's Profile information.*/

    public function updateProfileInformation(UpdateUserProfileInformation $updater)
    {
        $this->resetErrorBag();
        if($this->state['email'] != auth()->user()->email){
            $this->validate(['state.email' => ['required', 'email', 'max:255', Rule::unique('users','email')->ignore(auth()->user()->id)]]);
            session()->put('email',$this->state['email']);
            $this->resend();

        }

        $data=collect($this->state)->except('email')->toArray();
        $updater->update(
            Auth::user(),
            $this->photo
                ? array_merge($data, ['photo' => $this->photo])
                : $data
        );


        $this->refreshNavbar();
    }

    public function updateEmail(){
        if (session()->has('time') && time() < (session()->get('time')+(5*60)) ){
            if(auth()->user()->code == $this->code){
                if(session()->has('email')){
                    $email=session()->pull('email');
                    auth()->user()->update(['email'=>$email,'code'=>null]);
                    auth()->user()->save();
                    $this->refreshNavbar();
                    $this->cancel();
                    $this->code=null;
                }
            }else{
                $this->addError('code',__('text.Invalid Code!'));
                $this->dispatchBrowserEvent('danger',__('text.Invalid Code!'));
            }
        }else{
            $this->addError('code',__('text.CODE EXPIRED,please resend the activation code or cancel the operation.'));
            $this->dispatchBrowserEvent('danger',__('text.CODE EXPIRED,please resend the activation code or cancel the operation.'));
        }

    }
    public function resend(){
        $user=auth()->user();
        $code=implode('',array_rand([0,1,2,3,4,5,6,7,8,9],6));
        $user->update(['code' => $code]);
        $user->save();
        session()->put('email',session()->get('email'));
        Mail::to(session()->get('email'))->send(new SendCode($user->code,$user->name));
        session()->put('activeCodeField','');
        session()->put('time',time());
        $this->dispatchBrowserEvent('success',__('text.Message has been sent successfully'));
        $this->dispatchBrowserEvent('refreshCode',session()->get('time'));

    }

    public function cancel(){
+-        session()->forget('time');
        session()->forget('email');
        session()->forget('activeCodeField');
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


    public function deleteProfilePhoto(){
        $this->livewireDeleteSingleImage($this->getUserProperty(),'users');
        $this->getUserProperty()->update([
            'image' => null
        ]);
        $this->emit('saved');
        $this->emit('refresh-navbar',route('profile.show'));
    }

    /*
     * Render the component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('admin.Profile.update-profile-information-form');
    }
    protected function refreshNavbar(){
        if(auth()->user()->wasChanged()){
            $this->emit('saved');
            $this->emit('refresh-navbar',route('profile.show'));
        }
    }
}
