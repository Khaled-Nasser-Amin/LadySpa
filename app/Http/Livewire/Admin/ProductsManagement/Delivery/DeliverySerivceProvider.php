<?php

namespace App\Http\Livewire\Admin\ProductsManagement\Delivery;

use App\Models\DeliveryServiceProvider;
use App\Traits\ImageTrait;
use Livewire\Component;
use Livewire\WithPagination;

class DeliverySerivceProvider extends Component
{
    use WithPagination,ImageTrait;
    public $search,$status;

    protected $listeners=['delete'];
    public function confirmDelete($id){
        $this->emit('confirmDelete', $id);
    }
    public function delete(DeliveryServiceProvider $service_provider){
        // $this->livewireDeleteSingleImage($service_provider,'users');
        $service_provider->delete();
        session()->flash('success',__('text.User Deleted Successfully'));
        create_activity('Delivery Service Provider Deleted',auth()->user()->id,auth()->user()->id);

    }
    public function render()
    {
        $users=DeliveryServiceProvider::when($this->status  == 2 || $this->status  == 1,function($q){
            $this->status  == 2 ? $q->where('activation',0):$q->where('activation',1);
        })
        ->where(function($q){
           $q->when($this->search,function ($q){
             $q->where('name','like','%'.$this->search.'%')
                ->orWhere('email','like','%'.$this->search.'%')
                ->orWhere('phone','like','%'.$this->search.'%');
            });
        })

        ->latest()->paginate(10);
        return view('admin.productManagement.delivery_service_provider.index',compact('users'))->extends('admin.layouts.appLogged')->section('content');
    }
}
