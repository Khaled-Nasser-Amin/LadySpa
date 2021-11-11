<?php

namespace App\Http\Livewire\Admin\ProductsManagement\Shipping;

use App\Models\Setting;
use App\Models\Shipping;
use App\Traits\ImageTrait;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class ShippingCosts extends Component
{
    use WithPagination,ImageTrait;
    public $search,$city_name,$shipping_cost,$ids,$shipping_status,$shipping_cost_by_kilometer;

    public $cities=[
        '24.6500,46.7100'=>'Riyadh-الرياض',
        '21.5428,39.1728'=>'Jeddah-جدة',
        '21.4225,39.8261'=>'Makkah-مكة المكرمة',
        '26.4333,50.1000'=>'Dammam-الدمام',
        '24.4667,39.6000'=>'Madinah-المدينة المنورة',
        '25.3608,49.5997'=>'Al Hufaf-الهفوف',
        '26.3333,43.9667'=>'Buraydah-بريدة',
        '23.4895,46.7564'=>'Al Ḩillah-الحلة',
        '26.27944,50.20833'=>'Khobar-الخبر',
        '25.424331636,49.619830854'=>'Al Ahsa-الاحساء',
        '21.2667,40.4167'=>'Taif-الطائف',
        '27.01122,49.65825'=>'Al Jubail-الجبيل',
        '28.3838,36.5550'=>'Tabuk-تبوك',
        '18.2167,42.5000'=>'Abha-أبها',
        '18.3000,42.7333'=>'Khamis Mushait	-خميس مشيط',
        '27.5236,41.7001'=>'Hail-حائل',
        '16.8892,42.5611'=>'Jazan-جازان',
        '17.4917,44.1322'=>'Najran-نجران',
        '20.0129,41.4677'=>'Bahah-الباحة',
        '30.0000,40.1333'=>'Sakākā-سكاكا',
        '26.094088,43.973454'=>'Qassim-القصيم',
        '26.5196,50.0115'=>'Al Qaţīf-القطيف',
        '24.1556,47.3120'=>'Al Kharj-الخرج',
        '30.9833,41.0167'=>'Arar-عرعر',
    ];


    public function mount()
    {
       $setting=Setting::find(1);
       $this->shipping_status=$setting->shipping_status;
       $this->shipping_cost_by_kilometer=$setting->shipping_cost_by_kilometer;
    }


    protected $listeners=['delete'];

    public function confirmDelete($id){
        $this->emit('confirmDelete', $id);
    }
    public function delete(Shipping $shipping){
        $shipping->delete();
        session()->flash('success',__('text.Deleted Successfully'));
        create_activity('Shipping Cost Deleted',auth()->user()->id,auth()->user()->id);
    }

    public function updatedShippingStatus($value)
    {
        $setting=Setting::find(1);
        $setting->update(['shipping_status' => $value]);
        $setting->save();


    }
    public function render()
    {
        // $response = file_get_contents('https://maps.googleapis.com/maps/api/geocode/json?latlng=26.872544,41.322728&key=AIzaSyBmX3cxNy7VH9WLrzoh6FLGkjtZ0g3tLSE');
        // dd(json_decode($response));

        $shipping=Shipping::when($this->search,function ($q){
                $q->where('city_name','like','%'.$this->search.'%')
                   ->orWhere('shipping_cost','like','%'.$this->search.'%');

            })->latest()->paginate(10);
        return view('admin.productManagement.shipping.index',compact('shipping'))->extends('admin.layouts.appLogged')->section('content');
    }

    public function store(){
        $this->validation();
        Shipping::create([
            'city_name' => $this->cities[$this->city_name] ,
            'geoLocation' => $this->city_name, //geoLocation
            'shipping_cost' => $this->shipping_cost,
        ]);
        session()->flash('success',__('text.Created Successfully'));
        $this->resetVariables();
        $this->emit('addedShipping');
        create_activity('Shipping Cost Created',auth()->user()->id,auth()->user()->id);

    }
    protected function validation(){
        return $this->validate([
            'city_name' => ['required','string','max:255',Rule::unique('Shippings','geoLocation')],
            'shipping_cost' => 'required|numeric',
        ]);
    }


    public function edit($id){
        $this->ids=$id;
        $shipping=Shipping::findOrFail($id);
        $this->city_name= $shipping->city_name;
        $this->shipping_cost=$shipping->shipping_cost;
    }

    public function update(){

        $this->UpdateShippingValidate();
        $shipping=Shipping::findOrFail($this->ids);
        $shipping->update([
            'shipping_cost' => $this->shipping_cost
        ]);
        $shipping->save();
        if($shipping->wasChanged()){
            session()->flash('success',__('text.Updated Successfully'));
            create_activity('Shipping Cost Updated',auth()->user()->id,auth()->user()->id);

            $this->resetVariables();
        }
        $this->emit('EditShipping');
    }

    protected function UpdateShippingValidate(){
        return $this->validate([
            'shipping_cost' => 'required|numeric',
        ]);
    }


    public function resetVariables(){
        $this->city_name= null;
        $this->shipping_cost=null;
    }

    public function shippingCost()
    {
        $setting=Setting::find(1);
        $setting->update(['shipping_cost_by_kilometer' => $this->shipping_cost_by_kilometer]);
        $setting->save();
        $this->dispatchBrowserEvent('success',__('text.Updated Successfully'));
    }

}
