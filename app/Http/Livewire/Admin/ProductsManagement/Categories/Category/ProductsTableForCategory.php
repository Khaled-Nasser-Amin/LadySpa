<?php

namespace App\Http\Livewire\Admin\ProductsManagement\Categories\Category;

use App\Models\Category;
use App\Models\Size;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class ProductsTableForCategory extends Component
{
    use WithPagination;
    public $category;
    protected $listeners=['categoryDetails' => 'edit'];

    public function mount(Category $category){
        $this->category=$category;
    }
    public function render()
    {

        $month= Carbon::now()->month;
        $year= Carbon::now()->year;

        session()->put('current_month',$month);
        session()->put('current_year',$year);

        $products_have_orders=$this->category->orders()->pluck('id')->toArray();
        $sizes=Size::withTrashed()->whereHas('order',function($query) use($year,$month){
            $query->whereYear('created_at',$year)->whereMonth('created_at',$month);
        })->join('colors','colors.id','sizes.color_id')->select('sizes.*')
        ->where(function($q) use($products_have_orders){
            return $q
            ->whereIn('colors.product_id',$products_have_orders)
            ->orderBy('colors.product_id');
        })->get();


        return view('components.admin.categories.category.products-table-for-category',compact('sizes'));
    }


    public function searchByProduct($product){
        session()->put('product_id' , $product);
        $this->redirect(route('admin.products'));
    }
}
