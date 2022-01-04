<?php

namespace App\Http\Livewire\Admin\ProductsManagement\Refunds;

use App\Models\Refund;
use App\Models\RefundReservation;
use App\Traits\ImageTrait;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\WithPagination;

class RefundsReservations extends Component
{
    use WithPagination,ImageTrait;
    public $search,$status;

    protected $listeners=['delete'];
    public function confirmDelete($id,$type){
        $this->emit('confirmDelete', [$id,$type]);
    }
    public function delete($date){
        Gate::authorize('isAdmin');
        if($date[1] == 'reservation'){
            $refund=RefundReservation::findOrFail($date[0]);
            $refund->update(['refund_status' => 'money refunded']);
            $refund->save();
            session()->flash('success',__('text.Item Returned Successfully'));
            create_activity('Money Refunded',auth()->user()->id,auth()->user()->id);
        }
    }
    public function render()
    {
        $refunds= $this->search();
        return view('components.admin.refunds.reservations-refund',compact('refunds'));
    }

    public function search(){
        return RefundReservation::
        join('users','users.id','refund_reservations.vendor_id')
        ->join('reservations','reservations.id','refund_reservations.reservation_id')
        ->join('xsessions','xsessions.id','refund_reservations.session_id')->select('refund_reservations.*')
        ->when($this->status  == 2 || $this->status  == 1,function($q){
            $this->status  == 2 ? $q->where('refund_reservations.refund_status','not refunded yet'):$q->where('refund_status','money refunded');
        })
         ->where(function($q){
            return $q->when(auth()->user()->role != 'admin',function($q){
                return $q->where('refund_reservations.vendor_id',auth()->user()->id);
            });
        })
        ->where(function($q){
           $q->when($this->search,function ($q){
             $q->where('refund_reservations.reservation_id',$this->search)
                ->orWhere('refund_reservations.number_of_additions','like','%'.$this->search.'%')
                ->orWhere('refund_reservations.number_of_persons',$this->search)
                ->orWhere('refund_reservations.taxes',$this->search)
                ->orWhere('refund_reservations.total_refund_amount',$this->search)
                ->orWhere('refund_reservations.subtotal_refund_amount',$this->search)
                ->orWhere(function($q){
                    return $q->when(auth()->user()->role == 'admin',function($q){
                        return $q->where('users.store_name','like','%'.$this->search.'%');
                    });
                })
                ->orWhere(function($q){
                    return $q->when(auth()->user()->role != 'admin',function($q){
                        return $q->where('refund_reservations.vendor_id',auth()->user()->id);
                    })
                    ->where(function($q){
                       return $q->where('xsessions.name_ar','like','%'.$this->search.'%')
                        ->orWhere('xsessions.name_en','like','%'.$this->search.'%');
                    });
                });
            });
        })

        ->
        distinct('refund_reservations.id')->latest('refund_reservations.created_at')->
        paginate(10);
    }
}






