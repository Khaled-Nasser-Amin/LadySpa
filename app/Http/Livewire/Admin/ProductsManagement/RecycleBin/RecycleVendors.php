<?php

namespace App\Http\Livewire\Admin\ProductsManagement\RecycleBin;

use App\Http\Livewire\Admin\ProductsManagement\Products\Products;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Gate;

class RecycleVendors extends Component
{
    use WithPagination;
    public $name,$email,$location,$phone,$whatsapp,$store_name,$status,$search;


    protected $listeners=['restore'];


    public function confirmRestore($id){
        $this->emit('confirmRestore', $id);
    }

    public function restore($id){
        $vendor=User::onlyTrashed()->findOrFail($id);
        Gate::authorize('isAdmin');
        $vendor_deleted_at=$vendor->deleted_at->toDateTimeString();
        $vendor->restore();
        $vendor->products()->onlyTrashed()->where('deleted_at','>=',$vendor_deleted_at)->restore();
        session()->flash('success',__('text.Restored Successfully'));
        create_activity('Vendor Restored',auth()->user()->id,$vendor->id);
    }


    public function render()
    {
        $vendors=$this->search();
        return view('admin.productManagement.recycle_bin.recycle_vendors',compact('vendors'))->extends('admin.layouts.appLogged')->section('content');
    }

    protected function search(){
        return User::with('orders')->where('role','!=','admin')->onlyTrashed()
        ->where(function($q){
            return $q->when($this->status  == 2 || $this->status  == 1,function($q){
                $this->status  == 2 ? $q->where('activation',0):$q->where('activation',1);
            })->where(function($q){
                return $q->when($this->search,function ($q){
                        return $q->where('name','like','%'.$this->search.'%')
                            ->orWhere('email','like','%'.$this->search.'%')
                            ->orWhere('phone','like','%'.$this->search.'%')
                            ->orWhere('whatsapp','like','%'.$this->search.'%')
                            ->orWhere('store_name','like','%'.$this->search.'%')
                            ->orWhere('location','like','%'.$this->search.'%');
                        });
            });
        })->latest()->paginate(10);
    }

    protected function restoreHisProducts($products,$vendor_deleted_at){
        // $r=Product::withTrashed()->find(46);
        // dd($products,$r);
        foreach($products as $product){
            if($product->deleted_at->toDateTimeString() < $vendor_deleted_at){
                dd($product->deleted_at->toDateTimeString() <= $vendor_deleted_at,$product->deleted_at->toDateTimeString() , $vendor_deleted_at,$product->id);
                $product->restore();
            }
        }
    }
}


