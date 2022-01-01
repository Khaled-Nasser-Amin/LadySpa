<?php

namespace App\Http\Livewire\Admin\ProductsManagement\Reservations;


use App\Models\Reservation as ModelsReservation;

use App\Traits\ImageTrait;
use Livewire\Component;
use Livewire\WithPagination;

class Reservation extends Component
{
    use WithPagination,ImageTrait;
    public $search,$payment_status,$reservation_status,$payment_way;



    public function render()
    {
        $reservations=$this->search();
        return view('admin.productManagement.reservations.index',compact('reservations'))->extends('admin.layouts.appLogged')->section('content');
    }


    //search and reservation pagination
    protected function search(){
        return  ModelsReservation::join('users','users.id','=','reservations.vendor_id')->select('reservations.*')
        ->where(function($q){
            $q->when(auth()->user()->role != 'admin',function($q){
                return $q->where('vendor_id',auth()->user()->id);
            })
            ->when(auth()->user()->role == 'admin',function($q){
                return $q->where('reservations.payment_way','online payment')
                ->orWhere('vendor_id',auth()->user()->id);
            });
        })
        ->where(function($q){
           return $q
           ->when($this->payment_status,function($q){
            return $q->where('reservations.payment_status',$this->payment_status);
          })
          ->when($this->payment_way,function($q){
            return $q->where('reservations.payment_way',$this->payment_way);
          })
          ->when($this->reservation_status,function($q){
            return $q->where('reservations.reservation_status',$this->reservation_status);
          })
          ->where(function($q){

            $q->when(is_numeric($this->search),function ($q){
                return $q->where('reservations.id',$this->search)
                    ->orWhere('reservations.taxes','like','%'.$this->search.'%')
                    ->orWhere('reservations.taxes','like','%'.$this->search.'%')
                    ->orWhere('reservations.shipping','like','%'.$this->search.'%')
                    ->orWhere('reservations.subtotal','like','%'.$this->search.'%');
            })
            ->when(!is_numeric($this->search),function ($q){
                return $q->where('reservations.payment_way','like','%'.$this->search.'%')
                    ->orWhere('reservations.address','like','%'.$this->search.'%')
                    ->orWhere('reservations.receiver_name','like','%'.$this->search.'%')
                    ->orWhereIn('reservations.receiver_name',explode(" ",$this->search))
                    ->orWhere('users.store_name','like','%'.$this->search.'%');
            });
         });

        })->
        orderByDesc('reservations.id')->latest()->paginate(10);
    }
}
