<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BannerCollection;
use App\Http\Resources\ProductCollection;
use App\Http\Resources\ProductResource;
use App\Http\Resources\VendorCollection;
use App\Models\Banner;
use App\Models\Product;
use App\Models\User;
use App\Traits\Responses;
use Illuminate\Http\Request;

class Vendors_ProductsController extends Controller
{

    use Responses;


    public function banners(Request $request){

        app()->setlocale($request->lang);
        $banners=Banner::whereDate('expire_at','>',now())->get();
        return $this->success(['banners' => collect(BannerCollection::collection($banners))->filter()]);
    }


    public function vendors_products(Request $request){

        app()->setlocale($request->lang);
        $vendors=User::where('activation',1)->where('add_product',1)->has('products')->get();
        $offers=Product::where('featured',1)->get();
        return $this->success(['vendors' => collect(VendorCollection::collection($vendors))->filter(),'banners' => collect($offers->pluck('banner'))->filter()]);
    }


    public function vendor_products(Request $request){ //vendor_id

        app()->setlocale($request->lang);
        $vendor=User::find($request->vendor_id);
        if($vendor){
            return $this->success(collect(ProductCollection::collection($vendor->products))->filter());

        }else{
            return $this->error("",404);
        }
    }

    public function product_details(Request $request){ //product_id

        app()->setlocale($request->lang);
        $product=Product::find($request->product_id);
        if($product){
            return $this->success(new ProductResource($product));

        }else{
            return $this->error("",404);
        }
    }


}
