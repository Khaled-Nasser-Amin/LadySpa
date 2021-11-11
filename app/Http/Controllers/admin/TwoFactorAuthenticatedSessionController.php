<?php

namespace App\Http\Controllers\admin;

use App\Providers\RouteServiceProvider;
use Illuminate\Routing\Controller;
use App\Http\Requests\TwoFactorLoginRequest;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

class TwoFactorAuthenticatedSessionController extends Controller
{


    /*
     * Attempt to authenticate a new session using the two factor authentication code.
     *
     * @param  \Laravel\Fortify\Http\Requests\TwoFactorLoginRequest  $request
     * @return mixed
     */
    public function store(TwoFactorLoginRequest $request)
    {
        $user = $request->challengedUser();

        if ($code = $request->validRecoveryCode()) {
            $user->replaceRecoveryCode($code);
        } elseif (! $request->hasValidCode()) {

            if ($request->code){
                return view('admin.Auth.two-factor-challenge')->withErrors(['code'=>__('text.The provided two factor authentication code was invalid.')]);
            }elseif($request->recovery_code){
                return view('admin.Auth.two-factor-challenge')->withErrors(['recovery_code'=>__('text.The provided two factor authentication recovery_code was invalid.')]);
            }else{
                return view('admin.Auth.two-factor-challenge')->withErrors(['recovery_code'=>__('text.Enter your two factor authentication code')]);

            }
        }

        Auth::login($user, $request->remember());

        $request->session()->regenerate();

        return redirect(RouteServiceProvider::HOME);
    }

}
