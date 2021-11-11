<?php

namespace App\Http\Controllers\Api\delivery_service_provider;

use App\Http\Controllers\Controller;
use App\Mail\SendCode;
use App\Models\DeliveryServiceProvider;
use App\Models\Setting;
use App\Traits\ImageTrait;
use App\Traits\Responses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    use Responses,ImageTrait;
    public function __construct(){
        $this->middleware(['Auth:delivery_service_provider_api','scope:delivery'])->only(['logout']);
    }


    //authentication
    public function login(Request $request)
    {
        app()->setlocale($request->lang);
        $delivery=DeliveryServiceProvider::where('phone',$request->username)
        ->orWhere('email',$request->username)
        ->first();
        if ($delivery){
            if (Hash::check($request->password,$delivery->password)){
                if($delivery->activation != 0 ){
                    Auth::guard('delivery_service_provider')->login($delivery);
                    $token=$delivery->createToken('delivery_service_provider token',['delivery'])->accessToken;
                    return $this->success(['user'=> $delivery,'token' => $token],'logged in successfully' , 200);

                }else{
                    return $this->generateCode($delivery,400);
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
        auth()->guard('delivery_service_provider_api')->user()->token()->delete();
        auth()->guard('delivery_service_provider')->logout();
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
        $data=$request->except(['password','driving_license','personal_id','image']);
        $data['password']=bcrypt($request['password']);
        $data['image']=$this->store_images($request->file('image'));
        $data['driving_license']=$this->store_images($request->file('driving_license'));
        $data['personal_id']=$this->store_images($request->file('personal_id'));
        $code=substr(str_shuffle('1234567890'),0,6);
        $data['code']=$code;
        $delivery=DeliveryServiceProvider::create($data);
        return $this->SendCode($delivery,200);
    }



    //store delivery images
    protected function store_images($image)
    {
        $fileName=time().'_'.$image->getClientOriginalName();
        $image->move(public_path('images\users\\'),$fileName);
        return $fileName;
    }

    public function activeAccount(Request $request){
        app()->setlocale($request->lang);
        $delivery=DeliveryServiceProvider::find($request->user_id);
        if($delivery && $delivery->activation == 0 && $request->code == $delivery->code){
            $delivery->update(['activation' => 1,'code' => null]);
            $delivery->save();
            Auth::guard('delivery_service_provider')->login($delivery);
            $token=$delivery->createToken('delivery_service_provider token',['delivery'])->accessToken;
            return $this->success(['user'=> $delivery,'token' => $token],__('text.Created Successfully'),200);
        }else{
            return $this->error(__('text.Invalid Code!'),404);
        }
    }




    //forget password
    public function forgetPassword(Request $request){
        app()->setlocale($request->lang);

        $delivery=DeliveryServiceProvider::where('phone',$request->username)
            ->orWhere('email',$request->username)->first();
        if($delivery){
            $code=substr(str_shuffle('1234567890'),0,6);
            $delivery->update(['code' => $code]);
            $delivery->save();

            if(filter_var($request->username ,FILTER_VALIDATE_EMAIL)){
                Mail::to($delivery->email)->send(new SendCode($delivery->code,$delivery->name));
                return $this->success(['user' => $delivery],__('text.We have sent a verification code to your email').":".$delivery->email,200);
            }elseif(is_numeric($request->username)){
                return $this->SendCode($delivery,200);
            }
        }else{
            return $this->error(__('text.User does not exist in our records'),404);
        }
    }

    public function checkOtp(Request $request){ // user_id  , code
        app()->setlocale($request->lang);
        $delivery=DeliveryServiceProvider::find($request->user_id);
        if($delivery) {
            if ($request->code == $delivery->code) {
                $delivery->update([
                    'code' => null,
                ]);
                $delivery->save();
                return $this->success(['user' => $delivery,'key' => $delivery->password],'', 200);
            } else {
                return $this->error(__('text.Invalid Code!'), 404);
            }
        }else{
            return $this->error(__('text.User does not exist in our records'),404);
        }
    }

    public function changePassword(Request $request){ // user_id lang password / key
        app()->setlocale($request->lang);

        $validator=Validator::make($request->all(),['password' => 'required|min:8','key' => 'required']);
        if ($validator->fails()){
            return response()->json($validator->errors(),404);
        }
        $delivery=DeliveryServiceProvider::find($request->user_id);
        if($delivery){
            if($request->key == $delivery->password) {
                $delivery->update([
                    'password' => bcrypt($request->password),
                ]);

                $delivery->save();
                Auth::guard('delivery_service_provider')->login($delivery);
                $token=$delivery->createToken('delivery_service_provider token',['delivery'])->accessToken;
                return $this->success(['user'=> $delivery,'token' => $token],__('text.Password Changed Successfully'),200);
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
        $delivery=DeliveryServiceProvider::where('phone',$request->phone)->Where('email',$request->email)->first();
        return $this->generateCode($delivery,200);
    }

    protected function generateCode($delivery,$status){
        if ($delivery){
            $code=substr(str_shuffle('1234567890'),0,6);
            $delivery->update(['code' => $code]);
            $delivery->save();
            return $this->SendCode($delivery,$status);
        }else{
            return $this->error(__('text.User does not exist in our records'),404);
        }
    }


    protected function rules(){
        return [
            'name' => 'required|string|max:255',
            'password' => 'required|string|min:8',
            'driving_license' => 'required|mimes:jpg,png,jpeg,gif,webp',
            'image' => 'required|mimes:jpg,png,jpeg,gif,webp',
            'personal_id' => 'required|mimes:jpg,png,jpeg,gif,webp',
            'phone' => 'required|numeric|unique:delivery_service_providers',
            'email' => 'required|email|max:255|unique:delivery_service_providers',
        ];
    }


    protected function SendCode($delivery,$status){
        $setting=Setting::find(1);
        if($setting->twillo_token && $setting->twillo_phone && $setting->twillo_sid){
            send_sms('+2'.$delivery->phone,__('text.Verification code').$delivery->code);
            return $this->success(['user' => $delivery],__('text.We have sent a verification code to your number').":".$delivery->phone,$status);
        }else{
            Mail::to($delivery->email)->send(new SendCode($delivery->code,$delivery->name));
            return $this->success(['user' => $delivery],__('text.We have sent a verification code to your email').":".$delivery->email,$status);

        }
    }


}
