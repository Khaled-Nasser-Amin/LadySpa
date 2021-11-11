<?php

namespace App\Http\Livewire\Admin\ProductsManagement\Activities;

use App\Models\Activity;
use App\Traits\ImageTrait;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;


class Activities extends Component
{
    use WithPagination,ImageTrait;
    public $search;

    protected $listeners=['delete','deleteAll'];
    public function confirmDelete($id){
        $this->emit('confirmDelete', $id);
    }
    public function delete(Activity $activity){
        Gate::authorize('delete-activity',$activity);
        $activity->delete();
        create_activity('Activity Deleted',auth()->user()->id,$activity->belongs_to_id);
        session()->flash('success',__('text.Deleted Successfully'));
    }

    public function confirmDeleteAll(){
        $this->emit('confirmDeleteAll');
    }

    public function deleteAll(){

        if(auth()->user()->role == 'admin'){
            $ids=Activity::select('belongs_to_id')->groupBy('belongs_to_id')->get()->pluck('belongs_to_id');
            Activity::truncate();
            foreach($ids as $id){
                create_activity('Activities Deleted',auth()->user()->id,$id);
            }
        }else{
            Activity::where('vendor_id',auth()->user()->id)->delete();
            create_activity('Activities Deleted',auth()->user()->id,auth()->user()->id);
        }
        session()->flash('success',__('text.Deleted Successfully'));


    }
    public function render()
    {
        $activities=Activity::
        where(function($q){
          return $q->when($this->search,function ($q){
                $q->join('users','users.id','activities.vendor_id')
                ->where('users.store_name','like','%'.$this->search.'%')
                ->select('activities.*');
            });
        })->when(auth()->user()->role != 'admin',function($q){
                return $q->where('belongs_to_id',auth()->user()->id);
            })
            ->latest()->paginate(15);
        return view('admin.productManagement.activities.index',compact('activities'))->extends('admin.layouts.appLogged')->section('content');
    }



}
