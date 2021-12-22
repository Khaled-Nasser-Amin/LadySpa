<?php

// use App\Models\Category;
use Aloha\Twilio\Twilio;
use App\Models\Activity;
use App\Models\Refund;
use App\Models\Setting;
use Carbon\Carbon;

function send_sms($to,$message){
    $setting=Setting::find(1);
    if($setting->twillo_token && $setting->twillo_phone && $setting->twillo_sid){
        $sid=$setting->twillo_sid;
        $token=$setting->twillo_token;
        $from=$setting->twillo_phone;
        $twilio=new Twilio($sid,$token,$from);
        $twilio->message($to,$message);
    }else{
        return false;
    }

}

// function recursion($id ,$sub_mark='',$bg_color,$counter_color=0){
//     $array_bg_color=['bg-danger','bg-dark','bg-success','bg-warning','bg-info'];
//     $category=Category::withTrashed()->find($id);
//     if(!$category->trashed()){
//         $name=app()->getLocale() == 'ar' ? $category->name_ar : $category->name_en;
//         echo "<option class='text-white py-5 ".$bg_color."' value='".$category->id."'>".$sub_mark.$name ."</option>";
//         $bg_color=$array_bg_color[$counter_color];
//         $sub_mark='&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$sub_mark;
//     }

//     foreach($category->child_categories()->withTrashed()->get() as $cat){
//         if($counter_color == 4){
//             $counter_color=0;
//         }

//         if ($cat->child_categories->count() > 0) {
//             recursion($cat->id,$sub_mark,$bg_color,($counter_color+1));
//         }else{
//              $name=app()->getLocale() == 'ar' ? $cat->name_ar : $cat->name_en;
//             echo "<option class='text-white py-5 ".$bg_color."' value='".$cat->id."'>".$sub_mark.$name ."</option>";
//         }

//     }

// }


// function update_category_recursion($id ,$sub_mark='',$bg_color,$counter_color=0,$categories){
//     if(in_array($id,$categories)){
//         $array_bg_color=['bg-danger','bg-dark','bg-success','bg-warning','bg-info'];
//         $category=Category::withTrashed()->find($id);

//         if(!$category->trashed()){
//             $name=app()->getLocale() == 'ar' ? $category->name_ar : $category->name_en;
//             echo "<option class='text-white py-5 ".$bg_color."' value='".$category->id."'>".$sub_mark.$name ."</option>";
//             $bg_color=$array_bg_color[$counter_color];
//             $sub_mark='&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$sub_mark;
//         }

//         foreach($category->child_categories()->withTrashed()->get() as $cat){

//             if($counter_color == 4){
//                 $counter_color=0;
//             }
//             if(in_array($cat->id,$categories)){
//                 if ($cat->child_categories->count() > 0) {
//                     update_category_recursion($cat->id,$sub_mark,$bg_color,($counter_color+1),$categories);
//                 }else{
//                      $name=app()->getLocale() == 'ar' ? $cat->name_ar : $cat->name_en;
//                     echo "<option class='text-white py-5 ".$bg_color."' value='".$cat->id."'>".$sub_mark.$name ."</option>";
//                 }
//             }
//         }
//     }
// }


function create_activity($activity_action,$vendor_id,$belongs_to_id){
    Activity::create([
        'activity_action' => $activity_action,
        'ip_address' => request()->getClientIp(),
        'user_agent' => substr(request()->header('User-Agent'), 0, 500),
        'vendor_id' => $vendor_id,
        'belongs_to_id' => $belongs_to_id,
    ]);
}




//return  orders current year

function getOrdersCurrentMonth($q){
    return $q->order()->whereYear('created_at',session()->get('current_year')??null)->whereMonth('created_at',session()->get('current_month')??null);
}


//return sizes refund
function sizes_refund($order_id,$sizes){
    return Refund::where('order_id',$order_id)->whereIn('size_id',$sizes)->get();

}



//check for active collection or group of products
function checkCollectionActive($product){
    if($product->type == 'group'){
        $out_of_stock=0;
        foreach($product->child_products()->get() as $child){
            foreach($child->pivot->sizes()->get() as $row){
                if($row->pivot->quantity > $row->stock){
                    $out_of_stock++;
                }
            }
        }

        $deletedSizes=$product->child_products()->get()->map(function($value){
            return $value->pivot->sizes()->onlyTrashed()->get();
        });
        $InactiveProducts=$product->child_products()->where('isActive',0)->get()->count();
        $deletedProduct=$product->child_products()->onlyTrashed()->get()->count();
        $deletedSizes=$deletedSizes->collapse()->count();
        return $deletedProduct > 0 || $deletedSizes > 0  || $out_of_stock > 0 || $InactiveProducts > 0;
    }
}

function checkPromoCode($user,$special_code,$normal_code,$request_code,$for){
    if($special_code && !$user->used_promocodes()->find($special_code->id) && $special_code->code == $request_code
         && Carbon::now()->between($special_code->start_date, $special_code->end_date)
         && $special_code->limitation > $special_code->used_customers->count()
         && $special_code->type_of_code == 'special'
         && ($special_code->for == 'general' || $special_code->for == $for)
    ){
        return [
            'type' => $special_code->type_of_discount,
            'value' => $special_code->value,
            'constraint' => $special_code->condition,
        ];
    }elseif($normal_code && !$user->used_promocodes()->find($normal_code->id) && Carbon::now()->between($normal_code->start_date, $normal_code->end_date)
        && $normal_code->limitation > $normal_code->used_customers->count()
        && $normal_code->type_of_code == 'normal'
        && ($normal_code->for == 'general' || $normal_code->for == $for)){
        return [
            'type' => $normal_code->type_of_discount,
            'value' => $normal_code->value,
            'constraint' => $normal_code->condition,
        ];
    }else{
        return false;
    }
}
