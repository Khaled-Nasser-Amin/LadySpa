<?php

namespace App\Http\Livewire\Admin\ProductsManagement\Products;

use App\Http\Controllers\admin\productManagement\products\ProductController;
use App\Models\Category;
use App\Models\Product;
use App\Models\Setting;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Gate;

class Products extends Component
{
    use WithPagination,AuthorizesRequests;
    public $price;
    public $productName;
    public $size;
    public $date;

    protected $listeners=['delete'];

    public function mount(){
        $product="";
        if(session()->has('product_id'))
            $product=Product::find(session()->get('product_id'));
        if($product){
            $this->size=$product->size;
            $this->date=$product->created_at;
            $this->productName=app()->getLocale() == 'ar' ? $product->name_ar:$product->name_en;
        }

        session()->forget(['product_id']);
    }

    public function render()
    {
        $setting=Setting::find(1);
        $products=$this->search();
        return view('admin.productManagement.products.index',compact('products','setting'))->extends('admin.layouts.appLogged')->section('content');
    }



    public function confirmDelete($id){

        $this->emit('confirmDelete',$id);
    }

    //delete product
    public function delete(Product $product){
        $this->authorize('delete',$product);
        $cat=$product->category;
        $instance=new ProductController();
        $vendor_id=$instance->destroy($product);
        if($cat && $cat->products->count() == 0){
            $this->deleteCategoryStatus($cat);
        }
        session()->flash('success',__('text.Product Deleted Successfully') );
        create_activity('Product Deleted',auth()->user()->id,$vendor_id);


    }


    //update product's featured
    public function updateFeatured(Product $product){
        $this->authorize('update',$product);
        $numberOfProducts=auth()->user()->products->where('featured',1)->count();
        if ($numberOfProducts < 6 || $product->featured == 1){
            if($product->featured == 0 ){
                $featured= 1;
                create_activity('Added a product as a feature',auth()->user()->id,$product->user_id);

            }else{
                $featured= 0;
                create_activity('Removed a product as a feature',auth()->user()->id,$product->user_id);
            }

            $product->update([
                'featured'=>$featured
            ]);
        }else{
            $this->dispatchBrowserEvent('danger',__('text.You have only 6 special products'));
        }

    }


    //update product's featured by admin  for slider
    public function updateAdminFeatured(Product $product){
        Gate::authorize('isAdmin');
        $numberOfProducts=Product::where('featured_slider',1)->count();
        if ($numberOfProducts < 10 || $product->featured_slider == 1){
            if($product->featured_slider == 0 ){
                $featured= 1;
                create_activity('Added a product as a feature',auth()->user()->id,$product->user_id);

            }else{
                $featured= 0;
                create_activity('Removed a product as a feature',auth()->user()->id,$product->user_id);
            }

            $product->update([
                'featured_slider'=>$featured
            ]);
        }else{
            $this->dispatchBrowserEvent('danger',__('text.You have only 10 special products'));
        }

    }

    //change product status
    public function updateStatus(Product $product){
        $this->authorize('update',$product);
        if($product->isActive == 0 ){
            $status= 1;
            $product->update([
                'isActive'=>$status
            ]);
            $this->updateCategoryStatus($product->category);
            create_activity('Active a product',auth()->user()->id,$product->user_id);

        }else{
            $status= 0;
            $product->update([
                'isActive'=>$status
            ]);
            $this->deleteCategoryStatus($product->category);
            create_activity('Unactive a product',auth()->user()->id,$product->user_id);
        }


    }


    //search and return products paginated
    protected function search(){
       return Product::join('sizes','sizes.product_id','products.id')->select('products.*')
        ->when($this->price,function ($q) {
                return $q->where('colors.price','=',$this->price)->select('products.*');
        })

        ->when($this->size,function ($q) {
            return $q->where('sizes.size','like','%'.$this->size.'%')->select('products.*');
        })
        ->where(function($q){
            return  $q->when($this->productName,function ($q){
                    return $q->where('products.name_ar','like','%'.$this->productName.'%')
                    ->orWhere('products.name_en','like','%'.$this->productName.'%');
                })->when($this->date,function ($q)  {
                    return $q->whereDate('products.created_at',$this->date);
                });
            })
            ->distinct('products.id')->latest('products.created_at')->paginate(12);
    }


    //inactive or active category
    protected function deleteCategoryStatus($cat){
        if($cat->products->where('isActive',1)->count() != 0 || $cat->child_categories->where('status',1)->count() > 0){
            return ;
        }else{
            $cat->update(['status' => 0]);
            $cat->save();
            if( $cat->parent_id == 0)
                return;
            $this->deleteCategoryStatus($cat->parent_category);

        }
    }

    protected function updateCategoryStatus($cat){
        if($cat->status == 1)
            return ;
        $cat->update(['status' => 1]);
        $cat->save();
        if($cat->parent_id == 0)
            return;
        $this->updateCategoryStatus($cat->parent_category);


    }
}
