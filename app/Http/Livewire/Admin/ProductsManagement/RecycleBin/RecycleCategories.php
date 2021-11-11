<?php

namespace App\Http\Livewire\Admin\ProductsManagement\RecycleBin;

use App\Models\Category;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;


class RecycleCategories extends Component
{
    use WithFileUploads,WithPagination;
    public $search;

    protected $listeners=['restore'];





    public function confirmRestore($id){
        $this->emit('confirmRestore', $id);
    }

    public function restore($id){
        Gate::authorize('isAdmin');
        $category=Category::onlyTrashed()->findOrFail($id);
        $category->restore();
        if($category->status == 1 && $category->parent_id != 0){
            $this->updateCategoryStatus($category->parent_category);
        }
        session()->flash('success',__('text.Restored Successfully'));
        create_activity('Category Restored',auth()->user()->id,auth()->user()->id);

    }

    public function render()
    {
        $categories=Category::onlyTrashed()->with('child_categories')
        ->where(function($q){
            return $q->when($this->search,function ($q){

                return $q->where('name_ar','like','%'.$this->search.'%')
                    ->orWhere('name_en','like','%'.$this->search.'%');
            });
        })
        ->paginate(10);
        return view('admin.productManagement.recycle_bin.recycle_categories',compact('categories'))->extends('admin.layouts.appLogged')->section('content');
    }

    protected function updateCategoryStatus($cat){
        if($cat->status == 1)
            return ;
        $cat->update(['status' => 1]);
        $cat->save();

        if($cat->parent_id == 0)
            return;
        $this->updateCategoryStatus($cat->parent_category);


    }

}
