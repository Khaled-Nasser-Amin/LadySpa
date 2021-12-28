<?php

namespace App\Http\Livewire\Admin\ProductsManagement\RecycleBin;

use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;


class RecycleProducts extends Component
{
    use WithFileUploads,WithPagination,AuthorizesRequests;
    public $price,$date,$productName,$size,$product_type,$featured_non_featured,$store_name;


    protected $listeners=['restore'];


    public function confirmRestore($id){
        $this->emit('confirmRestore', $id);
    }

    public function restore($id){
        $product=Product::onlyTrashed()->findOrFail($id);
        $this->authorize('delete',$product);
        if($product->user){
            $product->restore();
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
        return Product::onlyTrashed()->join('users','users.id','=','products.user_id')->select('products.*')->where(function($q){

            return $q
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
             });
             })
             ->distinct('products.id')->latest('products.created_at')
             ->paginate(12);
    }


}





