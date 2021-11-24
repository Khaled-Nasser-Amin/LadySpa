<?php

namespace App\Http\Livewire\Admin\ProductsManagement\Products;

use App\Models\Color;
use Livewire\Component;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;


class ProductDetails extends Component
{
    use AuthorizesRequests;
    public $images,$product;



    public function render()
    {
        return view('components.admin.products.product-details');
    }



}
