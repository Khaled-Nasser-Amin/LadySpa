<?php

namespace App\Http\Controllers\Api\Products;

use App\Http\Controllers\Api\ShippingController;
use App\Http\Controllers\Controller;
use App\Http\Resources\FavoriteCollection;
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
        return $this->success(['banners' => collect($banners->pluck('image'))->filter(),'favorites' =>collect(FavoriteCollection::collection($request->user()->wishList))->filter()]);
    }


    public function vendors_products(Request $request){

        app()->setlocale($request->lang);
        $vendors=User::where('activation',1)->has('products')->get();
        $offers=Product::where('featured',1)->where('isActive',1)->get();
        $featured=[];
        $this->featuredBanner($featured,$offers);
        if($request->user_geo_location){
            $this->sortByDistance($vendors,$request->user_geo_location);
            $vendors=$vendors->sortBy('distance');
        }
        return $this->success(['vendors' => collect(VendorCollection::collection($vendors))->filter(),'banners' => collect($featured)->filter()]);
    }


    public function vendor_products(Request $request){ //vendor_id

        app()->setlocale($request->lang);
        $vendor=User::find($request->vendor_id);
        if($vendor){
            return $this->success(collect(ProductCollection::collection($vendor->products))->collapse()->filter());

        }else{
            return $this->error("",404);
        }
    }

    public function product_details(Request $request){ //product_id

        app()->setlocale($request->lang);
        $product=Product::find($request->product_id);
        if($product){
            if($request->size_id && $request->size_id != 0){
                $product->size_id=$request->size_id;
            }
            return $this->success(new ProductResource($product));

        }else{
            return $this->error("",404);
        }
    }


    protected function featuredBanner(&$featured,$offers)
    {
        foreach($offers as $offer){
            if($offer->sizes && $offer->type == 'single'){
                foreach($offer->sizes as $size){
                    $featured[]=['image' => $offer->banner,'id' => $offer->id,'size_id' =>(int) $size->id,'type' => $offer->type];

                }
            }elseif($offer->type == 'group'){

                $featured[]=['image' => $offer->banner,'id' =>(int) $offer->id,'size_id' => 0,'type' => $offer->type];

            }

        }
    }

    protected function sortByDistance(&$vendors,$user_location)
    {
        $user_location=explode(',',$user_location);
        foreach($vendors as $vendor){
            $vendor_location=explode(',',$vendor->geoLocation);
            $vendor->distance=$this->calcDistance($user_location[0],$vendor_location[0],$user_location[1],$vendor_location[1]);
        }
    }

    protected function calcDistance($lat1, $lat2, $lon1, $lon2)
    {
        if (($lat1 == $lat2) && ($lon1 == $lon2)) {
            return 0;
        }
        else {
            $theta = $lon1 - $lon2;
            $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
            $dist = acos($dist);
            $dist = rad2deg($dist);
            $kilometers = round(($dist * 60 * 1.1515)* 1.609344);

            return $kilometers;

        }

    }



}
