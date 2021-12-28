<?php

namespace App\Http\Livewire\Admin\ProductsManagement\RecycleBin;

use App\Models\Xsession;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;


class RecycleSessions extends Component
{
    use WithFileUploads,WithPagination,AuthorizesRequests;
    public $price,$date,$sessionName,$addition_name,$addition_price,$featured_non_featured,$store_name;


    protected $listeners=['restore'];


    public function confirmRestore($id){
        $this->emit('confirmRestore', $id);
    }

    public function restore($id){
        $session=Xsession::onlyTrashed()->findOrFail($id);
        $this->authorize('delete',$session);
        if($session->user){
            $session->restore();
            create_activity('Session Restored',auth()->user()->id,$session->user_id);
            session()->flash('success',__('text.Restored Successfully'));

        }else{
            session()->flash('danger',__('text.Please restore vendor first'));

        }


    }

    public function render()
    {
        $sessions=$this->search();
        return view('admin.productManagement.recycle_bin.recycle_sessions',compact('sessions'))->extends('admin.layouts.appLogged')->section('content');
    }

    //search and return sessions paginated
    protected function search(){
        return Xsession::onlyTrashed()->join('users','users.id','=','xsessions.user_id')->select('xsessions.*')->where(function($q){

            return $q
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
             });
        })
             ->distinct('xsessions.id')->latest('xsessions.created_at')
             ->paginate(12);
    }


}





