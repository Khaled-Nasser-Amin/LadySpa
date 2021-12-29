<?php

namespace App\Http\Livewire\Front;

use App\Mail\SendCode;
use App\Models\User;
use App\Models\Vendor;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use DanHarrin\LivewireRateLimiting\WithRateLimiting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;
class Register extends Component
{
    use WithRateLimiting;
    public $name,$store_name,$email,$whatsapp,$location,$phone,$password,$password_confirmation,$code,$geoLocation='',$opening_time,$closing_time;

    public $session_rooms_limitation_indoor,$session_rooms_limitation_outdoor;
    public function updated($fields){
        $this->validateOnly($fields,[
            'store_name' => 'required|string|max:255|unique:users',
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users',
            'phone' => 'required|numeric|unique:users',
            'whatsapp' => 'required|numeric|unique:users',
            'password' => 'required|alpha_num|min:8|max:255',
            'password_confirmation' => 'required|alpha_num|min:8|max:255|',
            'session_rooms_limitation_indoor' => 'required|numeric|gt:0',
            'opening_time' => 'required|date_format:H:i',
            'closing_time' => 'required|date_format:H:i|after:opening_time',

        ]);
    }

    public function validation(){
       $this->session_rooms_limitation_outdoor= $this->session_rooms_limitation_outdoor == '' ? 0 : $this->session_rooms_limitation_outdoor;
      return  $this->validate([
        'store_name' => 'required|string|max:255|unique:users',
        'name' => 'required|string|max:255',
        'location' => 'required|string|max:255',
        'email' => 'required|email|max:255|unique:users',
        'phone' => 'required|numeric|unique:users',
        'session_rooms_limitation_indoor' => 'required|numeric|gt:0',
        'session_rooms_limitation_outdoor' => 'required|numeric|gte:0',
        'whatsapp' => 'required|numeric|unique:users',
        'password' => 'required|alpha_num|min:8|max:255|confirmed',
        'password_confirmation' => 'required|string|max:255|',
        'geoLocation' => 'required|string',
        'opening_time' => 'required|date_format:H:i',
        'closing_time' => 'required|date_format:H:i|after:opening_time',
        ]);
    }
    public function store(){
        try {
            $this->rateLimit(3);
        } catch (TooManyRequestsException $exception) {
            $this->setErrorBag(["error"=> __('text.Slow down! Please wait another'). $exception->secondsUntilAvailable." ". __('text.seconds to send again.')]);
            $this->dispatchBrowserEvent('danger', __('text.Slow down! Please wait another'). $exception->secondsUntilAvailable." ". __('text.seconds to send again.'));
            return ;
        }
        $data=$this->validation();

        $data['code']=implode('',array_rand([0,1,2,3,4,5,6,7,8,9],6));
        $data['password']=bcrypt($data['password']);
        $user=User::create($data);
        Mail::to($user->email)->send(new SendCode($user->code,$user->name));
        session()->put('data',$user);
        session()->put('time',time());
        session()->put('activeCodeField','');
        $this->dispatchBrowserEvent('success',__('text.Message has been sent successfully'));


    }

    public function resend(){
        try {
            $this->rateLimit(3);
        } catch (TooManyRequestsException $exception) {
            $this->setErrorBag(["code"=> __('text.Slow down! Please wait another'). $exception->secondsUntilAvailable." ". __('text.seconds to send again.'),"phone"=> __('text.Slow down! Please wait another'). $exception->secondsUntilAvailable." ". __('text.seconds to send again.')]);
            $this->dispatchBrowserEvent('danger', __('text.Slow down! Please wait another'). $exception->secondsUntilAvailable." ". __('text.seconds to send again.'));
            return ;
        }
        if(session()->has('data')){
            $code=implode('',array_rand([0,1,2,3,4,5,6,7,8,9],6));
            $user=session()->get('data');
            $user->updated(['code'=>$code]);
            $user->save();
            Mail::to($user->email)->send(new SendCode($user->code,$user->name));
            session()->put('activeCodeField','');
            session()->forget('time');
            session()->put('time',time());
            $this->dispatchBrowserEvent('success',__('text.Message has been sent successfully'));
            $this->dispatchBrowserEvent('refreshCode',session()->get('time'));

        }else{
            $this->cancel();
        }
       }

    public function create(){
        try {
            $this->rateLimit(5);
        } catch (TooManyRequestsException $exception) {
            $this->setErrorBag(["code"=> __('text.Slow down! Please wait another'). $exception->secondsUntilAvailable." ". __('text.seconds to send again.'),"phone"=> __('text.Slow down! Please wait another'). $exception->secondsUntilAvailable." ". __('text.seconds to send again.')]);
            $this->dispatchBrowserEvent('danger', __('text.Slow down! Please wait another'). $exception->secondsUntilAvailable." ". __('text.seconds to send again.'));
            return ;
        }
        if (session()->has('data') && session()->has('data.code') && $this->code == session()->get('data.code')){
            if (session()->has('time') && time() < (session()->get('time')+(5*60)) ){
                $user=session()->pull('data');
                $this->cancel();
                $user->update(['activation' => 1,'code' => Null]);
                $user->save();

                Auth::guard('web')->login($user);
                $this->dispatchBrowserEvent('success',__('text.Your account activated successfully'));
                $this->redirect('/admin');

            }else{
                $this->dispatchBrowserEvent('danger',__('text.CODE EXPIRED,please resend the activation code or cancel the operation.'));
            }
        }else{
            $this->setErrorBag(["code"=> __('text.Invalid Code!')]);
            $this->dispatchBrowserEvent('danger',__('text.Invalid Code!'));
        }
    }

    public function cancel(){
        session()->forget('data');
        session()->forget('time');
        session()->forget('activeCodeField');
    }


    public function render()
    {

        return view('front.register')->extends('admin.layouts.app')->section('content');
    }
}
