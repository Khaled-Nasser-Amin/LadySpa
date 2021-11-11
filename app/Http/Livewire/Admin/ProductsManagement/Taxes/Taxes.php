<?php

namespace App\Http\Livewire\Admin\ProductsManagement\Taxes;

use App\Models\Tax;
use App\Traits\ImageTrait;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class Taxes extends Component
{
    use WithPagination,ImageTrait;
    public $search,$name_ar,$name_en,$tax,$ids;


    protected $listeners=['delete'];
    public function confirmDelete($id){
        $this->emit('confirmDelete', $id);
    }
    public function delete(Tax $tax){
        $tax->delete();
        session()->flash('success',__('text.Deleted Successfully'));
        create_activity('Tax Deleted',auth()->user()->id,auth()->user()->id);

    }
    public function render()
    {
        // $response = file_get_contents('https://maps.googleapis.com/maps/api/geocode/json?latlng=26.872544,41.322728&key=AIzaSyBmX3cxNy7VH9WLrzoh6FLGkjtZ0g3tLSE');
        // dd(json_decode($response));

        $taxes=Tax::when($this->search,function ($q){
                $q->where('name_ar','like','%'.$this->search.'%')
                    ->orWhere('tax','like','%'.$this->search.'%')
                    ->orWhere('name_en','like','%'.$this->search.'%');

            })->latest()->paginate(10);
        return view('admin.productManagement.taxes.index',compact('taxes'))->extends('admin.layouts.appLogged')->section('content');
    }

    public function store(){
        $data=$this->validation();
        Tax::create($data);
        session()->flash('success',__('text.Created Successfully'));
        $this->resetVariables();
        $this->emit('addedTax');
        create_activity('Tax Created',auth()->user()->id,auth()->user()->id);

    }
    protected function validation(){
        return $this->validate([
            'name_ar' => ['required','string','max:255',Rule::unique('taxes','name_ar'),Rule::unique('taxes','name_en')],
            'name_en' => ['required','string','max:255',Rule::unique('taxes','name_ar'),Rule::unique('taxes','name_en')],
            'tax' => 'required|integer|max:100',
        ]);
    }


    public function edit($id){
        $this->ids=$id;
        $tax=Tax::findOrFail($id);
        $this->name_ar= $tax->name_ar;
        $this->name_en= $tax->name_en;
        $this->tax= $tax->tax;
    }

    public function update(){

        $data=$this->UpdateTaxValidate($this->ids);
        $shipping=Tax::findOrFail($this->ids);
        $shipping->update($data);
        $shipping->save();
        if($shipping->wasChanged()){
            session()->flash('success',__('text.Updated Successfully'));
            create_activity('Tax Updated',auth()->user()->id,auth()->user()->id);

            $this->resetVariables();
        }
        $this->emit('EditTax');
    }

    protected function UpdateTaxValidate($TaxId){
        return $this->validate([
            'name_ar' => ['required','string','max:255',Rule::unique('taxes','name_ar')->ignore($TaxId),Rule::unique('taxes','name_en')],
            'name_en' => ['required','string','max:255',Rule::unique('taxes','name_ar'),Rule::unique('taxes','name_en')->ignore($TaxId)],
            'tax' => 'required|integer|max:100',
        ]);
    }


    public function resetVariables(){
        $this->name_ar= null;
        $this->name_en=null;
        $this->tax=null;
    }

}
