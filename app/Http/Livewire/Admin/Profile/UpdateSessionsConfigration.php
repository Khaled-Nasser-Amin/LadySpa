<?php

namespace App\Http\Livewire\Admin\Profile;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Illuminate\Database\Eloquent\Builder;

class UpdateSessionsConfigration extends Component
{

    public $opening_time,$closing_time,$session_rooms_limitation_indoor,$session_rooms_limitation_outdoor;
    public function mount(){
        $this->session_rooms_limitation_indoor=$this->getUserProperty()->session_rooms_limitation_indoor;
        $this->session_rooms_limitation_outdoor=$this->getUserProperty()->session_rooms_limitation_outdoor;
        $this->opening_time=date('H:i', strtotime($this->getUserProperty()->opening_time));
        $this->closing_time=date('H:i', strtotime($this->getUserProperty()->closing_time));
    }
    public function updateGeoLocation(){
        $this->validate([
            'opening_time' => 'required|date_format:H:i',
            'closing_time' => 'required|date_format:H:i|after:opening_time',
            'session_rooms_limitation_indoor' => 'required|numeric|gt:0',
            'session_rooms_limitation_outdoor' => 'required|numeric|gte:0',
        ]);
        $outdoor_sessions_count=auth()->user()->whereHas('sessions', function (Builder $q){
                $q->where('external_price','>',0);

        })->count();
        if($outdoor_sessions_count > 0){
            $this->validate([
                'session_rooms_limitation_outdoor' => 'required|numeric|gt:0',
            ],['session_rooms_limitation_outdoor.gt' => app()->getLocale() == 'en' ? 'The session rooms limitation outdoor must be greater than 0 or you have to inactive all external prices of sessions.' : 'يجب أن تكون قيود غرف الجلسات الخارجية أكبر من 0 أو يجب إلغاء تنشيط جميع الأسعار الخارجية للجلسات.']);

        }
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
