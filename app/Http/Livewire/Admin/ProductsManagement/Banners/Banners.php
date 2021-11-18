<?php

namespace App\Http\Livewire\Admin\ProductsManagement\Banners;

use App\Models\Banner;
use App\Models\Vendor;
use App\Traits\ImageTrait;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class Banners extends Component
{
    use WithPagination,ImageTrait,WithFileUploads;
    public $search,$url,$name,$image,$show_in,$expire_at,$ids;

    protected $listeners=['deleteBanner'];

    public function store(){
        if($this->show_in == 'home'){
            if(Banner::where('show_in','home')->count() >= 15 )
            {
                session()->flash('danger', __('text.You have just 15 banners in home page'));
                $this->emit('addedBanner');
                return ;
            }
        }elseif ($this->show_in == 'shop'){
            if(Banner::where('show_in','shop')->count() >= 15 )
            {
                session()->flash('danger', __('text.You have just 15 banners in shop page'));
                $this->emit('addedBanner');
                return ;
            }
        }
        $data= $this->validateData();
        $data= $this->livewireAddSingleImage($data,$data,'banners');
        Banner::create($data);
        session()->flash('success', __('text.Banner Created Successfully'));
        $this->resetVariables();
        $this->emit('addedBanner');

    }

    public function validateData()
    {
        return $this->validate([
            'name' => 'required|string|max:255|unique:banners',
            'show_in' => 'required|string|max:255|in:home,shop',
            'url' => 'nullable|url|max:255',
            'image' => 'required|mimes:jpg,png,jpeg,gif',
            'expire_at' => 'nullable|date|after_or_equal:today',
        ]);
    }
    public function edit($id){
        $this->ids=$id;
        $banner=Banner::findOrFail($id);
        $this->name= $banner->name;
        $this->url=$banner->url;
        $this->expire_at=$banner->expire_at;
        $this->show_in=$banner->show_in;
    }

    public function update(){
        $data= $this->UpdateBannerRequestValidate($this->ids);
        $banner=Banner::findOrFail($this->ids);
        if ($this->image != null){
            $this->livewireDeleteSingleImage($banner,'banners');
            $data= $this->livewireAddSingleImage($data,$data,'banners');
        }else{
            $data=collect($data)->except('image')->toArray();
        }
        $banner->update($data);
        $banner->save();
        session()->flash('success', __('text.Banner Updated Successfully'));
        $this->resetVariables();
        $this->emit('updatedBanner');
    }

    public function UpdateBannerRequestValidate($Id){
        return $this->validate([
            'name' =>['required' , Rule::unique('banners','name')->ignore($Id)],
            'show_in' => 'required|string|max:255|in:home,shop',
            'url' => 'nullable|url|max:255',
            'image' => 'nullable|mimes:jpg,png,jpeg,gif',
            'expire_at' => 'nullable|date|after_or_equal:today',
        ]);

    }

    public function confirmDelete($id){
        $this->emit('confirmDeleteBanner', $id);
    }

    public function deleteBanner(Banner $banner){
        $this->livewireDeleteSingleImage($banner,'banners');
        $banner->delete();
        session()->flash('success',__('text.Banner Deleted Successfully'));
    }
    public function render()
    {
        $banners=Banner::when($this->search,function ($q){
            return $q->where('name','like','%'.$this->search.'%')
                ->orWhere('url','like','%'.$this->search.'%');
        })->latest()->paginate(10);
        return view('admin.productManagement.banners.index',compact('banners'))->extends('admin.layouts.appLogged')->section('content');
    }

    public function resetVariables(){
        $this->name= null;
        $this->url=null;
        $this->expire_at = null;
        $this->image=null;
        $this->show_in=null;
    }
}
