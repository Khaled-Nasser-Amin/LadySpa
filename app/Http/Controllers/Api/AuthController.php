<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\SendCode;
use App\Models\Customer;
use App\Models\Setting;
use App\Traits\Responses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    use Responses;
    public function __construct(){
        $this->middleware(['Auth:customer_api','scope:customer'])->only(['logout']);
    }


    //authentication
    public function login(Request $request)
    {
        app()->setlocale($request->lang);
        $user=Customer::where('phone',$request->username)
        ->orWhere('email',$request->username)
        ->first();
        if ($user){
            if (Hash::check($request->password,$user->password)){
                if($user->activation != 0 ){
                    Auth::guard('customer')->login($user);
                    $token=$user->createToken('customer token',['customer'])->accessToken;
                    return $this->success(['user'=> $user,'token' => $token],'logged in successfully' , 200);

                }else{
                    return $this->generateCode($user,400);
                }
            }else{
                return $this->error(__('text.Invalid Password'),401);
            }
        }else{
            return $this->error(__('text.User does not exist in our records'),404);
        }
    }

    public function logout()
    {
        auth()->guard('customer_api')->user()->token()->delete();
        auth()->guard('customer')->logout();
        return $this->success('','logged out successfully',200);
    }





    //register provider
    public function store(Request $request)
    {
        app()->setlocale($request->lang);
        $validator=Validator::make($request->all(),$this->rules());
        if ($validator->fails()){
            return response()->json($validator->errors(),404);
        }
        $data=$request->except('password');
        $data['password']=bcrypt($request['password']);
        $code=substr(str_shuffle('1234567890'),0,6);
        $data['code']=$code;
        $user=Customer::create($data);
        return $this->SendCode($user,200);
    }
    public function activeAccount(Request $request){
        app()->setlocale($request->lang);
        $user=Customer::find($request->user_id);
        if($user && $user->activation == 0 && $request->code == $user->code){
            $user->update(['activation' => 1,'code' => null]);
            $user->save();
            Auth::guard('customer')->login($user);
            $token=$user->createToken('customer token',['customer'])->accessToken;
            return $this->success(['user'=> $user,'token' => $token],__('text.Created Successfully'),200);
        }else{
            return $this->error(__('text.Invalid Code!'),404);
        }
    }




    //forget password
    public function forgetPassword(Request $request){
        app()->setlocale($request->lang);

        $user=Customer::where('phone',$request->username)
            ->orWhere('email',$request->username)->first();
        if($user){
            $data=$request->except('code');
            $code=substr(str_shuffle('1234567890'),0,6);
            $user->update(['code' => $code]);
            $user->save();

            if(filter_var($request->username ,FILTER_VALIDATE_EMAIL)){
                Mail::to($user->email)->send(new SendCode($user->code,$user->name));
                return $this->success(['user' => $user],__('text.We have sent a verification code to your email').":".$user->email,200);
            }elseif(is_numeric($request->username)){
                return $this->SendCode($user,200);
            }
        }else{
            return $this->error(__('text.User does not exist in our records'),404);
        }
    }

    public function checkOtp(Request $request){ // user_id  , code
        app()->setlocale($request->lang);
        $user=Customer::find($request->user_id);
        if($user) {
            if ($request->code == $user->code) {
                $user->update([
                    'code' => null,
                ]);
                $user->save();
                return $this->success(['user' => $user,'key' => $user->password],'', 200);
            } else {
                return $this->error(__('text.Invalid Code!'), 404);
            }
        }else{
            return $this->error(__('text.User does not exist in our records'),404);
        }
    }

    public function changePassword(Request $request){ // user_id lang password
        app()->setlocale($request->lang);

        $validator=Validator::make($request->all(),['password' => 'required|min:8','key' => 'required']);
        if ($validator->fails()){
            return response()->json($validator->errors(),404);
        }
        $user=Customer::find($request->user_id);
        if($user) {
            if($user->password == $request->key){
                $user->update([
                    'password' => bcrypt($request->password),
                ]);
                $user->save();
                Auth::guard('customer')->login($user);
                $token=$user->createToken('customer token',['customer'])->accessToken;
                return $this->success(['user'=> $user,'token' => $token],__('text.Password Changed Successfully'),200);

            }else{
                return $this->error(__('text.User does not exist in our records'),404);
            }
           }else{
            return $this->error(__('text.User does not exist in our records'),404);
        }
    }


    //resend code
    public function resend(Request $request){
        app()->setlocale($request->lang);
        $user=Customer::where('phone',$request->phone)->Where('email',$request->email)->first();
        return $this->generateCode($user,200);
    }

    protected function generateCode($user,$status){
        if ($user){
            $code=substr(str_shuffle('1234567890'),0,6);
            $user->update(['code' => $code]);
            $user->save();
            return $this->SendCode($user,$status);
        }else{
            return $this->error(__('text.User does not exist in our records'),404);
        }
    }


    protected function rules(){
        return [
            'name' => 'required|string|max:255',
            'password' => 'required|string|min:8',
            'phone' => 'required|numeric|unique:customers',
            'email' => 'required|email|max:255|unique:customers',
        ];
    }


    protected function SendCode($user,$status){
        $setting=Setting::find(1);
        if($setting->twillo_token && $setting->twillo_phone && $setting->twillo_sid){
            send_sms('+2'.$user->phone,__('text.Verification code').$user->code);
            return $this->success(['user' => $user],__('text.We have sent a verification code to your number').":".$user->phone,$status);
        }else{
            Mail::to($user->email)->send(new SendCode($user->code,$user->name));
            return $this->success(['user' => $user],__('text.We have sent a verification code to your email').":".$user->email,$status);

        }
    }


}
