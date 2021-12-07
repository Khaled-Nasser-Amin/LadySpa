<?php

namespace App\Http\Livewire\Admin\ProductsManagement\Codes;

use App\Models\Code;
use App\Traits\ImageTrait;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class Codes extends Component
{
    use WithPagination,ImageTrait;
    public $search,$name_ar,$name_en,$ids,$code;
    protected $listeners=['delete'];

    public function mount()
    {
       $this->changeCode();
    }

    public function changeCode()
    {
        $code=substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'),0,7);
        $this->code=$code;
    }
    public function confirmDelete($id){
        $this->emit('confirmDelete', $id);
    }
    public function delete(Code $code){
        $code->delete();

        session()->flash('success',__('text.Deleted Successfully'));
        create_activity('Code Deleted',auth()->user()->id,auth()->user()->id);

    }
    public function render()
    {
        $codes=Code::when($this->search,function ($q){
                $q->where('code','like','%'.$this->search.'%');

            })->latest()->paginate(10);
        return view('admin.productManagement.codes.index',compact('codes'))->extends('admin.layouts.appLogged')->section('content');
    }

    public function store(){
        $data=$this->validation();
        Code::create($data);
        session()->flash('success',__('text.Created Successfully'));
        $this->resetVariables();
        $this->emit('addedCode');
        create_activity('Code Created',auth()->user()->id,auth()->user()->id);

    }
    protected function validation(){
        return $this->validate([

        ]);
    }


    public function resetVariables(){
        $this->name_ar= null;
        $this->name_en=null;
    }

}
