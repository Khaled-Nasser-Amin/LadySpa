<?php

namespace App\Http\Controllers\admin\Profile;

use App\Http\Contracts\DeletesUsers;
use Illuminate\Support\Facades\DB;

class DeleteUser implements DeletesUsers
{
    /**
     * Delete the given user.
     *
     * @param  mixed  $user
     * @return void
     */
    public function delete($user)
    {
        // $user->getAttributes()['image'] ? unlink($user->image): null;
        // DB::table('sessions')->where('user_id',$user->id)->delete();
        // $user->delete();
    }
}
