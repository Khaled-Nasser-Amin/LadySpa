<?php

namespace App\Http\Controllers\front;

use App\Http\Controllers\Controller;
use App\Mail\Support;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class LandingPage extends Controller
{
    public function index()
    {
        $setting=Setting::find(1);
        return view('front.welcome',compact('setting'));
    }
    public function terms()
    {
        return view('front.terms');
    }
    public function user_terms()
    {
        return view('front.user_terms');
    }
    public function support()
    {
        return view('front.support');
    }

    public function support_post(Request $request)
    {
        $to=env('MAIL_USERNAME');
        Mail::to($to)->send(new Support($request->email,$request->message));
        session()->flash('status',__('text.Message created successfully'));
        return view('front.support');

    }


}
