<?php

namespace App\Http\Livewire\Admin\ProductsManagement\Refunds;

use App\Models\Refund;
use App\Traits\ImageTrait;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\WithPagination;

class Refunds extends Component
{
    use WithPagination,ImageTrait;
    public $search,$status;

    protected $listeners=['delete'];
    public function confirmDelete($id,$type){
        $this->emit('confirmDelete', [$id,$type]);
    }
    public function delete($data){
        Gate::authorize('isAdmin');
        if($data[1] == 'single'){
            $refund=Refund::findOrFail($data[0]);
            $refund->update(['refund_status' => 'money refunded']);
            $refund->save();
            session()->flash('success',__('text.Item Returned Successfully'));
            create_activity('Money Refunded',auth()->user()->id,auth()->user()->id);
        }


    }
    public function render()
    {
        $refunds= $this->search();
        return view('components.admin.refunds.singel-products',compact('refunds'));
    }

    public function search(){
        return Refund::
        join('users','users.id','refunds.vendor_id')
        ->join('sizes','sizes.id','refunds.size_id')
        ->join('products','products.id','sizes.product_id')->select('refunds.*')
        ->when($this->status  == 2 || $this->status  == 1,function($q){
            $this->status  == 2 ? $q->where('refunds.refund_status','not refunded yet'):$q->where('refund_status','money refunded');
        })
         ->where(function($q){
            return $q->when(auth()->user()->role != 'admin',function($q){
                return $q->where('refunds.vendor_id',auth()->user()->id);
            });
        })
        ->where(function($q){
           $q->when($this->search,function ($q){
             $q->where('refunds.order_id',$this->search)
                ->orWhere('refunds.size','like','%'.$this->search.'%')
                ->orWhere('refunds.quantity',$this->search)
                ->orWhere('refunds.taxes',$this->search)
                ->orWhere('refunds.total_refund_amount',$this->search)
                ->orWhere('refunds.subtotal_refund_amount',$this->search)
                ->orWhere(function($q){
                    return $q->when(auth()->user()->role == 'admin',function($q){
                        return $q->where('users.store_name','like','%'.$this->search.'%');
                    });
                })
                ->orWhere(function($q){
                    return $q->when(auth()->user()->role != 'admin',function($q){
                        return $q->where('products.user_id',auth()->user()->id);
                    })
                    ->where(function($q){
                       return $q->where('products.name_ar','like','%'.$this->search.'%')
                        ->orWhere('products.name_en','like','%'.$this->search.'%');
                    });
                });
            });
        })

        ->
        distinct('refunds.id')->latest('refunds.created_at')->paginate(10);
    }
}






