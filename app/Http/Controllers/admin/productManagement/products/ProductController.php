<?php

namespace App\Http\Controllers\admin\productManagement\products;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use App\Traits\ImageTrait;
use App\Models\Product;
use Illuminate\Support\Facades\Request;

class ProductController extends Controller
{
    use ImageTrait;

    public function store($request)
    {
        $this->authorize('create',Product::class);
        $data=collect($request)->except(['image','sizes','productsIndex','taxes_selected','banner','groupImage'])->toArray();
        $data['image']=$this->add_single_image($request['image'],'products');
        $data['banner']=$this->add_single_image($request['banner'],'products');
        $product=Product::create($data);
        return $product;
    }



    public function update($request,$id)
    {
        $product=Product::findOrFail($id);
        $this->authorize('update',$product);

        $data=collect($request)->except(['image','productsIndex','taxes_selected','sizes','banner','groupImage'])->toArray();
        $this->updateImage($request,$product,$data);
        $product->update($data);
        $product->taxes()->detach();
        $product->taxes()->syncWithoutDetaching($request['taxes_selected']);
        $product->save();
        return $product;
    }

    protected function updateImage($request,$product,&$data){
        if ($request['image']){
            if(!$product->has('orders')){
                $this->delete_single_image($product,'image');
            }
            $data['image']=$this->add_single_image($request['image'],'products');
        }

        if ($request['banner']){
            if(!$product->has('orders')){
                $this->delete_single_image($product,'banner');
            }
            $data['banner']=$this->add_single_image($request['banner'],'products');
        }

    }
    public function destroy($product)
    {
        $this->authorize('delete',$product);

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
    protected function delete_single_image($product,$attr){
        if ($product->getAttributes()[$attr] && File::exists(storage_path('app/public/products/'.$product->getAttributes()[$attr]))){
            unlink(storage_path('app\public\products\\').$product->getAttributes()[$attr]);
        }
    }

    public function show(Request $request,Product $product,$slug){
        $this->authorize('view',$product);

        $images= array_merge([$product->image],$product->images->pluck('name')->toArray());
        return view('admin.productManagement.products.show',compact('product','images'));
    }
    public function addNewProduct(){
        $this->authorize('create',Product::class);
        return view('admin.productManagement.products.create');
    }
    public function updateProduct(Product $product){
        $this->authorize('update',$product);
        return view('admin.productManagement.products.edit',compact('product'));
    }









}
