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


}
