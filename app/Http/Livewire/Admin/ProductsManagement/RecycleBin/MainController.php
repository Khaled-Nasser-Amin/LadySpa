<?php

namespace App\Http\Livewire\Admin\ProductsManagement\RecycleBin;


use Livewire\Component;
use Livewire\WithPagination;

class MainController extends Component
{
    use WithPagination;
    public $select;

    public function mount(){
        $this->select='Products';
    }
    public function render()
    {
        return view('admin.productManagement.recycle_bin.index')->extends('admin.layouts.appLogged')->section('content');
    }

}
