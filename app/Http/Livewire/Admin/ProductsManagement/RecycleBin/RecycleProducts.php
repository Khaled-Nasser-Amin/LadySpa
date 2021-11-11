<?php

namespace App\Http\Livewire\Admin\ProductsManagement\RecycleBin;

use App\Models\Product;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;


class RecycleProducts extends Component
{
    use WithFileUploads,WithPagination,AuthorizesRequests;
    public $search;
    public $price;
    public $category;
    public $productName;
    public $size;
    public $store_name;
    public $date;
    public $typeOfFabric;
    public $typeOfSleeve;
    public $filterProducts;

    protected $listeners=['restore'];


    public function confirmRestore($id){
        $this->emit('confirmRestore', $id);
    }

    public function restore($id){
        $product=Product::onlyTrashed()->findOrFail($id);
        $this->authorize('delete',$product);
        if($product->user){
            $product->restore();
            $this->updateCategoryStatus($product->category);
            create_activity('Product Restored',auth()->user()->id,$product->user_id);
            session()->flash('success',__('text.Restored Successfully'));

        }else{
            session()->flash('danger',__('text.Please restore vendor first'));

        }


    }

    public function render()
    {
        $products=$this->search();
        return view('admin.productManagement.recycle_bin.recycle_products',compact('products'))->extends('admin.layouts.appLogged')->section('content');
    }

    //search and return products paginated
    protected function search(){
        return Product::onlyTrashed()->where(function($q){

            return $q->join('colors','colors.product_id','=','products.id')->select('products.*')->join('sizes','sizes.color_id','=','colors.id')->select('products.*')
            ->when(auth()->user()->role != 'admin' || $this->filterProducts == 'My Products',function ($q) {
                return $q->where('user_id',auth()->user()->id);
             })
             ->when($this->price,function ($q) {
                    return $q->where('colors.price','=',$this->price)->select('products.*');
            })

            ->when($this->size,function ($q) {
                return $q->where('sizes.size','like','%'.$this->size.'%')->select('products.*');
            })
            ->when($this->store_name,function ($q) {
                return $q->join('users','users.id','=','products.user_id')
                ->where('users.store_name','like','%'.$this->store_name.'%')->select('products.*');
            })
            ->where(function($q){
                return  $q->when($this->productName,function ($q){
                        return $q->where('products.name_ar','like','%'.$this->productName.'%')
                        ->orWhere('products.name_en','like','%'.$this->productName.'%');
                    })
                    ->when($this->category,function ($q){
                            return $q->where('products.category_id',$this->category);
                    })
                        ->when($this->date,function ($q)  {
                        return $q->whereDate('products.created_at',$this->date);
                    })
                    ->when($this->typeOfFabric,function ($q)  {
                        return $q->where('products.typeOfFabric','like','%'.$this->typeOfFabric.'%');
                    })
                    ->when($this->typeOfSleeve,function ($q)  {
                        return $q->where('products.typeOfSleeve','like','%'.$this->typeOfSleeve.'%');
                    });
                });
        })->distinct('products.id')->latest('products.created_at')->paginate(12);
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



