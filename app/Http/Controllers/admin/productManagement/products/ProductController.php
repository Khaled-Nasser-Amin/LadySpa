<?php

namespace App\Http\Controllers\admin\productManagement\products;
use App\Http\Controllers\Controller;

use App\Traits\ImageTrait;
use App\Models\Product;
use Illuminate\Support\Facades\Request;

class ProductController extends Controller
{
    use ImageTrait;

    public function store($request)
    {
        $data=collect($request)->except(['image','sizes','productsIndex'])->toArray();
        $data['image']=$this->add_single_image($request['image'],'products');
        $data['banner']=$this->add_single_image($request['banner'],'products');
        $product=Product::create($data);
        return $product;
    }



    public function update($request,$id)
    {
        $product=Product::findOrFail($id);
        $data=collect($request)->except(['image','colorsIndex','taxes_selected'])->toArray();
        if ($request['image']){
            if(!$product->has('orders')){
                $this->livewireDeleteSingleImage($product,'products');
            }
            $data=$this->livewireAddSingleImage($request,$data,'products');
        }

        $product->update($data);
        $product->taxes()->detach();
        $product->taxes()->syncWithoutDetaching($request['taxes_selected']);
        $product->save();
        return $product;
    }
    public function destroy($product)
    {
        $vendor_id=$product->user_id;
        $product->delete();
        return $vendor_id;
    }

    protected function add_single_image($image,$folder){
        $path=$image->store('public/'.$folder);
        $arr=explode('/',$path);
        $imageName=end($arr);
        return $imageName;
    }

    public function show(Request $request,Product $product,$slug){
        $images= array_merge([$product->image],$product->images->pluck('name')->toArray());
        return view('admin.productManagement.products.show',compact('product','images'));
    }
    public function addNewProduct(){
        return view('admin.productManagement.products.create');
    }
    public function updateProduct(Product $product){
        return view('admin.productManagement.products.edit',compact('product'));
    }









}
