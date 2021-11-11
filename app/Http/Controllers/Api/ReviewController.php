<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Review;
use App\Traits\Responses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReviewController extends Controller
{

    use Responses;
     //review
     public function review(Request $request){
        $validator=Validator::make($request->all(),['review' => 'required|integer|between:1,5','comment' => 'nullable|string']);
        if ($validator->fails()){
            return response()->json($validator->errors(),404);
        }
        $product=Product::find($request->product_id);
        if(!$product){
            return $this->error('This product does not exist in our records',404);
        }

        $user=$request->user();
        if($user){

            if($user->reviews()->find($product->id)){
                $user->reviews()->updateExistingPivot($product->id ,['comment' => $request->comment,'review' => $request->review]);
                return $this->success('','Review updated successfully');
            }else {
                $user->reviews()->syncWithoutDetaching([$product->id =>['comment' => $request->comment,'review' => $request->review]]);
                return $this->success('','Review created successfully');
            }
        }else{
            return $this->error('This user does not exist in our records',404);
        }
    }

    public function return_reviews(Request $request){
        $reviews = Review::where('product_id',$request->product_id)->get();
        return $this->success($reviews);
    }
}
