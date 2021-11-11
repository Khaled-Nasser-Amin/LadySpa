<?php

namespace App\Http\Livewire\Admin\Profile;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class UpdateGeoLocation extends Component
{

    public $geoLocation;
    public function mount(){
        $this->geoLocation=$this->getUserProperty()->geoLocation;
    }
    public function updateGeoLocation(){
        $data=$this->validate(['geoLocation' => 'required|string',]);

        $this->getUserProperty()->update(['geoLocation' => $this->geoLocation]);
        $this->getUserProperty()->save();
        $this->emit('saved');


    }

    public function getUserProperty()
    {
        return Auth::user();
    }


    public function render()
    {

        return view('admin.Profile.update-geo-location');
    }
}
