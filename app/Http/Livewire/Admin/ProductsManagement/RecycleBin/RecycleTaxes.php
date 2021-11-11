<?php

namespace App\Http\Livewire\Admin\ProductsManagement\RecycleBin;

use App\Models\Tax;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Gate;

class RecycleTaxes extends Component
{
    use WithFileUploads,WithPagination,AuthorizesRequests;
    public $search;

    protected $listeners=['restore'];


    public function confirmRestore($id){
        $this->emit('confirmRestore', $id);
    }

    public function restore($id){
        $tax=Tax::onlyTrashed()->findOrFail($id);
        Gate::authorize('isAdmin');
        $tax->restore();
        create_activity('Tax Restored',auth()->user()->id,auth()->user()->id);
        session()->flash('success',__('text.Restored Successfully'));

    }

    public function render()
    {
        $taxes=$this->search();
        return view('admin.productManagement.recycle_bin.recycle_taxes',compact('taxes'))->extends('admin.layouts.appLogged')->section('content');
    }

    //search and return products paginated
    protected function search(){
        return Tax::onlyTrashed()->where(function($q){
            return $q->when($this->search,function ($q){
                $q->where('name_ar','like','%'.$this->search.'%')
                    ->orWhere('tax','like','%'.$this->search.'%')
                    ->orWhere('name_en','like','%'.$this->search.'%');

            });
        })->latest()->paginate(10);
    }



}



