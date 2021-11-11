<?php

namespace App\Http\Livewire\Admin\ProductsManagement\Categories;

use App\Http\Controllers\admin\productManagement\categories\CategoryController;
use App\Models\Category;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class Categories extends Component
{
    use WithFileUploads,WithPagination;

    public $name_ar;
    public $name_en;
    public $image;
    public $parent;
    public $slug;
    public $ids;
    public $search;
    public $update_categories=[];
    public $cateUpdate=[];

    protected $listeners=['delete'];



    public function store(){
        $category=new CategoryController();
        $data=$this->validateData();
        $data['parent']=$this->parent;
        $data=$this->setSlug($data);
        $category->store($data);
        session()->flash('success', __('text.Category Created Successfully'));
        $this->resetVariables();
        $this->emit('addedCategory');

        create_activity('Category Created',auth()->user()->id,auth()->user()->id);

    }

    public function edit($id){
        $this->update_categories=[];
        $this->cateUpdate=[];
        $this->ids=$id;
        $cat=Category::findOrFail($id);
        $this->name_ar= $cat->name_ar;
        $this->name_en=$cat->name_en;
        $this->slug=$cat->slug;
        $this->parent=$cat->parent_id;
        $this->getCategoriesForUpdate($id);
        $arr=collect($this->cateUpdate)->collapse();
        $this->update_categories=Category::withTrashed()->whereNotIn('id',$arr)->where('id','!=',$cat->id)->pluck('id')->toArray();
    }

    public function update(){

        $data=$this->UpdateCategoryRequestValidate($this->ids);
        $category_controller=new CategoryController();
        $category=Category::findOrFail($this->ids);
        $data=$this->setSlug($data);
        $old_parent_category=$category->parent_category;
        if(isset($data['parent']) && in_array($data['parent'],$this->update_categories)){
            $category_controller->update($data,$category);
        }else{// for root category
            $data['parent']=$this->parent;
            $category_controller->update($data,$category);
        }

        if($category->status == 1 && $category->wasChanged('parent_id')){
            $this->updateCategoryStatus($category->parent_category);
            if($old_parent_category)
                $this->deleteCategoryStatus($old_parent_category);
        }

        if($category->wasChanged()){
            session()->flash('success',__('text.Category Updated Successfully'));
            $this->update_categories=[];
            $this->resetVariables();
            create_activity('Category Updated',auth()->user()->id,auth()->user()->id);
        }

        $this->emit('updatedCategory');

    }

    public function confirmDelete($id){
        $this->emit('confirmDelete', $id);
    }

    public function delete($id){
        $category_controller=new CategoryController();
        $category=Category::findOrFail($id);
        $parent_category=$category->parent_category;
        $category_controller->destroy($category);
        if($parent_category){
            $this->deleteCategoryStatus($parent_category);
        }
        session()->flash('success',__('text.Category Deleted Successfully'));
        create_activity('Category Deleted',auth()->user()->id,auth()->user()->id);

    }

    public function render()
    {
        $categories=Category::with('child_categories')->when($this->search,function ($q){
            return $q->where('name_ar','like','%'.$this->search.'%')
                ->orWhere('name_en','like','%'.$this->search.'%');
        })->paginate(10);
        return view('admin.productManagement.categories.index',compact('categories'))->extends('admin.layouts.appLogged')->section('content');
    }

    public function validateData()
    {
       return $this->validate([
            'name_ar' => 'required|string|max:255|unique:categories|unique:categories,name_en',
            'name_en' => 'required|string|max:255|unique:categories|unique:categories,name_ar',
            'slug' => 'nullable|string|max:255',
           'image' => 'nullable|mimes:jpg,png,jpeg,gif',
            'parent' => 'nullable|exists:categories,id',
        ]);
    }

    public function UpdateCategoryRequestValidate($categoryId){
        if($this->parent){
            return $this->validate([
                'name_ar' =>['required' , Rule::unique('categories','name_ar')->ignore($categoryId), Rule::unique('categories','name_en')->ignore($categoryId)],
                'name_en' =>['required' , Rule::unique('categories','name_ar')->ignore($categoryId), Rule::unique('categories','name_en')->ignore($categoryId)],
                'slug' => 'nullable|string|max:255',
                'image' => 'nullable|mimes:jpg,png,jpeg,gif',
                'parent' => ['sometimes',Rule::exists('categories','id')->whereNot('id',$categoryId),Rule::in($this->update_categories)],
            ]);
        }else{
            return $this->validate([
                'name_ar' =>['required' , Rule::unique('categories','name_ar')->ignore($categoryId), Rule::unique('categories','name_en')->ignore($categoryId)],
                'name_en' =>['required' , Rule::unique('categories','name_ar')->ignore($categoryId), Rule::unique('categories','name_en')->ignore($categoryId)],
                'slug' => 'nullable|string|max:255',
                'image' => 'nullable|mimes:jpg,png,jpeg,gif',
            ]);
        }


    }


    public function resetVariables(){
        $this->name_ar= null;
        $this->name_en=null;
        $this->image = null;
        $this->slug=null;
        $this->parent=null;
        /*$this->type=null;*/
        $this->ids=null;
    }
    public function setSlug($data){
        if ($this->slug == null){
            $data['slug'] = $this->name_en.'-'.$this->name_ar;
        }
        return $data;

    }

    public function getCategoriesForUpdate($id){
        $category=Category::find($id);
        if($this->cateUpdate[]=$category->child_categories->pluck('id')->toArray()){
            foreach($category->child_categories as $cat){
                $this->getCategoriesForUpdate($cat->id);
            }
        }

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

    protected function deleteCategoryStatus($cat){

        if($cat->products->where('isActive',1)->count() != 0 || $cat->child_categories->where('status',1)->count() > 0){
           return ;
        }else{
            $cat->update(['status' => 0]);
            $cat->save();
            if( $cat->parent_id == 0)
                return;
            $this->deleteCategoryStatus($cat->parent_category);

        }
    }
}
