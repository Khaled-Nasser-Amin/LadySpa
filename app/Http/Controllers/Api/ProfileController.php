<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\SendCode;
use App\Models\Setting;
use App\Traits\ImageTrait;
use App\Traits\Responses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    use ImageTrait,Responses;

    //image
    public function changeImage(Request $request){
        app()->setlocale($request->lang);
        $user=$request->user();
        if($request->image){

            if ($user->getAttributes()['image']){
                $old_image=$user->getAttributes()['image'];
            }
            $file=$this->AddSingleImage($request,storage_path('app\public\users\\'));
            $user->update([
                'image' => $file,
            ]);
            if(isset($old_image)){
                unlink(storage_path('app\public\users\\').$old_image);
            }
            $user->save();
            return response()->json($user->image,200);
        }else{
            return $this->error(__('text.Image Required'),404);
        }
    }

    public function changeName(Request $request){
        app()->setlocale($request->lang);
        $user=$request->user();
        $rule=['name' => 'required|string|max:255|min:3'];
        $validator=$this->validation($request,$rule);
        if ($validator->fails()){
            $errors=collect($validator->errors())->map(function($q){
                return $q[0];
            });
            return response()->json($errors,404);
        }

        $user->update([
            'name' => $request->name,
        ]);
        $user->save();
        return response()->json($user->name,200);
    }


    public function changePassword(Request $request){
        app()->setlocale($request->lang);
        $user=$request->user();
        $rule=[
            'password' => 'required|string|min:8',
            'old_password' => 'required|string|min:8',
            'password_confirmation' => 'required|string|min:8|same:password'
        ];
        $validator=$this->validation($request,$rule);
        if ($validator->fails()){
            $errors=collect($validator->errors())->map(function($q){
                return $q[0];
            });
            return response()->json($errors,404);
        }

        if(Hash::check($request->old_password, $user->password)){
            $user->update([
                'password' => bcrypt($request->password)
            ]);
            return $this->success($request->password,__('text.Updated Successfully'),200);
        }else{
            return response()->json(['old_password'=>__('text.Invalid Old Password')],404);
        }
    }


    public function changeEmail(Request $request){
        $user=$this->emailValidation($request);
        if(!isset($user->id)){
            return $user;
        }
        if($user->email == $request->email){
            return response()->json(['email'=>__('text.You already have this email')],404);
        }
        $code=implode('',array_rand([1,2,3,4,5,6,0,7,8,9],6));
        $user->update(['code' => $code]);
        Mail::to($request->email)->send(new SendCode($code,$user->name));
        return $this->success($request->email,__('text.We have sent a verification code to your email').":".$request->email,200);
    }


    public function checkEmailOtp(Request $request){
        $user=$this->emailValidation($request);
        if(!isset($user->id)){
            return $user;
        }
        if($request->code == $user->code && isset($request->email)){
            $user->update(['email' => $request->email,'code' => null]);
            $user->save();
            return $this->success($request->email,__('text.Updated Successfully'),200);
        }else{
            return response()->json(['code'=>__('text.Invalid Code!')],404);
        }

    }

    protected function emailValidation($request){
        app()->setlocale($request->lang);
        $user=$request->user();
        $rule=['email' => 'required|email|unique:customers,email,'.$user->id,];
        $validator=$this->validation($request,$rule);
        if ($validator->fails()){
            $errors=collect($validator->errors())->map(function($q){
                return $q[0];
            });
            return response()->json($errors,404);
        }

        return $user;
    }
    public function changePhone(Request $request){
        $user=$this->phoneValidation($request);
        if(!isset($user->id)){
            return $user;
        }
        $code=implode('',array_rand([3,7,1,4,5,6,0,2,8,9],6));
        $user->update(['code' => $code]);
        $setting=Setting::find(1);
        if($setting->twillo_token && $setting->twillo_phone && $setting->twillo_sid){
            send_sms('+2'.$user->phone,__('text.Verification code').$user->code);
            return $this->success(['phone'=>$request->phone,'status'=>true],__('text.We have sent a verification code to your number').":".$request->phone,200);

        }else{
            Mail::to($user->email)->send(new SendCode($code,$user->name));
            return $this->success(['phone'=>$request->phone,'status'=>false],__('text.We have sent a verification code to your email').":".$user->email,200);
        }
    }

    public function checkPhoneOtp(Request $request){
        $user=$this->phoneValidation($request);
        if(!isset($user->id)){
            return $user;
        }
        if(isset($request->phone) && $request->code == $user->code ){
            $user->update(['phone' => $request->phone,'code' => null]);
            $user->save();
            return $this->success($request->phone,__('text.Updated Successfully'),200);
        }else{
            return response()->json(['code'=>__('text.Invalid Code!')],404);
        }
    }

    protected function phoneValidation($request){
        app()->setlocale($request->lang);
        $user=$request->user();
        $rule=['phone' => 'required|numeric|unique:customers,phone,'.$user->id];
        $validator=$this->validation($request,$rule);
        if ($validator->fails()){
            $errors=collect($validator->errors())->map(function($q){
                return $q[0];
            });
            return response()->json($errors,404);
        }
        if($user->phone == $request->phone){
            return response()->json(['phone'=>__('text.You already have this phone')],404);
        }
        return $user;
    }

    protected function validation($request,$rule,$message=[]){
        return  Validator::make($request->all(),$rule,$message);

    }


    protected function resend(Request $request){
        app()->setlocale($request->lang);
        $user=$request->user();
        $code=implode('',array_rand([3,7,1,4,5,6,0,2,8,9],6));
        $user->update(['code' => $code]);
        if(isset($request->phone)){
            $setting=Setting::find(1);
            if($setting->twillo_token && $setting->twillo_phone && $setting->twillo_sid){
                send_sms('+2'.$user->phone,__('text.Verification code').$user->code);
                return $this->success($request->phone,__('text.We have sent a verification code to your number').":".$request->phone,200);

            }else{
                Mail::to($user->email)->send(new SendCode($code,$user->name));
                return $this->success($request->phone,__('text.We have sent a verification code to your email').":".$user->email,200);

            }
        }elseif(isset($request->email)){
            Mail::to($request->email)->send(new SendCode($code,$user->name));
            return $this->success($request->phone,__('text.We have sent a verification code to your email').":".$request->email,200);

        }else{
            return response()->json(['code'=>__('text.Invalid data!')],404);
        }


    }

}
