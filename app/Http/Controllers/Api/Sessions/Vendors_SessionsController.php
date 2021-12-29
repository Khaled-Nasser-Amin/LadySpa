<?php

namespace App\Http\Controllers\Api\Sessions;

use App\Http\Controllers\Controller;
use App\Http\Resources\BannerCollection;
use App\Http\Resources\Sessions\SessionCollection;
use App\Http\Resources\Sessions\SessionResource;
use App\Http\Resources\Sessions\VendorCollection;
use App\Models\Banner;
use App\Models\Product;
use App\Models\User;
use App\Models\Xsession;
use App\Traits\Responses;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

class Vendors_SessionsController extends Controller
{

    use Responses;



    public function vendors_sessions(Request $request){

        app()->setlocale($request->lang);
        $vendors=User::where('activation',1)->whereHas('sessions', function (Builder $query) use($request){
            $query->when($request->type=='outdoor',function($q){
                $q->where('external_price','>',0);
            });
        })->get();
        $offers=Xsession::where('featured',1)->where('isActive',1)
        ->when($request->type=='outdoor',function($q){
            $q->where('external_price','>',0);
        })
        ->get();
        return $this->success(['vendors' => collect(VendorCollection::collection($vendors))->filter(),'banners' => collect(BannerCollection::collection($offers))->filter()]);
    }


    public function vendor_sessions(Request $request){ //vendor_id , type

        app()->setlocale($request->lang);
        $vendor=User::find($request->vendor_id);
        if($vendor){
            $sessions=$vendor->sessions()->when($request->type=='outdoor',function($q){
                $q->where('external_price','>',0);
            })->get();
            return $this->success(collect(SessionCollection::collection($sessions))->filter());

        }else{
            return $this->error("",404);
        }
    }

    public function session_details(Request $request){ //session_id ,type

        app()->setlocale($request->lang);
        $session=Xsession::find($request->session_id);
        if($session){
            if($request->type){
                $session->type=$request->type;
            }
            return $this->success(new SessionResource($session));
        }else{
            return $this->error("",404);
        }
    }


}
