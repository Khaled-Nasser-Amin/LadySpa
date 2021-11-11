<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductCollection;
use App\Http\Resources\VendorCollection;
use App\Models\User;
use App\Traits\Responses;
use Illuminate\Http\Request;


class VendorController extends Controller
{
    use Responses;


    public function all_vendors()
    {
        $vendors=User::has('products')->get();
        return $this->success(collect(VendorCollection::collection($vendors))->filter());
    }


    public function vendor_products(Request $request)
    {
        app()->setlocale($request->lang);
        $vendor=User::find($request->vendor_id);

        if($vendor && $vendor->products->count() > 0){
            $products=$vendor->products;
            $categories=$products->pluck('category')->unique();
            foreach($categories as $category){
                $cate=$this->check_parent_cat($category);
                $data[]=
                [
                    'category_name' => app()->getLocale() == 'ar' ? $cate->name_ar:$cate->name_en,
                    'category_id' => $cate->id,
                    'products' => collect(ProductCollection::collection($products->where('category_id',$category->id)))->filter(),
                ];
            }

             $root_categories=collect($data)->unique('category_id');
            foreach($root_categories as $root){
                $lists_of_array=collect($data)->where('category_id',$root['category_id'])->pluck('products');
                foreach($lists_of_array as $list){
                    foreach($list as $sub_list){
                        $array[]=$sub_list;
                    }
                }
                $final_data[]=
                [
                    'category_name' => $root['category_name'],
                    'category_id' => $root['category_id'],
                    'products' => $array,
                ];
                $array=[];

            }

            return $this->success($final_data);


        }else{
            return $this->error(__('text.Not Found'),404);
        }
    }


    protected function check_parent_cat($category){
        if($category->parent_id == 0){
            return $category;
        }else{
            return $this->check_parent_cat($category->parent_category);
        }

    }





}
