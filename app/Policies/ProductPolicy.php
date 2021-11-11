<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProductPolicy
{
    use HandlesAuthorization;


    public function viewAny(User $user)
    {
        //
    }


    public function view(User $user, Product $product)
    {

        return $user->id == $product->user_id || $user->role == 'admin';

    }


    public function create(User $user)
    {
        return $user->add_product == 1;

    }


    public function update(User $user, Product $product)
    {
        return $user->id == $product->user_id ;
    }


    public function delete(User $user, Product $product)
    {
        return $user->id == $product->user_id || $user->role == 'admin';
    }


    public function UpdateProductVendor(User $user, Product $product)
    {
        return $user->id == $product->user_id;
    }



    public function forceDelete(User $user, Product $product)
    {
        //
    }
}
