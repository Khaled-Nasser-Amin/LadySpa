<?php

namespace App\Http\Controllers\admin\Profile;

use App\Traits\ImageTrait;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Http\Contracts\UpdatesUserProfileInformation;

class UpdateUserProfileInformation implements UpdatesUserProfileInformation
{

    use ImageTrait;
    /*
     * Validate and update the given user's Profile information.
     *
     * @param  mixed  $user
     * @param  array  $input
     * @return void
     */
    public function update($user, array $input)
    {

        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'store_name' => ['required', 'string', 'max:255'],
            'phone' => ['numeric'],
            'whatsapp' => ['numeric'],
            'photo' => ['nullable', 'mimes:jpg,jpeg,png', 'max:1024'],
        ])->validateWithBag('updateProfileInformation');

        if (isset($input['photo']))
        {
            $request['image']=$input['photo'];
            $data=$this->livewireAddSingleImage($request,$data=[],'users');
            $this->livewireDeleteSingleImage($user,'users');
            $user->update([
                'image' => $data['image']
            ]);
            $user->save();
        }
        $user->forceFill([
            'name' => $input['name'],
            'phone' => $input['phone'],
            'store_name' => $input['store_name'],
            'whatsapp' => $input['whatsapp'],
        ])->save();

    }

    /*
     * Update the given verified user's Profile information.
     *
     * @param  mixed  $user
     * @param  array  $input
     * @return void
     */
    protected function updateVerifiedUser($user, array $input)
    {
        $user->forceFill([
            'name' => $input['name'],
            'phone' => $input['phone'],
            'store_name' => $input['store_name'],
            'whatsapp' => $input['whatsapp'],
        ])->save();

        $user->sendEmailVerificationNotification();
    }


}
