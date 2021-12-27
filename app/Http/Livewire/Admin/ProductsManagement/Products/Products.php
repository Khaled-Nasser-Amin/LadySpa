<?php

namespace App\Http\Livewire\Admin\ProductsManagement\Products;

use App\Http\Controllers\admin\productManagement\products\ProductController;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Gate;
class Products extends Component
{
    use WithPagination,AuthorizesRequests;
    public $price,$date,$productName,$size,$product_type,$featured_non_featured,$store_name;




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
        $products=$this->search();
        return view('admin.productManagement.products.index',compact('products'))->extends('admin.layouts.appLogged')->section('content');
    }



    public function confirmDelete($id){

        $this->emit('confirmDelete',$id);
    }

    //delete product
    public function delete(Product $product){
        $this->authorize('delete',$product);
        $instance=new ProductController();
        $vendor_id=$instance->destroy($product);
        session()->flash('success',__('text.Product Deleted Successfully') );
        create_activity('Product Deleted',auth()->user()->id,$vendor_id);
    }


    //update product's featured
    public function updateFeatured(Product $product){
        Gate::authorize('isAdmin');
        if(checkCollectionActive($product)){
            return ;
        }
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
            $this->dispatchBrowserEvent('danger',__('text.You have only'). ' 6 ' . __('text.special products'));
        }

    }


    //update product's featured by admin  for slider
        // public function updateAdminFeatured(Product $product){
        //     Gate::authorize('isAdmin');
        //     $numberOfProducts=Product::where('featured_slider',1)->count();
        //     if ($numberOfProducts < 10 || $product->featured_slider == 1){
        //         if($product->featured_slider == 0 ){
        //             $featured= 1;
        //             create_activity('Added a product as a feature',auth()->user()->id,$product->user_id);

        //         }else{
        //             $featured= 0;
        //             create_activity('Removed a product as a feature',auth()->user()->id,$product->user_id);
        //         }

        //         $product->update([
        //             'featured_slider'=>$featured
        //         ]);
        //     }else{
        //         $this->dispatchBrowserEvent('danger',__('text.You have only 10 special products'));
        //     }

    // }

    //change product status
    public function updateStatus(Product $product){
        $this->authorize('update',$product);

        if($product->isActive == 0 ){
            $status= 1;
            $product->update([
                'isActive'=>$status
            ]);
            create_activity('Active a product',auth()->user()->id,$product->user_id);

        }else{
            $status= 0;
            $product->update([
                'isActive'=>$status
            ]);
            create_activity('Unactive a product',auth()->user()->id,$product->user_id);
        }


    }


    //search and return products paginated
    protected function search(){
       return Product::join('users','users.id','=','products.user_id')->select('products.*')
       ->where(function ($q) {
            $q->when(auth()->user()->role != 'admin' ,function ($q) {
            return $q->where('user_id',auth()->user()->id);
            });
        })
        ->where(function ($q) {
            $q->when($this->store_name,function ($q) {
                return $q
                ->where('users.store_name','like','%'.$this->store_name.'%')->select('products.*');
            });
        })
        ->
        when($this->size,function ($q) {
            $q->where(function($q){
                return $q->whereHas('sizes', function (Builder $query){
                    return $query->where('size','like','%'.$this->size.'%');


                })->orWhere(function($q){
                        return $q->whereHas('child_products', function (Builder $query) {
                            $query ->withTrashed()->whereHas('sizes', function (Builder $query) {
                                $query->withTrashed()->where('size','like','%'.$this->size.'%');
                            });
                        });
                    });
            });
        })
        ->where(function ($q) {

            $q->when($this->productName,function ($q){
                $q->where(function($q){
                    return $q->where(function($q){
                        return $q->where('products.name_ar','like','%'.$this->productName.'%')
                        ->orWhere('products.name_en','like','%'.$this->productName.'%');
                    })
                    ->orWhere(function($q){
                        return $q->whereHas('child_products', function (Builder $query) {
                        $query ->withTrashed()->where('name_ar','like','%'.$this->productName.'%')
                        ->orWhere('name_en','like','%'.$this->productName.'%');
                        });
                    });
                });
            })



            ->when($this->price,function ($q) {
                $q->where(function($q){
                    return $q->whereHas('sizes', function (Builder $query){
                        $query->where(function($q){
                            $q->where('sizes.price','=',$this->price)
                            ->orWhere('sizes.sale','=',$this->price);
                        });

                    })->orWhere(function($q){
                        return $q->where('products.group_price','=',$this->price)
                        ->orWhere('products.group_sale','=',$this->price);
                    });
                });

            })


            ->when($this->date,function ($q)  {
                return $q->whereDate('products.created_at',$this->date);
            })->when($this->product_type,function ($q)  {
                return $q->where('products.type',$this->product_type);
            })->when($this->featured_non_featured,function ($q)  {
                $featured=$this->featured_non_featured == 'Featured' ? 1 : 0;
                return $q->where('products.featured',$featured);
            });
        })
        ->distinct('products.id')->latest('products.created_at')
        ->paginate(12);

    }


}

