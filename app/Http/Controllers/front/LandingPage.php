<?php

namespace App\Http\Controllers\front;

use App\Http\Controllers\Controller;
use App\Models\Setting;

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

}
