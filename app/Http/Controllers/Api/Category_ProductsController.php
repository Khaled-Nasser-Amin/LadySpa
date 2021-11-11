<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryCollection;
use App\Models\Category;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\ProductCollection;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Traits\Responses;
use Illuminate\Http\Request;

class Category_ProductsController extends Controller
{

    use Responses;

    public function parent_categories(Request $request){

        app()->setlocale($request->lang);
        $categories=Category::where('parent_id',0)->where('status',1)->get();

        return $this->success(['categories' => CategoryCollection::collection($categories),'favorites' =>ProductCollection::collection($request->user()->wishList)]);
    }


    public function category_products(Request $request){ //category_id

        app()->setlocale($request->lang);
        $category=Category::find($request->category_id);
        if($category){
            return $this->success(new CategoryResource($category));

        }else{
            return $this->error("",404);
        }
    }

    public function product(Request $request){ //product_id

        app()->setlocale($request->lang);
        $product=Product::find($request->product_id);
        if($product){
            return $this->success(new ProductResource($product));

        }else{
            return $this->error("",404);
        }
    }

    public function featured_slider_products(Request $request){

        app()->setlocale($request->lang);
        $products=Product::where('featured_slider',1)->get();
        return $this->success(ProductCollection::collection($products));

    }
}
