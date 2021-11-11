<?php

namespace App\Http\Controllers\admin\productManagement\categories;
use App\Http\Controllers\Controller;

use App\Traits\ImageTrait;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;


class CategoryController extends Controller
{
    use ImageTrait;

    public function store($request)
    {
        $data=collect($request)->except([/*'parent',*/'image'])->toArray();
        if($request['image']){
            $data=$this->livewireAddSingleImage($request,$data,"categories");

        }
        $category=Category::create($data);
        $this->accociateParentCategory($request,$category);
    }


    public function show(Category $category,$slug)
    {
        return view('admin.productManagement.categories.show',compact('category'));
    }


    public function update($request,$category)
    {
        $data=collect($request)->except(['parent','image'])->toArray();
        if ($request['image']){
            $data=$this->livewireAddSingleImage($request,$data,"categories");
            $this->livewireDeleteSingleImage($category,"categories");
        }
        $category->update($data);
        $category->save();
        $this->accociateParentCategory($request,$category);
    }


    public function destroy($category)
    {
        // $this->livewireDeleteSingleImage($category,"categories");
        $category->delete();
    }

    public function accociateParentCategory($request,$category){
        if ($request['parent']){
            $category->parent_category()->associate($request['parent'])->save();
        }elseif($request['parent'] == "0"){
            $category->update(['parent_id' => 0]);
        }
    }

}
