<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Xsession;
use Illuminate\Auth\Access\HandlesAuthorization;

class XsessionPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function viewAny(User $user)
    {
        //
    }


    public function view(User $user, Xsession $xsession)
    {

        return $user->id == $xsession->user_id || $user->role == 'admin';

    }


    public function create(User $user)
    {
        return $user->add_session == 1;

    }


    public function update(User $user, XSession $xsession)
    {
        return $user->id == $xsession->user_id ;
    }


    public function delete(User $user, XSession $xsession)
    {
        return $user->id == $xsession->user_id || $user->role == 'admin';
    }


    public function UpdateSessionVendor(User $user, XSession $xsession)
    {
        return $user->id == $xsession->user_id;
    }



    public function forceDelete(User $user, XSession $xsession)
    {
        //
    }
}
