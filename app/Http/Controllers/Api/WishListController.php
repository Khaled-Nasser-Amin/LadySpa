<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Traits\Responses;
use Illuminate\Http\Request;

class WishListController extends Controller
{
    use Responses;
    public function updateWishListProduct(Request $request){
        app()->setlocale($request->lang);
        $product=Product::find($request->product_id);
        if(($product && $product->sizes()->find($request->size_id)) || ($product && $request->size_id == 0)){

            if($request->user()->wishList()->where('product_id',$request->product_id)->where('size_id',$request->size_id)->first()){
                $product->wishList()->wherePivot('size_id',$request->size_id)->detach($request->user()->id);
                return $this->success('',__('text.Removed successfully from your favorite list'),200);
            }else {
                if($request->size_id == 0){
                    $product->wishList()->attach([$request->user()->id => ['size_id' => null]]);

                }else{
                    $product->wishList()->attach([$request->user()->id => ['size_id' => $request->size_id]]);

                }
                return $this->success('',__('text.Added successfully to your favorite list'),200);


            }
        }else{
          return $this->error('',404);
        }

    }



}
