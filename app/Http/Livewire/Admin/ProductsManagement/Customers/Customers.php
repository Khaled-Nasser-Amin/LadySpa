<?php

namespace App\Http\Livewire\Admin\ProductsManagement\Customers;

use App\Models\Customer;
use App\Traits\ImageTrait;
use Livewire\Component;
use Livewire\WithPagination;

class Customers extends Component
{
    use WithPagination,ImageTrait;
    public $search,$status;

    protected $listeners=['delete'];
    public function confirmDelete($id){
        $this->emit('confirmDelete', $id);
    }
    public function delete(Customer $customer){
        // $this->livewireDeleteSingleImage($customer,'users');
        $customer->delete();
        session()->flash('success',__('text.User Deleted Successfully'));
        create_activity('User Deleted',auth()->user()->id,auth()->user()->id);

    }
    public function render()
    {
        $users=Customer::when($this->status  == 2 || $this->status  == 1,function($q){
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
        return view('admin.productManagement.customers.index',compact('users'))->extends('admin.layouts.appLogged')->section('content');
    }
}
