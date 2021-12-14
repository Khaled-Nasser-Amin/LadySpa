<?php

namespace App\Http\Livewire\Admin\ProductsManagement\PromoCodes;

use App\Models\Promocode;
use App\Traits\ImageTrait;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class PromoCodes extends Component
{
    use WithPagination,ImageTrait;
    public $searchCode,$searchNumber,$searchFor,$searchType_of_code,$searchType_of_discount,$date,$code,$start_date,$limitation,$end_date,$for,$type_of_code,$type_of_discount,$value,$condition;
    protected $listeners=['delete'];

    public function mount()
    {
        $this->type_of_discount="percentage";
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
    public function delete(Promocode $code){
        $code->delete();
        session()->flash('success',__('text.Deleted Successfully'));
        create_activity('Promotion Code Deleted',auth()->user()->id,auth()->user()->id);

    }
    public function render()
    {
        $codes=$this->search();
        return view('admin.productManagement.codes.index',compact('codes'))->extends('admin.layouts.appLogged')->section('content');
    }

    public function store(){
        $data=$this->validation();
        $code=Promocode::create($data);
        $code->vendor()->associate(auth()->user()->id);
        session()->flash('success',__('text.Created Successfully'));
        $this->resetVariables();
        $this->emit('addedCode');
        create_activity('Promotion Code Created',auth()->user()->id,auth()->user()->id);

    }
    protected function validation(){
        return $this->validate([
            'code' => 'required|unique:promocodes',
            'start_date' => 'required|date|after:yesterday',
            'end_date' => 'required|date|after_or_equal:start_date',
            'limitation' => 'required|integer',
            'for' => [Rule::in(['general','products','sessions'])],
            'type_of_code' => [Rule::in(['normal','special'])],
            'type_of_discount' => [Rule::in(['amount','percentage'])],
            'value' => 'required|integer',
            'condition' => 'required|integer',
        ]);
    }


    public function resetVariables(){
      $this->reset(['code','start_date','limitation','end_date','for','condition','type_of_code','value','type_of_discount']);
    }

    protected function search()
    {
        return Promocode::
        when($this->searchCode,function ($q){
            $q->where('code','like','%'.$this->searchCode.'%');

        })
        ->when($this->searchNumber,function ($q){
            $q->where(function($q){
                $q->where('limitation',$this->searchNumber)
                ->orWhere('value',$this->searchNumber)
                ->orWhere('condition',$this->searchNumber);
            });

        })
        ->when($this->date,function ($q){
            $q->where(function($q){
                $q->where('start_date','like','%'.$this->date.'%')
                ->orWhere('end_date','like','%'.$this->date.'%');
            });

        })
        ->when($this->searchFor,function ($q){
            $q->where('for',$this->searchFor);

        })
        ->when($this->searchType_of_code,function ($q){
            $q->where('type_of_code',$this->searchType_of_code);

        })
        ->when($this->searchType_of_discount,function ($q){
            $q->where('type_of_discount',$this->searchType_of_discount);

        })
        ->latest()->paginate(10);
    }

}
