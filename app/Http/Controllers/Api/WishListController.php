<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Traits\Responses;
use Illuminate\Http\Request;

class WishListController extends Controller
{
    use Responses;
    public function updateWishList(Request $request){
        app()->setlocale($request->lang);
        $product=Product::find($request->product_id);
        if($product){
            if($request->user()->wishList()->find($request->product_id)){
                $product->wishList()->detach($request->user());
                return $this->success('',__('text.Removed successfully from your favorite list'),200);
            }else {
                $product->wishList()->syncWithoutDetaching($request->user());
                return $this->success('',__('text.Added successfully to your favorite list'),200);

            }
        }else{
          return $this->error('',404);
        }

    }


}
