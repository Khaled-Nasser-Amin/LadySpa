<?php

namespace App\Http\Livewire\Admin\ProductsManagement\Sessions;

use App\Models\Color;
use Livewire\Component;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;


class SessionDetails extends Component
{
    use AuthorizesRequests;
    public $images,$product;



    public function render()
    {
        return view('components.admin.sessions.session-details');
    }



}
