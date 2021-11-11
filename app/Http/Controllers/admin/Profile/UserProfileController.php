<?php

namespace App\Http\Controllers\admin\Profile;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class UserProfileController extends Controller
{

    public function show(Request $request)
    {
        return view('admin.Profile.show', [
            'request' => $request,
            'user' => $request->user(),
        ]);
    }
}
