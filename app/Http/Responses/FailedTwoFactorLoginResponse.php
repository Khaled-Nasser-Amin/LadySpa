<?php

namespace App\Http\Responses;

use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Validator as ValidatorFacade;

class FailedTwoFactorLoginResponse
{
    /*
     * Create an HTTP response that represents the object.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function toResponse($request)
    {
        if ($request->code){
            tap(ValidatorFacade::make([], []), function ($validator){
                $validator->errors()->add('code',__('text.The provided two factor authentication code was invalid.'));

            });
        }else{
            tap(ValidatorFacade::make([], []), function ($validator){
                $validator->errors()->add('recovery_code', __('text.The provided two factor authentication recovery_code was invalid.'));
            });
        }
        if ($request->code){
            return Response::view('admin.Auth.two-factor-challenge');
        }else{
            return Response::view('admin.Auth.two-factor-challenge');
        }
    }
}
