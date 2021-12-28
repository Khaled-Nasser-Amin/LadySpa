<?php

namespace App\Http\Livewire\Admin\Profile;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class UpdateSessionsConfigration extends Component
{

    public $opening_time,$closing_time,$session_rooms_limitation_indoor,$session_rooms_limitation_outdoor;
    public function mount(){
        $this->session_rooms_limitation_indoor=$this->getUserProperty()->session_rooms_limitation_indoor;
        $this->session_rooms_limitation_outdoor=$this->getUserProperty()->session_rooms_limitation_outdoor;
        $this->opening_time=date('h:i', strtotime($this->getUserProperty()->opening_time));
        $this->closing_time=date('h:i', strtotime($this->getUserProperty()->closing_time));
    }
    public function updateGeoLocation(){
        $this->validate([
            'opening_time' => 'required|date_format:H:i',
            'closing_time' => 'required|date_format:H:i|after:opening_time',
            'session_rooms_limitation_indoor' => 'required|numeric|gt:0',
            'session_rooms_limitation_outdoor' => 'required|numeric|gte:0',
        ]);
        $this->getUserProperty()->update([
            'opening_time' => $this->opening_time,
            'closing_time' => $this->closing_time,
            'session_rooms_limitation_indoor' => $this->session_rooms_limitation_indoor,
            'session_rooms_limitation_outdoor' => $this->session_rooms_limitation_outdoor,
        ]);
        $this->getUserProperty()->save();
        $this->emit('saved');
    }

    public function getUserProperty()
    {
        return Auth::user();
    }


    public function render()
    {

        return view('admin.Profile.update-sessions-configration');
    }
}
