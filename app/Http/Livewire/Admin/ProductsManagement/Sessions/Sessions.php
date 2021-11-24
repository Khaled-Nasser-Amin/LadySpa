<?php

namespace App\Http\Livewire\Admin\ProductsManagement\Sessions;

use App\Http\Controllers\admin\productManagement\products\ProductController;
use App\Models\Setting;
use App\Models\Xsession;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Sessions extends Component
{
    use WithPagination,AuthorizesRequests;
    public $price,$date,$productName,$size,$product_type,$featured_non_featured;

    // public $price=150,$date,$productName='product1',$size='xl',$product_type='single',$featured_non_featured='Featured';




    protected $listeners=['delete'];

    public function mount(){
        $product="";
        if(session()->has('product_id'))
            $product=Xsession::find(session()->get('product_id'));
        if($product){
            $this->size=$product->size;
            $this->date=$product->created_at;
            $this->productName=app()->getLocale() == 'ar' ? $product->name_ar:$product->name_en;
        }

        session()->forget(['product_id']);
    }

    public function render()
    {
        $sessions=$this->search();
        return view('admin.productManagement.sessions.index',compact('sessions'))->extends('admin.layouts.appLogged')->section('content');
    }



    public function confirmDelete($id){

        $this->emit('confirmDelete',$id);
    }

    //delete product
    public function delete(Xsession $session){
        $instance=new Xsession();
        $vendor_id=$instance->destroy($product);
        session()->flash('success',__('text.Product Deleted Successfully') );
        create_activity('Product Deleted',auth()->user()->id,$vendor_id);
    }


    //update product's featured
    public function updateFeatured(Xsession $session){
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
    public function updateStatus(Xsession $session){
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
       return Xsession::distinct('xsessions.id')->latest('xsessions.created_at')
        ->paginate(12);

    }



}

