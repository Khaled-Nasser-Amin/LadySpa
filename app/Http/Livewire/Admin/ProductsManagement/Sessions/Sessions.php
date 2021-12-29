<?php

namespace App\Http\Livewire\Admin\ProductsManagement\Sessions;

use App\Http\Controllers\admin\productManagement\sessions\SessionController;
use App\Models\Setting;
use App\Models\Xsession;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Gate;

class Sessions extends Component
{
    use WithPagination,AuthorizesRequests;
    public $price,$date,$sessionName,$addition_name,$addition_price,$featured_non_featured,$store_name;



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
        $setting=Setting::find(1);

        $sessions=$this->search();
        return view('admin.productManagement.sessions.index',compact('sessions','setting'))->extends('admin.layouts.appLogged')->section('content');
    }



    public function confirmDelete($id){

        $this->emit('confirmDelete',$id);
    }

    //delete product
    public function delete(Xsession $session){
        $this->authorize('delete',$session);
        if($session->isActive == 1 ){
            $session->update([
                'isActive'=>0
            ]);
        }
        $instance=new SessionController();
        $vendor_id=$instance->destroy($session);
        session()->flash('success',__('text.Session Deleted Successfully') );
        create_activity('Session Deleted',auth()->user()->id,$vendor_id);
    }


    //update session's featured
    public function updateFeatured(Xsession $session){
        Gate::authorize('isAdmin');


        $numberOfSessions=Xsession::where('featured',1)->count();
        $allowed_featured_sessions=Setting::find(1)->no_of_featured_sessions;

        if ($numberOfSessions < $allowed_featured_sessions || $session->featured == 1){
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
            $this->dispatchBrowserEvent('danger',__('text.You have only '). $allowed_featured_sessions . __('text.special sessions'));
        }

    }




    //change session status
    public function updateStatus(Xsession $session){
        $this->authorize('update',$session);

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


    //search and return xsessions paginated
    protected function search(){
       return Xsession::
       join('users','users.id','=','xsessions.user_id')->select('xsessions.*')
       ->where(function ($q) {
            $q->when(auth()->user()->role != 'admin' ,function ($q) {
            return $q->where('user_id',auth()->user()->id);
            });
        })
        ->where(function ($q) {
            $q->when($this->store_name,function ($q) {
                return $q
                ->where('users.store_name','like','%'.$this->store_name.'%')->select('xsessions.*');
            });
        })
        ->where(function ($q) {

            $q->when($this->addition_name,function ($q) {
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
                    ->orWhere('xsessions.sale','=',$this->price)
                    ->orWhere('xsessions.external_price','=',$this->price)
                    ->orWhere('xsessions.external_sale','=',$this->price);
                });

            })


            ->when($this->date,function ($q)  {
                return $q->whereDate('xsessions.created_at',$this->date);
            })->when($this->featured_non_featured,function ($q)  {
                $featured=$this->featured_non_featured == 'Featured' ? 1 : 0;
                return $q->where('xsessions.featured',$featured);
            });
        })
        ->distinct('xsessions.id')->latest('xsessions.created_at')
        ->paginate(12);

    }



}

