<?php

namespace App\Http\Livewire\Admin\ProductsManagement\Refunds;

use App\Models\RefundGroup;
use App\Traits\ImageTrait;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\WithPagination;

class RefundGroups extends Component
{
    use WithPagination,ImageTrait;
    public $search,$status;

    protected $listeners=['delete'];
    public function confirmDelete($id,$type){
        $this->emit('confirmDelete', [$id,$type]);
    }
    public function delete($data){
        Gate::authorize('isAdmin');
        if($data[1] == 'group'){
            $refund=RefundGroup::findOrFail($data[0]);
            $refund->update(['refund_status' => 'money refunded']);
            $refund->save();
            session()->flash('success',__('text.Item Returned Successfully'));
            create_activity('Money Refunded',auth()->user()->id,auth()->user()->id);
        }


    }
    public function render()
    {
        $refunds= $this->search();
        return view('components.admin.refunds.group-products',compact('refunds'));
    }

    public function search(){
        return RefundGroup::
        join('users','users.id','refund_groups.vendor_id')
        ->join('products','products.id','refund_groups.product_id')->select('refund_groups.*')
        ->when($this->status  == 2 || $this->status  == 1,function($q){
            $this->status  == 2 ? $q->where('refund_groups.refund_status','not refunded yet'):$q->where('refund_status','money refunded');
        })
         ->where(function($q){
            return $q->when(auth()->user()->role != 'admin',function($q){
                return $q->where('refund_groups.vendor_id',auth()->user()->id);
            });
        })
        ->where(function($q){
           $q->when($this->search,function ($q){
             $q->where('refund_groups.order_id',$this->search)
                ->orWhere('refund_groups.quantity',$this->search)
                ->orWhere('refund_groups.taxes',$this->search)
                ->orWhere('refund_groups.total_refund_amount',$this->search)
                ->orWhere('refund_groups.subtotal_refund_amount',$this->search)
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
        distinct('refund_groups.id')->latest('refund_groups.created_at')->paginate(10);
    }
}






