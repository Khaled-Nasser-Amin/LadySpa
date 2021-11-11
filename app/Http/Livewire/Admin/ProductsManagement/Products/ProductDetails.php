<?php

namespace App\Http\Livewire\Admin\ProductsManagement\Products;

use App\Models\Color;
use Livewire\Component;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;


class ProductDetails extends Component
{
    use AuthorizesRequests;
    public $images,$product,$active_color;


    public function changeColor(Color $color){
        $this->active_color=$color->id;
        $this->images=$color->images;
    }
    public function render()
    {
        return view('components.admin.products.product-details');
    }



}
