<?php

namespace App\Http\Livewire\Admin\ProductsManagement\Vendors;

use App\Models\User;
use App\Traits\ImageTrait;
use Illuminate\Http\Request;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class Vendors extends Component
{
    use WithPagination,ImageTrait,WithFileUploads;
    public $search,$status;

    public $name,$email,$location,$phone,$whatsapp,$image,$store_name,$password,$password_confirmation;

    public $user;


    protected $listeners=['delete'];


    public function confirmDelete($id){
        $this->emit('confirmDelete', $id);
    }
    public function delete(User $vendor){
        // $this->livewireDeleteSingleImage($vendor,'users');
       $products=$vendor->products();
        $vendor->delete();
        $products->delete();
        session()->flash('success',__('text.Vendor Deleted Successfully'));
        create_activity('Vendor Deleted',auth()->user()->id,auth()->user()->id);

    }
    public function render()
    {
        $vendors=$this->search();

        return view('admin.productManagement.vendors.index',compact('vendors'))->extends('admin.layouts.appLogged')->section('content');
    }


    public function store(){
        $data=$this->validation();
        $data['password']=bcrypt($this->password);
        $data['activation']=1;

        if($data['image']){
           $data= $this->livewireAddSingleImage($data,$data,'users');
        }

        User::create($data);
        session()->flash('success',__('text.Vendor Created Successfully'));
        $this->resetVariables();
        $this->emit('vendorCreated');
        create_activity('Vendor Created',auth()->user()->id,auth()->user()->id);


    }
    public function updated($fields){
        $this->validateOnly($fields,[
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'store_name' => 'required|string|max:255|unique:users',
            'phone' => 'required|numeric|unique:users',
            'whatsapp' => 'required|numeric|unique:users',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|alpha_num|min:8',
            'password_confirmation' => 'required|alpha_num|min:8|max:255|',
        ]);
    }

    public function validation(){
      return  $this->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'store_name' => 'required|string|max:255|unique:users',
            'phone' => 'required|numeric|unique:users',
            'whatsapp' => 'required|numeric|unique:users',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|alpha_num|min:8|max:255|confirmed',
            'password_confirmation' => 'required|string|max:255|',
            'image' => 'nullable|mimes:jpg,png,jpeg,gif',

        ]);
    }

    protected function search(){
        return User::with('orders')->where('role','!=','admin')->when($this->status  == 2 || $this->status  == 1,function($q){
            $this->status  == 2 ? $q->where('activation',0):$q->where('activation',1);
        })
        ->where(function($q){
           $q->when($this->search,function ($q){
             $q->where('name','like','%'.$this->search.'%')
                ->orWhere('email','like','%'.$this->search.'%')
                ->orWhere('phone','like','%'.$this->search.'%')
                ->orWhere('whatsapp','like','%'.$this->search.'%')
                ->orWhere('store_name','like','%'.$this->search.'%')
                ->orWhere('location','like','%'.$this->search.'%');
            });
        })

        ->latest()->paginate(10);
    }

    public function getVendorForUpdate(User $user){
        $this->user=$user;
    }

    public function updateVendor(){
        $data=$this->validation_for_update();

        if($this->user){
            $this->user->update(['password' => bcrypt($data['password'])]);
            $this->user->save();
            if($this->user->wasChanged()){
                create_activity('Password Changed',auth()->user()->id,$this->user->id);
                session()->flash('success',__('text.Vendor Updated Successfully'));
            }
        }

        $this->resetVariables();
        $this->emit('vendorUpdated');


    }

    public function validation_for_update(){
        return  $this->validate([
              'password' => 'required|alpha_num|min:8|max:255|confirmed',
              'password_confirmation' => 'required|string|max:255|',
          ]);
      }

    //user can or can not add product
    public function productStatus(User $user){
        if($user->role != "admin"){
            if($user->add_product == 1){
                $value= 0;
                create_activity('Upload product status is deactivated',auth()->user()->id,$user->id);

            }else{
                $value= 1;
                create_activity('Upload product status is activated',auth()->user()->id,$user->id);

            }

            $user->update(['add_product' => $value]);
            $user->save();
        }
    }


    public function resetVariables(){
        $this->password='';
        $this->password_confirmation='';
        $this->name='';
        $this->email='';
        $this->location='';
        $this->phone='';
        $this->whatsapp='';
    }



}
