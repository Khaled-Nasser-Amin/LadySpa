<?php

namespace App\Http\Livewire\Admin\ProductsManagement\Settings;

use App\Models\Setting;
use App\Traits\ImageTrait;
use Livewire\Component;
use Livewire\WithPagination;

class Settings extends Component
{
    use WithPagination,ImageTrait;
    public $payment_token,$twillo_token,$twillo_phone,$twillo_sid,$contact_phone,$contact_email,$contact_whatsapp,$contact_land_line;

    public $setting;
    public function __construct()
    {
        $this->setting=Setting::find(1);
        $this->payment_token=$this->setting->payment_token;
        $this->twillo_token=$this->setting->twillo_token;
        $this->twillo_phone=$this->setting->twillo_phone;
        $this->twillo_sid=$this->setting->twillo_sid;
        $this->contact_phone=$this->setting->contact_phone;
        $this->contact_email=$this->setting->contact_email;
        $this->contact_whatsapp=$this->setting->contact_whatsapp;
        $this->contact_land_line=$this->setting->contact_land_line;

    }




    public function updatePaymentToken(){

        $this->setting->update(['payment_token' => $this->payment_token]);
        $this->setting->save();

        if($this->setting->wasChanged('payment_token')){
            $this->dispatchBrowserEvent('success',__('text.Payment Token Updated Successfully'));
            create_activity('Payment Token Updated',auth()->user()->id,auth()->user()->id);
        }
        return;
    }

    public function updateTwilloConfiguration(){
        $this->validate([
            'twillo_phone' => 'nullable|numeric',
        ]);
        $this->setting->update([
            'twillo_token' => $this->twillo_token,
            'twillo_phone' => $this->twillo_phone,
            'twillo_sid' => $this->twillo_sid,
        ]);
        $this->setting->save();
        if($this->setting->wasChanged('twillo_token') || $this->setting->wasChanged('twillo_phone') ||$this->setting->wasChanged('twillo_sid')){
            $this->dispatchBrowserEvent('success',__('text.Twillo Configuration Updated Successfully'));
            create_activity('Twillo Configuration Updated',auth()->user()->id,auth()->user()->id);
        }
        return;
    }

    public function updateContactInformation(){
        $this->validate([
            'contact_phone' => 'nullable|numeric',
            'contact_whatsapp' => 'nullable|numeric',
            'contact_land_line' => 'nullable|numeric',
        ]);
        $phone=trim($this->contact_phone) == '' ? Null:$this->contact_phone;
        $whatsapp=trim($this->contact_whatsapp) == '' ? Null:$this->contact_whatsapp;
        $land_line=trim($this->contact_land_line) =='' ? Null:$this->contact_land_line;
        $this->setting->update([
            'contact_email' => $this->contact_email,
            'contact_phone' => $phone,
            'contact_whatsapp' => $whatsapp,
            'contact_land_line' => $land_line,
        ]);
        $this->setting->save();
        if($this->setting->wasChanged('contact_email') || $this->setting->wasChanged('contact_phone') ||$this->setting->wasChanged('contact_whatsapp')||$this->setting->wasChanged('contact_land_line')){
            $this->dispatchBrowserEvent('success',__('text.Contact Information Updated Successfully'));

            create_activity('Contact Information Updated',auth()->user()->id,auth()->user()->id);
        }
        return;
    }

    public function render()
    {
        return view('admin.productManagement.settings.index',)->extends('admin.layouts.appLogged')->section('content');
    }

}
