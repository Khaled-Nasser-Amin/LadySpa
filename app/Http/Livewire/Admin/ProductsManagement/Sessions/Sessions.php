<?php

namespace App\Http\Livewire\Admin\ProductsManagement\Sessions;

use App\Http\Controllers\admin\productManagement\products\ProductController;
use App\Http\Controllers\admin\productManagement\sessions\SessionController;
use App\Models\Setting;
use App\Models\Xsession;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Sessions extends Component
{
    use WithPagination,AuthorizesRequests;
    public $price,$date,$sessionName,$addition_name,$addition_price,$featured_non_featured;



    protected $listeners=['delete'];

    public function mount(){
        $session="";
        if(session()->has('session_id'))
            $session=Xsession::find(session()->get('session_id'));
        if($session){
            $this->price=$session->price;
            $this->date=$session->created_at;
            $this->sessionName=app()->getLocale() == 'ar' ? $session->name_ar:$session->name_en;
        }

        session()->forget(['session_id']);
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
        $instance=new SessionController();
        $vendor_id=$instance->destroy($session);
        session()->flash('success',__('text.Session Deleted Successfully') );
        create_activity('Session Deleted',auth()->user()->id,$vendor_id);
    }


    //update product's featured
    public function updateFeatured(Xsession $session){

        $numberOfSessions=auth()->user()->sessions->where('featured',1)->count();
        if ($numberOfSessions < 6 || $session->featured == 1){
            if($session->featured == 0 ){
                $featured= 1;
                create_activity('Added a session as a feature',auth()->user()->id,$session->user_id);

            }else{
                $featured= 0;
                create_activity('Removed a session as a feature',auth()->user()->id,$session->user_id);
            }

            $session->update([
                'featured'=>$featured
            ]);
        }else{
            $this->dispatchBrowserEvent('danger',__('text.You have only'). ' 6 ' . __('text.special sessions'));
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
        if($session->isActive == 0 ){
            $status= 1;
            $session->update([
                'isActive'=>$status
            ]);
            create_activity('Active a session',auth()->user()->id,$session->user_id);

        }else{
            $status= 0;
            $session->update([
                'isActive'=>$status
            ]);
            create_activity('Unactive a session',auth()->user()->id,$session->user_id);
        }


    }


    //search and return products paginated
    protected function search(){
       return Xsession::

        when($this->addition_name,function ($q) {
            return $q->whereHas('additions', function (Builder $query){
                return $query->where('name_ar','like','%'.$this->addition_name.'%')
                ->orWhere('name_en','like','%'.$this->addition_name.'%');
            });
        })
        ->when($this->addition_price,function ($q) {
                return $q->whereHas('additions', function (Builder $query){
                    return $query->where('price','like','%'.$this->addition_price.'%');
                });
        })

        ->when($this->sessionName,function ($q){
            $q->where(function($q){
                return $q->where('xsessions.name_ar','like','%'.$this->sessionName.'%')
                ->orWhere('xsessions.name_en','like','%'.$this->sessionName.'%');
            });
        })



        ->when($this->price,function ($q) {
            $q->where(function($q){
                return $q->where('xsessions.price','=',$this->price)
                ->orWhere('xsessions.sale','=',$this->price);
            });

        })


        ->when($this->date,function ($q)  {
            return $q->whereDate('xsessions.created_at',$this->date);
        })->when($this->featured_non_featured,function ($q)  {
            $featured=$this->featured_non_featured == 'Featured' ? 1 : 0;
            return $q->where('xsessions.featured',$featured);
        })
        ->distinct('xsessions.id')->latest('xsessions.created_at')
        ->paginate(12);

    }



}

